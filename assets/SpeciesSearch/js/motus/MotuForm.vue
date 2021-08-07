<template>
  <form id="main-form" ref="form" action="#" @submit.prevent="submit">
    <fieldset>
      <legend>
        <h2>{{ $t("ui.search") }}</h2>
      </legend>
      <div class="form-content">
        <togglable-panel
          class="species-select"
          title="queries.label.search.espece"
        >
          <taxonomy-select ref="taxonomy" />
        </togglable-panel>
        <b-card
          :header="$t('queries.identification.label')"
          header-class="font-weight-bold"
          class="motu-select"
        >
          <motu-dataset-select ref="motu" multiple />
        </b-card>
        <b-card
          :header="$t('id_criteria')"
          header-class="font-weight-bold"
          class="criteria-select"
        >
          <identification-criteria ref="criteria" />
        </b-card>

        <button-loading id="submit" ref="submit" :loading="loading">
          {{ $t("ui.search") }}
        </button-loading>
      </div>
    </fieldset>
  </form>
</template>

<i18n>
{
  "en": {
    "id_criteria": "Identification criteria"
  },
  "fr": {
    "id_criteria": "Critères d'identification"
  }
}
</i18n>

<script>
// Components
import ButtonLoading from "~Components/ButtonLoading";
import TogglablePanel from "../components/TogglablePanel";
import TaxonomySelect from "../components/taxonomy/TaxonomySelect";
import MotuDatasetSelect from "../components/motu-datasets/MotuDatasetSelect";
import IdentificationCriteria from "./IdentificationCriteria.vue";
import i18n from "../i18n";

export default {
  i18n,
  components: {
    TogglablePanel,
    TaxonomySelect,
    MotuDatasetSelect,
    ButtonLoading,
    IdentificationCriteria,
  },
  data() {
    return {
      loading: true,
      url: Routing.generate("motu-query"),
    };
  },
  computed: {},
  async mounted() {
    await this.init();
    this.submit();
  },
  methods: {
    async init() {
      return Promise.all([
        this.$refs.taxonomy.init(),
        this.$refs.motu.init(),
        this.$refs.criteria.init(),
      ]);
    },
    async submit() {
      this.loading = true;
      const response = await fetch(this.url, {
        method: "POST",
        body: new FormData(this.$refs.form),
      });
      const data = await response.json();
      this.loading = false;
      this.$emit("update:results", data.rows);
      this.$emit("update:query", data.query);
      return data.rows;
    },
  },
};
</script>

<style lang="less" scoped>
.form-content {
  display: grid;
  grid-template-areas:
    "taxonomy motu id-level"
    ". submit .";
  grid-template-columns: 1fr 1fr 1fr;
  gap: 10px;
  align-content: stretch;

  .species-select {
    grid-area: taxonomy;
  }
  .motu-select {
    grid-area: motu;
  }
  .criteria-select {
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
  .form-content {
    grid-template-areas:
      "taxonomy motu"
      "id-level id-level"
      "submit submit";
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 620px) {
  .form-content {
    grid-template-areas:
      "taxonomy"
      "motu"
      "id-level"
      "submit";
    grid-template-columns: 1fr;
  }
}
</style>
