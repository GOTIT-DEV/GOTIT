<template>
  <div class="form-component">
    <!-- <slot> </slot> -->
    <div id="taxonomy-select" class="panel panel-default">
      <div class="panel-heading">
        <strong>
          {{ taxoPanelLabel }}
        </strong>
      </div>
      <div class="panel-body">
        <TaxonomySelect ref="taxonomy" withTaxname> </TaxonomySelect>
      </div>
    </div>
    <div id="motu-select" class="panel panel-default">
      <div class="panel-heading">
        <strong>
          {{ motuPanelLabel }}
        </strong>
      </div>
      <div class="panel-body">
        <MotuDatasetSelect ref="motu" />
      </div>
    </div>
    <ButtonLoading
      id="submit"
      ref="submit"
      v-bind:loading="loading"
      @click="submit"
    >
      Search
    </ButtonLoading>
  </div>
</template>

<script>
// Components
import ButtonLoading from "../components/ButtonLoading";
import TogglablePanel from "../components/TogglablePanel";
import TaxonomySelect from "../components/taxonomy/TaxonomySelect";
import MotuDatasetSelect from "../components/motu-datasets/MotuDatasetSelect";

export default {
  components: {
    TogglablePanel,
    TaxonomySelect,
    MotuDatasetSelect,
    ButtonLoading
  },
  computed: {
    ready() {
      return Promise.all([this.$refs.taxonomy.ready, this.$refs.motu.ready]);
    }
  },
  data() {
    return {
      loading: true,
      motuPanelLabel: Translator.trans("identification.label"),
      taxoPanelLabel: Translator.trans("label.search.espece")
    };
  },
  methods: {
    submit() {
      this.loading = true;
    }
  }
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