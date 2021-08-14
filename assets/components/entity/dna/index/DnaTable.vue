<template>
  <div id="index-table-container">
    <entity-index-table
      ref="table"
      :table-id="tableId"
      :fields="fields"
      :routes="routes"
      :provider-pagination="providerPagination"
      export-filename="dna.csv"
      :short-item-repr="shortItemRepr"
    >
      <template #cell(specimen_fk)="data">
        <a :href="generateRoute('specimen_show', { id: data.item.id })">
          {{ data.value }}
        </a>
      </template>
      <template #cell(store_fk)="data">
        <a :href="generateRoute('store_show', { id: data.item.id })">
          {{ data.value }}
        </a>
      </template>
      <template #cell(pcrs)="data">
        <a :href="pcrLinkUrl(data.item)">
          <i :class="['fas', pcrLinkIcon(data.item.pcrs.length)]" />
          <span v-if="data.item.pcrs.length > 1">
            ({{ data.item.pcrs.length }})
          </span>
        </a>
      </template>
    </entity-index-table>
  </div>
</template>

<script>
import EntityIndexTable from "~Components/entity/EntityIndexTable.vue";
import moment from "moment";
export default {
  components: { EntityIndexTable },
  data() {
    return {
      tableId: "dna-list-table",
      routes: {
        show: "dna_show",
        edit: "dna_edit",
        list: "app_api_dna_list",
        delete: "app_api_dna_delete",
      },
      providerPagination: {
        items: "items",
        pagination: "pagination",
      },
      fields: [
        {
          key: "id",
          label: "ID",
          sortable: true,
          visible: false,
        },
        {
          key: "code",
          sortable: true,
          visible: true,
          searchable: true,
          searchActive: false,
        },
        {
          key: "specimen_fk",
          label: this.$t("messages.Specimen"),
          formatter(value) {
            return value?.molecular_code;
          },
          sortable: true,
          visible: true,
          searchable: true,
        },
        {
          key: "date",
          sortable: true,
          visible: true,
          export: {
            formatter(value) {
              return value;
            },
          },
          formatter(value, key, item) {
            const prec = item.date_precision_voc_fk.code;
            const format =
              prec === "J"
                ? "L"
                : prec === "M"
                ? "MMM YYYY"
                : prec === "A"
                ? "YYYY"
                : "L";
            return value ? moment(value).format(format) : value;
          },
        },
        {
          key: "dna_extractions",
          label: this.$t("messages.Producers"),
          sortable: false,
          visible: true,
          formatter(relations, key, item) {
            return relations
              .map((relation) => relation.person_fk.name)
              .join(", ");
          },
        },
        {
          key: "store_fk",
          label: this.$t("messages.Store fk"),
          formatter(value) {
            return value?.code;
          },
          sortable: true,
          visible: true,
          searchable: true,
        },
        {
          key: "pcrs",
          label: "PCRs",
          sortable: false,
          visible: true,
          class: "text-nowrap",
          export: {
            exclude: true,
          },
        },
      ],
    };
  },
  computed: {
    newPcrUrl() {
      return this.generateRoute("pcr_new");
    },
  },
  methods: {
    shortItemRepr(item) {
      return `(#${item.id}) ${item.code}`;
    },
    pcrLinkIcon(pcrCount) {
      return pcrCount === 0
        ? "fa-plus"
        : pcrCount === 1
        ? "fa-link"
        : "fa-list";
    },
    pcrLinkUrl(item) {
      return item.pcrs.length === 0
        ? this.newPcrUrl
        : this.generateRoute("pcr_index", { dna: item.id });
    },
  },
};
</script>

<style lang="less" scoped></style>
