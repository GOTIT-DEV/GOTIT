<template>
  <b-modal
    id="modal-sequences"
    size="xl"
    :title="`${site.site_code} // MOTU ${site.motu}`"
  >
    <b-data-table primary-key="id" :fields="fields" :items="site.sequences">
      <template #cell(code)="data">
        <a
          :id="`seq-code-${data.index}`"
          :href="
            generateRoute(
              data.item.seq_type
                ? 'external_sequence_show'
                : 'internal_sequence_show',
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
    </b-data-table>
  </b-modal>
</template>

<script>
import BDataTable from "~Components/BDataTable";
export default {
  components: { BDataTable },
  data() {
    return {
      site: {},
      fields: [
        {
          key: "code",
          label: "Code",
          sortable: true,
        },
        {
          key: "accession_number",
          label: "Accession",
          sortable: true,
        },
        {
          key: "seq_type",
          label: "Type",
          sortable: true,
          formatter: (isExternal) =>
            this.$t(
              isExternal
                ? "queries.entity.seq.type.externe"
                : "queries.entity.seq.type.interne"
            ),
        },
        {
          key: "motu",
          label: "MOTU",
          sortable: true,
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

<style lang="less" scoped></style>
