<template>
  <b-modal id="details-modal" size="xl" :title="title">
    <b-data-table
      ref="table"
      :items="items"
      :fields="fields"
      :export-filename="exportedTableName"
    >
      <template #cell(code)="data">
        <a
          :id="`seq-code-${data.index}`"
          :href="
            generateRoute(
              data.item.type
                ? 'external_sequence_show'
                : 'internal_sequence_show',
              { id: data.item.id }
            )
          "
        >
          {{ data.value }}
        </a>
        <b-tooltip :target="`seq-code-${data.index}`" triggers="hover">
          {{ data.value }}
        </b-tooltip>
      </template>

      <template #cell(acc)="data">
        <a :href="`https://www.ncbi.nlm.nih.gov/nuccore/${data.value}`">
          {{ data.value }}
        </a>
      </template>
    </b-data-table>
  </b-modal>
</template>

<i18n>
  {
    "en": {
      "criterion": "Criterion | Criteria",
      "gene": "Gene | Genes",
      "internal": "Internal",
      "external": "External"
    },
    "fr": {
      "criterion": "Critère | Critères",
      "gene": "Gène | Gènes",
      "internal": "Interne",
      "external": "Externe"
    }
  }
</i18n>

<script>
import BDataTable from "~Components/BDataTable";
export default {
  components: {
    BDataTable,
  },
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
          key: "code",
          class: "column",
          sortable: true,
          visible: true,
        },
        {
          key: "acc",
          label: "Accession",
          sortable: true,
          visible: true,
        },
        {
          key: "gene",
          label: this.$tc("gene"),
          sortable: true,
          visible: true,
        },
        {
          key: "type",
          formatter: (isExternal) => {
            return this.$t(isExternal ? "external" : "internal");
          },
          sortable: true,
          visible: true,
        },
        {
          key: "motu",
          label: "MOTU",
          sortable: true,
          visible: true,
        },
        {
          key: "criterion",
          label: this.$tc("criterion"),
          sortable: true,
          visible: true,
        },
      ],
    };
  },
  computed: {
    title() {
      return this.items.length
        ? (({ taxname, method }) => `${taxname} // ${method}`)(this.items[0])
        : "";
    },
    exportedTableName() {
      return this.items.length
        ? (({ taxname, method }) => `${taxname}_${method}_motus.csv`)(
            this.items[0]
          )
        : null;
    },
  },
};
</script>

<style lang="less" scoped></style>
