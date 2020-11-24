<template>
  <l-popup ref="popup" class="site-popup">
    <label>
      <a :href="site.station_url">
        <b>{{ site.station_code }}</b>
      </a>
    </label>
    <b-table
      small
      stacked
      dark
      responsive
      tbody-tr-class="site-table"
      table-class="mb-0"
      :items="[site]"
      :fields="fields"
    >
      <template #cell(motu)="data">
        <b-badge variant="info">
          {{ data.value }}
        </b-badge>
      </template>
    </b-table>
    <span class="site-location text-capitalize">
      {{ site.municipality.toLowerCase() }}
    </span>
    <span class="site-location">{{ site.country }}</span>
    <b-button-group class="mt-3">
      <b-button
        size="sm"
        variant="dark"
        :title="$t('show_seqs')"
        @click="$emit('show-seq-modal')"
      >
        <i class="fas fa-dna"></i> {{ site.sequences.length }}
      </b-button>

      <b-button
        size="sm"
        variant="dark"
        :title="$t('filter_motu')"
        @click="$emit('filter-display', site.motu)"
      >
        <i class="fas fa-eye"></i>
      </b-button>
      <b-button
        size="sm"
        variant="dark"
        :title="$t('fit_motu')"
        @click="fitMotu(site.motu)"
      >
        <i class="fas fa-crosshairs"></i>
      </b-button>
    </b-button-group>
  </l-popup>
</template>

<i18n>
{
  "fr" : {
    "show_seqs": "Lister séquences",
    "filter_motu" : "Filtrer MOTU",
    "fit_motu" : "Cadrer MOTU"
  }, 
  "en" : {
    "show_seqs": "Show sequences",
    "filter_motu" : "Filter MOTU",
    "fit_motu" : "Fit view to MOTU"
  }
}
</i18n>

<script>
import { LPopup } from "vue2-leaflet";
export default {
  components: { LPopup },
  props: {
    site: { type: Object },
  },
  data() {
    return {
      fields: [
        {
          key: "motu",
          label: "MOTU",
        },
        {
          key: "latitude",
          label: "Lat.",
        },
        {
          key: "longitude",
          label: "Lon.",
        },
        {
          key: "altitude",
          label: "Alt",
          formatter: (value) => `${value} m`,
        },
      ],
    };
  },
  methods: {
    fitMotu(motu) {
      this.$refs.popup.mapObject.closePopup();
      console.log(this.$refs.popup.mapObject);
      this.$emit("fit-motu", motu);
    },
  },
};
</script>

<style lang="less" >
.site-popup {
  display: flex;
  flex-direction: column;
  min-width: 10rem;
  table.table-dark {
    background-color: transparent;
    tbody > tr.site-table > td {
      border-color: grey;
    }
  }
  .site-location {
    align-self: flex-end;
  }
}
</style>