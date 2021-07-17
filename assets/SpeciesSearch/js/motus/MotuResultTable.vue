<template>
  <b-data-table ref="table" v-bind="$props" :fields="fields">
    <template #cell(taxon)="data">
      <a
        :href="
          generateRoute('referentieltaxon_show', {
            id: data.item.id,
          })
        "
      >
        {{ data.value }}
      </a>
    </template>

    <template #cell(details)="data">
      <b-button
        variant="light"
        size="sm"
        class="border"
        @click="$emit('show-details', data.item)"
      >
        <i class="fas fa-th-list"></i>
      </b-button>
    </template>
  </b-data-table>
</template>

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
      fields: [
        {
          key: "taxon",
          label: this.$t("queries.columns.taxon"),
          sortable: true,
          visible: true,
        },
        {
          key: "method",
          label: this.$t("queries.columns.method"),
          sortable: true,
          visible: true,
        },
        {
          key: "dataset",
          label: this.$t("queries.columns.dataset"),
          sortable: true,
          visible: true,
        },
        {
          key: "count_seq",
          label: this.$t("queries.columns.seqs"),
          sortable: true,
          visible: true,
        },
        {
          key: "count_motus",
          label: this.$t("queries.columns.motus"),
          sortable: true,
          visible: true,
        },
        {
          key: "details",
          label: this.$t("queries.columns.details"),
          sortable: true,
          visible: true,
        },
      ],
    };
  },
};
</script>

<style lang="less" scoped>
</style>