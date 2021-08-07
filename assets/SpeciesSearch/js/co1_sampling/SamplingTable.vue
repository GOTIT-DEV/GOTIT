<template>
  <b-data-table
    ref="table"
    :items="items"
    :fields="fields"
    :searchbar-placeholder="$t('search_taxon')"
    export-filename="sampling_summary.csv"
    export-columns-by-key
    hover
    responsive
    striped
  >
    <template #thead-top>
      <b-tr>
        <b-th colspan="1" class="border-0" />
        <b-th colspan="3" class="text-center border-left border-bottom-0">
          {{ $t("samples.specimens") }}
        </b-th>
        <b-th
          colspan="3"
          class="text-center border-left border-right border-bottom-0"
        >
          {{ $t("samples.co1") }}
        </b-th>
      </b-tr>
    </template>

    <template #cell(id)="data">
      <b-radio
        v-model="selected"
        name="selected-row"
        :value="data.item"
        @change="$emit('update:selection', $event)"
      />
    </template>
  </b-data-table>
</template>

<i18n>
{
  "en": {
    "search_taxon" : "Search for taxa",
    "samples": {
      "specimens": "Specimen samples",
      "co1": "CO1 samples"
    },
    "taxon": "Taxon",
    "sites": "Sites",
    "lmp": "LMP (Deg)",
    "mle": "MLE (km)",
    "details": "Details"
  },
  "fr": {
    "search_taxon" : "Rechercher des taxons",
    "samples": {
      "specimens": "Échantillons individus",
      "co1": "Échantillons CO1"
    },
    "taxon": "Taxon",
    "sites": "Stations",
    "lmp": "LMP (Deg)",
    "mle": "MLE (km)",
    "details": "Détails"
  }
}
</i18n>

<script>
import BDataTable from "~Components/BDataTable";
export default {
  components: { BDataTable },
  props: {
    items: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      selected: undefined,
      fields: [
        {
          key: "taxon_name",
          label: this.$t("taxon"),
          sortable: true,
          visible: true,
        },
        {
          key: "nb_sta",
          label: this.$t("sites"),
          sortable: true,
          visible: true,
          class: " text-center border-left font-weight-bold",
        },
        {
          key: "lmp",
          label: this.$t("lmp"),
          formatter: (value) => (value ? value.toFixed(3) : null),
          sortable: true,
          visible: true,
        },
        {
          key: "mle",
          label: this.$t("mle"),
          sortable: true,
          visible: true,
          formatter: (value) => (value ? value.toFixed(3) : null),
        },
        {
          key: "nb_sta_co1",
          label: this.$t("sites"),
          sortable: true,
          visible: true,
          class: "text-center border-left font-weight-bold",
        },
        {
          key: "lmp_co1",
          label: this.$t("lmp"),
          formatter: (value) => (value ? value.toFixed(3) : null),
          sortable: true,
          visible: true,
        },
        {
          key: "mle_co1",
          label: this.$t("mle"),
          sortable: true,
          visible: true,
          formatter: (value) => (value ? value.toFixed(3) : null),
        },
        {
          key: "id",
          label: this.$t("details"),
          sortable: false,
          visible: true,
          class: "py-0 px-1 align-middle text-center border-left",
        },
      ],
    };
  },
  methods: {},
};
</script>

<style lang="less" scoped></style>
