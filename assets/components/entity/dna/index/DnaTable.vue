<template>
  <div id="index-table-container">
    <entity-index-table
      ref="table"
      :table-id="tableId"
      :fields="fields"
      :items="items"
      :routes="routes"
      :provider-pagination="providerPagination"
      export-filename="dna.csv"
      :short-item-repr="shortItemRepr"
      @delete:item="$emit('delete:item', $event)"
    >
      <template #cell(specimen)="data">
        <a :href="generateRoute('specimen_show', { id: data.item.id })">
          {{ data.value }}
        </a>
      </template>
      <template #cell(store)="data">
        <a :href="generateRoute('store_show', { id: data.item.id })">
          {{ data.value }}
        </a>
      </template>
      <template #cell(producers)="data">
        <b-avatar-group size="sm">
          <b-avatar
            v-for="person in data.value"
            :key="person.name"
            v-b-tooltip.hover
            :title="person.name"
            variant="info"
            :href="generateRoute('person_show', { id: person.id })"
            :text="
              person.name
                .split(' ')
                .map((part) => part.charAt(0))
                .join('')
            "
          />
        </b-avatar-group>
      </template>
      <template #cell(pcrs)="data">
        <a :href="pcrLinkUrl(data.item)">
          <i :class="['fas', pcrLinkIcon(data.item)]" />
          <span v-if="pcrCount(data.item) > 1">
            ({{ pcrCount(data.item) }})
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
  props: {
    items: {
      type: Array,
      default: null,
    },
  },
  data() {
    return {
      tableId: "dna-list-table",
      routes: {
        show: "api_dnas_get_item",
        edit: "dna_edit",
        list: "api_dnas_get_collection",
        delete: "api_dnas_delete_item",
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
          key: "specimen",
          label: this.$t("messages.Specimen"),
          formatter(value) {
            return value?.molecularCode;
          },
          sortable: true,
          visible: true,
          searchable: true,
          searchKey: "molecularCode",
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
            const prec = item.datePrecision.code;
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
          key: "producers",
          label: this.$t("messages.Producers"),
          sortable: false,
          visible: true,
          export: {
            formatter(producers, key, item) {
              return producers.map((producer) => producer.name).join(", ");
            },
          },
        },
        {
          key: "store",
          label: this.$t("messages.Store"),
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
    pcrCount(item) {
      return item.pcrs?.length;
    },
    pcrLinkIcon(item) {
      return this.pcrCount(item) > 1
        ? "fa-list"
        : this.pcrCount(item) === 1
        ? "fa-link"
        : "fa-plus";
    },
    pcrLinkUrl(item) {
      return this.pcrCount(item) === 0
        ? this.newPcrUrl
        : this.generateRoute("pcr_index", { dna: item.id });
    },
  },
};
</script>

<style lang="less" scoped></style>
