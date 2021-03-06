<template>
  <div>
    <b-form @submit.prevent="submit">
      <b-row align-h="center">
        <b-col xl="8">
          <b-card :title="$t('constraints')">
            <b-row>
              <b-col v-for="(selection, label) in formState" :key="label">
                <label :for="`${label}-select`">{{ $t(label) }}</label>
                <multiselect
                  :id="`${label}-select`"
                  :options="
                    options.filter(
                      (opt) =>
                        !(opt.disabledFor && opt.disabledFor.includes(label))
                    )
                  "
                  v-model="formState[label]"
                  :showLabels="false"
                  label="label"
                  :allowEmpty="false"
                >
                  <template slot="singleLabel" slot-scope="props">
                    Taxon :
                    <b-badge
                      v-if="props.option.variant"
                      :variant="props.option.variant"
                    >
                      {{ props.option.label }}
                    </b-badge>
                    <span v-else> {{ $t(props.option.label) }}</span>
                  </template>
                  <template slot="option" slot-scope="props">
                    Taxon :
                    <b-badge
                      v-if="props.option.variant"
                      :variant="props.option.variant"
                    >
                      {{ props.option.label }}
                    </b-badge>
                    <span v-else> {{ $t(props.option.label) }}</span>
                  </template>
                </multiselect>
                <!-- </b-form-group> -->
              </b-col>
            </b-row>
          </b-card>
          <div class="d-flex justify-content-center mt-3">
            <button-loading :loading="loading"> Rechercher </button-loading>
          </div>
        </b-col>
      </b-row>
    </b-form>

    <div id="results" v-if="displayResults">
      <h2>Résultats</h2>

      <b-data-table
        :items="results"
        :fields="fields"
        :busy="loading"
        fixed
        stacked="md"
        exportFilename="taxon_attributions.csv"
      >
        <template #cell(biomaterial)="data">
          <assignment-cell :item="data.value" entity="biomaterial" />
        </template>
        <template #cell(specimen)="data">
          <assignment-cell :item="data.value" entity="specimen" />
        </template>
        <template #cell(sequence)="data">
          <assignment-cell :item="data.value" entity="sequence" />
        </template>
      </b-data-table>
    </div>
  </div>
</template>

<i18n>
{
  "en": {
    "constraints": "Constraints",
    "any" : "any",
    "undefined": "undefined",
    "biomaterial": "Material lot",
    "specimen": "Specimen",
    "sequence": "Sequence"
  },
  "fr": {
    "constraints": "Contraintes",
    "any" : "tous",
    "undefined": "indéfini",
    "biomaterial": "Lot matériel",
    "specimen": "Individu",
    "sequence": "Séquence"
  }
}
</i18n>

<script>
import BDataTable from "../../../components/BDataTable";
import Multiselect from "vue-multiselect";
import ButtonLoading from "../../../components/ButtonLoading";
import AssignmentCell from "./AssignmentCell";

export default {
  components: { BDataTable, Multiselect, ButtonLoading, AssignmentCell },
  data() {
    return {
      displayResults: false,
      loading: false,
      formState: {},
      options: [
        { value: "A", label: "A", variant: "info" },
        { value: "B", label: "B", variant: "success" },
        { value: "C", label: "C", variant: "danger" },
        { value: "0", label: "any" },
        {
          value: "1",
          label: "undefined",
          disabledFor: ["biomaterial", "specimen"],
        },
      ],
      results: [],
      fields: [
        {
          key: "biomaterial",
          label: this.$t("biomaterial"),
          sortable: true,
          sortKey: "taxname",
          unpacker: (biomat) => ({
            biomat_id: biomat.id,
            biomat_code: biomat.code,
            biomat_taxon: biomat.taxname,
            biomat_criterion: biomat.criterion.code,
          }),
        },
        {
          key: "specimen",
          label: this.$t("specimen"),
          sortable: true,
          sortKey: "taxname",
          unpacker: (specimen) => ({
            specimen_id: specimen.id,
            specimen_code_morph: specimen.code.morpho,
            specimen_code_mol: specimen.code.biomol,
            specimen_taxon: specimen.taxname,
            specimen_criterion: specimen.criterion.code,
          }),
        },
        {
          key: "sequence",
          label: this.$t("sequence"),
          sortable: true,
          sortKey: "taxname",
          unpacker: (sequence) => ({
            seq_id: sequence.id,
            seq_code: sequence.code,
            seq_taxon: sequence.taxname,
            seq_criterion: sequence.criterion.code,
          }),
        },
      ],
    };
  },
  created() {
    this.formState = {
      biomaterial: this.options[0],
      specimen: this.options[1],
      sequence: this.options[2],
    };
  },
  methods: {
    async submit() {
      this.loading = true;
      const query = Object.keys(this.formState).reduce((acc, key) => {
        acc[key] = this.formState[key].value;
        return acc;
      }, {});
      const json = JSON.stringify(query);
      console.log(json);

      const response = await fetch(Routing.generate("consistency-query"), {
        method: "POST",
        body: json,
      });
      this.results = await response.json();
      this.displayResults = true;
      this.loading = false;
    },
  },
};
</script>

<style lang="less">
.multiselect.select-A {
  * {
    color: white;
  }
  .multiselect__select::before {
    border-top-color: white;
  }
  > *,
  .multiselect__single {
    background: #17a2b8;
  }
}
</style>