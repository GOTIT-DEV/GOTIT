<template>
  <div class="site-info">
    <label>
      <a :href="url">
        <b>{{ site?.site_code }}</b>
      </a>
    </label>
    <b-table-lite
      small
      stacked
      dark
      tbody-tr-class="site-table"
      table-class="mb-0"
      :items="site ? [site] : []"
      :fields="fields"
    >
      <template #cell(motu)="data">
        <b-badge variant="info">
          {{ data.value }}
        </b-badge>
      </template>
    </b-table-lite>
    <span class="site-location text-capitalize">
      {{ site?.municipality.toLowerCase() }}
    </span>
    <span class="site-location">{{ site?.country }}</span>
  </div>
</template>

<script>
export default {
  props: {
    site: { type: Object, default: () => ({}) },
    extraFields: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  data() {
    return {
      baseFields: [
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
          formatter: (value) => (value === null ? "N/A" : `${value} m`),
        },
      ],
    };
  },
  computed: {
    fields() {
      return [...this.extraFields, ...this.baseFields];
    },
    url() {
      return this.site
        ? Routing.generate("station_show", {
            id: this.site.site_id,
            _locale: Translator.locale,
          })
        : undefined;
    },
  },
};
</script>

<style lang="less">
.site-info {
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
