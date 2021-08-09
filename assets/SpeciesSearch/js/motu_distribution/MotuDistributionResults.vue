<template>
  <div class="distribution-results">
    <h2>{{ $t("results") }}</h2>
    <b-tabs
      justified
      active-nav-item-class="font-weight-bold text-primary"
      content-class="mt-3"
    >
      <b-tab active title-link-class="result-tab" @click="scrollToMap">
        <template #title>
          <i class="fas fa-map-marker" />
          {{ $t("map") }}
        </template>
        <motu-distribution-map ref="map" :data="results" />
      </b-tab>

      <b-tab title-link-class="result-tab">
        <template #title>
          <i class="fas fa-th-list" />
          {{ $t("table") }}
        </template>

        <b-data-table ref="table" :items="results" :fields="fields">
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
                    ? 'external_sequence_show'
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

          <template #cell(site_code)="data">
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
    "results": "Results",
    "map": "MOTU map",
    "table": "Distribution data",
    "external": "external",
    "internal": "internal"
  },
  "fr": {
    "results": "Résultats",
    "external": "externe",
    "internal": "interne",
    "map": "Carte des MOTUs",
    "table": "Données de distribution"
  }
}
</i18n>

<script>
import MotuDistributionMap from "./MotuMap";
import BDataTable from "~Components/BDataTable";
import i18n from "~SpeciesSearch/js/i18n";
import Vue from "vue";

export default {
  i18n,
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
          key: "site_code",
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
    scrollToMap() {
      Vue.nextTick(() =>
        this.$refs.map.$el.scrollIntoView({
          behavior: "smooth",
          block: "center",
          inline: "center",
        })
      );
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
