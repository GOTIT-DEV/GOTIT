<template>
  <div class="form-component col-xl-8 offset-xl-2">

    <b-card
      id="taxonomy-select"
      :header="$t('queries.label.search.espece')"
      header-class="font-weight-bold"
    >
      <TaxonomySelect ref="taxonomy" withTaxname> </TaxonomySelect>
    </b-card>

    <b-card
      id="motu-select"
      :header="$t('queries.identification.label')"
      header-class="font-weight-bold"
    >
      <MotuDatasetSelect ref="motu" />
    </b-card>

    <ButtonLoading id="submit" ref="submit" :loading="loading" @click="submit">
      {{ $t("ui.search") }}
    </ButtonLoading>
    
  </div>
</template>

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
  computed: {
    ready() {
      return Promise.all([this.$refs.taxonomy.ready, this.$refs.motu.ready]);
    },
  },
  data() {
    return {
      loading: true,
    };
  },
  methods: {
    submit() {
      this.loading = true;
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