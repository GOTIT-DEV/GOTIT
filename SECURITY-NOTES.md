# Notes de sécurité — dépendances JavaScript

Référentiel des avis de sécurité traités sur la chaîne de compilation front (Yarn 1 / Webpack Encore).

- **Branche :** `Gotit3.0@051026`
- **Date du lot 1 :** 2026-10-05
- **Environnement :** Node v20.12.2, Yarn 1.22.22, Symfony 5.4 / PHP 8.1

## Méthode de diagnostic

`yarn audit` **fonctionne** sur ce projet (code de sortie `30` quand des avis sont présents) et
expose pour chacun le chemin exact de dépendance, la sévérité et la version corrigée. Il reste
la référence, à condition de lire sa sortie.

La distinction déterminante est **build / runtime**, et non le nombre brut d'avis. `yarn audit`
signale tout ce qui est présent dans l'arbre, y compris les outils de compilation qui ne sont
jamais livrés au navigateur.

> Le drapeau `dev` de `yarn audit` n'est pas fiable sur cette arborescence : il attribue
> `dev: false` à des paquets pourtant accessibles uniquement par des `devDependencies`. Le
> classement retenu ici est calculé en remontant au premier segment du chemin et en le
> comparant aux sections `dependencies` / `devDependencies` du `package.json`.

## Résultats

| Mesure | Avant | Après |
| --- | --- | --- |
| Advisories runtime (livrées au navigateur) | 20 | **7** |
| Advisories build (poste de travail et CI) | 311 | 267 |
| Total | 331 | 274 |
| dont `high` | 183 | 154 |

### Fermés par le lot 1

| Paquet | Avant | Après | Avis closed |
| --- | --- | --- | --- |
| `plotly.js` | 1.58.5 | 2.35.3 | 7, dont **CVE-2023-46308** (prototype pollution, `critical`) |
| `lodash` | 4.17.21 | 4.18.1 | 3 `high` |
| `moment` | 2.29.2 | 2.31.0 | 1 `high` + 1 `moderate` |
| `js-cookie` | 2.2.1 | 3.0.8 | 1 `high` |
| `js-yaml` | 3.14.1 (×4) + 4.1.0 | 3.15.2 (unique) | 5 `moderate` |

`sql-formatter` n'a pas été mis à jour : son API v2 est incompatible avec les versions suivantes
(export par défaut devenu nommé, option `language` devenue obligatoire, casse des mots-clés SQL
par défaut modifiée). La mise à jour de `lodash` suffit à fermer ses 3 avis sans toucher au
paquet.

## Risques assumés

Les 7 advisories runtime restantes. Aucun code vulnérable n'est atteignable dans l'usage réel
de l'application.

### 1. `vue` 2.6.14 — `low`, ReDoS (`GHSA-5j4c-8p2g-v4jx`)

Corrigé uniquement à partir de Vue 3.0.0. La migration entraînerait `bootstrap-vue`, `vue-i18n 8`,
`vuex 3`, `vue2-leaflet` et `vue-multiselect` : refonte de la couche front, hors périmètre d'une
mise à jour de sécurité. **Sujet à réexaminer lors d'une migration Vue 3.**

### 2. `vue-json-csv` → `lodash.pick` — `high` (`GHSA-p6mc-m468-83gw`)

**Aucun correctif n'existe** (`patched_versions: <0.0.0`) et `vue-json-csv` 2.1.0 est déjà la
dernière version publiée. À remplacer si l'export CSV devient sensible.

### 3. Chaîne `vue-plotly` → `plotly.js` — 5 advisories

| Paquet | Sévérité | Chemin |
| --- | --- | --- |
| `maplibre-gl` | **critical** | `GHSA-jrc7-96c5-q579`, contournement XSS de `DOM.sanitize()`, corrigé en ≥ 6.4.1 |
| `protocol-buffers-schema` | moderate ×2 | via `@plotly/mapbox-gl` → `pbf` |
| `word-wrap` | moderate ×2 | via `regl-scatter2d` → `glslify` → `static-eval` |

**Pourquoi l'exposition est nulle.** Ces avis se situent dans les traces *cartographiques*
(`maplibre-gl`, `@plotly/mapbox-gl`, `pbf`) et les traces *WebGL / 3D* (`regl-scatter2d`,
`regl-splom`, `glslify`) :

- `assets/SpeciesSearch/js/species_hypotheses/BarPlot.vue:101` déclare `type: "bar"` — le seul
  graphique de l'application ;
- **aucun fichier** du dépôt n'utilise `scattermapbox`, `scattermap`, `scattergeo`, `scattergl`
  ou `gl3d` ;
- la carte de l'application est **Leaflet** (`assets/components/maps/LeafletMap.vue`), sans lien
  avec plotly.js.

Le code vulnérable est donc présent dans l'arbre mais jamais exécuté.

**Piste de fermeture.** `plotly.js` 4.x embarque `maplibre-gl` 6.9.0 (≥ 6.4.1) et supprime
`@plotly/mapbox-gl`, ce qui fermerait probablement les 5. Mais plotly.js 4.x impose
`node >= 22.0.0` alors que l'environnement est en Node 20.12.2. Cela suppose une montée de Node,
qui touche toute la chaîne de build et mérite un chantier distinct.

## Différé

Les **267 advisories build** (`loader-utils`, `webpack`, `@babel/traverse`, `websocket-driver`,
`brace-expansion`, `minimatch`, `postcss`…) concernent le poste de travail et la CI, pas les
utilisateurs. Leur remédiation est prévue au **lot 2**, en commit séparé, via la mise à jour de
`@symfony/webpack-encore` et `vue-loader` sans changement de majeur des loaders.

## Contraintes de compilation

`resolutions` est utilisé dans `package.json` :

| Entrée | Raison |
| --- | --- |
| `plotly.js: ^2.35.3` | `vue-plotly@1.1.0` déclare `plotly.js ^1.52.1`, plage incapable d'atteindre la version corrigée. La résolution impose 2.35.3. |
| `lodash: ^4.18.1` | `lodash` n'est pas déclaré en dépendance ; il est hissé via `sql-formatter`. La résolution force 4.18.1. |
| `@mapbox/jsonlint-lines-primitives: 2.0.2` | La 2.0.3 exige `node >= 22` et fait échouer l'installation. La 2.0.2 accepte `node >= 0.6`. Épinglage volontaire plutôt que `--ignore-engines`. |
| `**/js-yaml: ^3.15.2` | Les 4 consommateurs (`eslint`, `@eslint/eslintrc`, `js-yaml-loader`, `@intlify/vue-i18n-loader`) déclarent tous `^3.13.1`. La dépendance directe `js-yaml: ^4.0.0` était incohérente et n'était importée nulle part ; elle est alignée sur la branche 3.x. |

## Vérification

```bash
yarn install
yarn build          # doit aboutir : « webpack compiled successfully »
yarn audit --json   # code 0 attendu à la fermeture totale
```

Contrôle HTTP attendu : `/fr/`, `/fr/login`, `/fr/station` en `200`.
