<template>
  <div class="distribution-results">
    <h2 id="results-title">
      {{ $t("queries.label.resultats") }}
    </h2>
    <b-tabs
      justified
      active-nav-item-class="font-weight-bold text-primary"
      content-class="mt-3"
    >
      <b-tab active title-link-class="result-tab">
        <template #title>
          <i class="fas fa-map-marker"></i>
          {{ $t("map") }}
        </template>
        <motu-distribution-map
          ref="map"
          :data="results"
        ></motu-distribution-map>
      </b-tab>

      <b-tab title-link-class="result-tab">
        <template #title>
          <i class="fas fa-th-list"></i>
          {{ $t("table") }}
        </template>

        <b-data-table :items="results" :fields="fields">
          <template #cell(taxon_name)="data">
            <a
              :href="
                generateRoute('referentieltaxon_show', {
                  id: data.item.taxon_id,
                })
              "
            >
              {{ data.value }}
            </a>
          </template>

          <template #cell(code)="data">
            <a
              :id="`seq-code-${data.index}`"
              :href="
                generateRoute(
                  data.item.seq_type
                    ? 'sequenceassembleeext_show'
                    : 'sequenceassemblee_show',
                  {
                    id: data.item.id,
                  }
                )
              "
            >
              {{ data.value }}
            </a>
            <b-tooltip :target="`seq-code-${data.index}`" triggers="hover">
              {{ data.value }}
            </b-tooltip>
          </template>

          <template #cell(accession_number)="data">
            <a :href="`https://www.ncbi.nlm.nih.gov/nuccore/${data.value}`">
              {{ data.value }}
            </a>
          </template>

          <template #cell(station_code)="data">
            <a :href="generateRoute('station_show', { id: data.item.id })">
              {{ data.value }}
            </a>
          </template>
        </b-data-table>
      </b-tab>
    </b-tabs>
  </div>
</template>

<i18n>
{
  "en": {
    "map": "MOTU map",
    "table": "Distribution data",
    "external": "external",
    "internal": "internal"
  },
  "fr": {
    "external": "externe",
    "internal": "interne",
    "map": "Carte des MOTUs",
    "table": "Données de distribution"
  }
}
</i18n>

<script>
import MotuDistributionMap from "./MotuMap";
import Multiselect from "vue-multiselect";
import BDataTable from "../../../components/BDataTable";

export default {
  components: {
    MotuDistributionMap,
    BDataTable,
  },
  props: {
    results: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      fields: [
        {
          key: "taxon_name",
          label: this.$t("queries.columns.taxon"),
          sortable: true,
          visible: true,
        },
        {
          key: "code",
          label: this.$t("queries.columns.seq"),
          sortable: true,
          class: "column",
          visible: true,
        },
        {
          key: "seq_type",
          label: this.$t("queries.columns.type"),
          visible: true,
          filterByFormatted: true,
          class: "text-capitalize",
          formatter: (isExternal, key, index) => {
            return this.$t(isExternal ? "external" : "internal");
            // return type.charAt(0).toUpperCase() + type.slice(1);
          },
        },
        {
          key: "accession_number",
          label: this.$t("queries.columns.accession"),
          sortable: true,
          visible: true,
        },
        {
          key: "motu",
          label: this.$t("queries.columns.motu"),
          sortable: true,
          visible: true,
        },
        {
          key: "latitude",
          label: this.$t("queries.columns.lat"),
          sortable: true,
          visible: true,
        },
        {
          key: "longitude",
          label: this.$t("queries.columns.long"),
          sortable: true,
          visible: true,
        },
        {
          key: "station_code",
          label: this.$t("queries.columns.station"),
          sortable: true,
          visible: true,
        },
        {
          key: "municipality",
          label: this.$t("queries.columns.municipality"),
          sortable: true,
          visible: true,
        },
        {
          key: "country",
          label: this.$t("queries.columns.country"),
          sortable: true,
          visible: true,
        },
      ],
    };
  },
  methods: {
    generateRoute(route_name, args) {
      return Routing.generate(route_name, args);
    },
  },
};
</script>

<style lang="less">
.distribution-results {
  .result-tab {
    font-size: 12pt;
  }
}
</style>