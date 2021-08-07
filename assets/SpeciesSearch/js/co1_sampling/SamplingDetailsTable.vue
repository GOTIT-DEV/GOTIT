<template>
  <b-data-table
    id="sampling-details-table"
    :items="items"
    :fields="fields"
    :export-filename="exportFilename"
    responsive
    hover
    striped
  >
    <!-- Add some legend as extra header -->
    <template #thead-top>
      <b-tr>
        <b-th colspan="9" class="text-right">
          <span class="text-muted mx-2">
            <i class="fas fa-dna" />
            {{ $t("sequence") }}
          </span>
          <span class="text-muted mx-2">
            <i class="fas fa-vial" />
            {{ $t("biomat") }}
          </span>
          <span class="text-primary mx-2">
            <i class="fas fa-circle fa-xs" />
            {{ $t("internal") }}
          </span>
          <span class="text-success mx-2">
            <i class="fas fa-circle fa-xs" />
            {{ $t("external") }}
          </span>
          <span class="text-muted mx-2">
            <i class="fas fa-circle fa-xs no-sample" />
            {{ $t("no_sample") }}
          </span>
        </b-th>
      </b-tr>
    </template>

    <template #cell(site_code)="data">
      <a :href="generateRoute('station_show', { id: data.item.site_id })">
        {{ data.value }}
      </a>
    </template>
    <template #cell(has_co1)="data">
      <i
        class="fas fa-dna"
        :class="data.value > 0 ? 'text-primary' : 'no-sample'"
      />
    </template>
    <template #head(int_biomat)>
      <i class="fas fa-vial text-primary" />
    </template>
    <template #cell(int_biomat)="data">
      <i
        class="fas fa-vial"
        :class="data.value > 0 ? 'text-primary' : 'no-sample'"
      />
    </template>
    <template #head(ext_biomat)>
      <i class="fas fa-vial text-success" />
    </template>
    <template #cell(ext_biomat)="data">
      <i
        class="fas fa-vial"
        :class="data.value > 0 ? 'text-success' : 'no-sample'"
      />
    </template>
  </b-data-table>
</template>

<i18n>
{
  "en": {
    "site": "Site",
    "municipality": "Municipality",
    "country": "Country",
    "sequence" : "Sequence",
    "biomat" : "Biomaterial",
    "internal" : "Interne",
    "external" : "Externe",
    "no_sample" : "No sample"
  },
  "fr": {
    "site": "Station",
    "municipality": "Commune",
    "country": "Pays",
    "sequence" : "Séquence",
    "biomat" : "Lot matériel",
    "internal" : "Interne",
    "external" : "Externe",
    "no_sample" : "Pas d'échantillon"
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
    taxname: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      fields: [
        {
          key: "site_code",
          label: this.$t("site"),
          visible: true,
          sortable: true,
        },
        {
          key: "latitude",
          visible: true,
          sortable: true,
        },
        {
          key: "longitude",
          visible: true,
          sortable: true,
        },
        {
          key: "altitude",
          visible: true,
          sortable: true,
        },
        {
          key: "municipality",
          label: this.$t("municipality"),
          visible: true,
          sortable: true,
        },
        {
          key: "country",
          label: this.$t("country"),
          visible: true,
          sortable: true,
        },
        {
          key: "has_co1",
          label: "CO1",
          visible: true,
          sortable: true,
          class: "text-center",
        },
        {
          key: "int_biomat",
          visible: true,
          sortable: true,
          // class: "text-center",
        },
        {
          key: "ext_biomat",
          visible: true,
          sortable: true,
          // class: "text-center",
        },
      ],
    };
  },
  computed: {
    exportFilename() {
      return `${this.taxname}_sampling_sites.csv`;
    },
  },
};
</script>

<style lang="less">
#sampling-details-table .no-sample {
  color: lightgrey;
}
</style>
