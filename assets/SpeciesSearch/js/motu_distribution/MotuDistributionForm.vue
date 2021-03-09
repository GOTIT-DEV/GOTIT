<template>
  <form id="main-form" ref="form" action="#" @submit.prevent="submit">
    <legend>
      <h2>{{ $t("search") }}</h2>
    </legend>
    <div class="form-component col-xl-8 offset-xl-2">
      <b-card
        id="taxonomy-select"
        :header="$t('species')"
        header-class="font-weight-bold"
      >
        <TaxonomySelect
          ref="taxonomy"
          withTaxname
          @update:genus="taxonomy.genus = $event"
          @update:species="taxonomy.species = $event"
          @update:taxname="taxonomy.taxname = $event"
        />
      </b-card>

      <b-card
        id="motu-select"
        :header="$t('identification')"
        header-class="font-weight-bold"
      >
        <MotuDatasetSelect
          ref="motu"
          @update:dataset="motu.dataset = $event"
          @update:methods="motu.methods = $event"
        />
      </b-card>

      <ButtonLoading id="submit" ref="submit" :loading="loading">
        {{ $t("search") }}
      </ButtonLoading>
    </div>
  </form>
</template>

<i18n>
{
  "en": {
    "search": "Search",
    "species": "Query species",
    "identification": "Species identification"
  },
  "fr": {
    "search": "Rechercher",
    "species": "Recherche par espèce",
    "identification": "Méthode d'identification"
  }
}
</i18n>

<script>
// Components
import ButtonLoading from "../../../components/ButtonLoading";
import TogglablePanel from "../components/TogglablePanel";
import TaxonomySelect from "../components/taxonomy/TaxonomySelect";
import MotuDatasetSelect from "../components/motu-datasets/MotuDatasetSelect";

export default {
  components: {
    TogglablePanel,
    TaxonomySelect,
    MotuDatasetSelect,
    ButtonLoading,
  },
  async mounted() {
    await Promise.all([this.$refs.taxonomy.ready, this.$refs.motu.ready]);
    this.submit();
  },
  data() {
    return {
      loading: true,
      url: Routing.generate("distribution-query"),
      taxonomy: {
        genus: undefined,
        species: undefined,
        taxname: undefined,
      },
      motu: {
        dataset: undefined,
        methods: undefined,
      },
    };
  },
  methods: {
    log(ev) {
      console.log(ev);
    },
    async submit() {
      this.loading = true;
      const response = await fetch(this.url, {
        method: "POST",
        body: new FormData(this.$refs.form),
      });
      const json = await response.json();
      this.loading = false;
      this.$emit("update:results", json.rows);
      return json.rows;
    },
  },
};
</script>

<style lang="less" scoped>
.form-component {
  display: grid;
  grid-template-areas:
    "taxonomy motu"
    "submit submit";
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  align-content: stretch;

  .species-select {
    grid-area: taxonomy;
  }
  #motu-select {
    grid-area: motu;
  }
  #slot-container {
    grid-area: id-level;
  }

  #submit {
    grid-area: submit;
    margin: 0 auto;
    justify-self: center;
    min-width: 10em;
    width: fit-content;
    width: -moz-fit-content;
  }
}

@media (max-width: 1200px) {
  .form-component {
    grid-template-areas:
      "taxonomy motu"
      "submit submit";
  }
}

@media (max-width: 620px) {
  .form-component {
    grid-template-areas:
      "taxonomy"
      "motu"
      "submit";
    grid-template-columns: 1fr;
  }
}
</style>