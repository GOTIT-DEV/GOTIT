<template>
  <b-modal
    id="modal-sequences"
    size="xl"
    :title="`${station.station_code} // MOTU ${station.motu}`"
  >
    <b-table
      striped
      responsive
      primary-key="id"
      :fields="fields"
      :items="station.sequences"
    >
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
    </b-table>
  </b-modal>
</template>

<script>
export default {
  data() {
    return {
      station: {},
      fields: [
        {
          key: "code",
        },
        {
          key: "accession_number",
        },
        {
          key: "seq_type",
          formatter: (isExternal) =>
            this.$t(
              isExternal
                ? "queries.entity.seq.type.externe"
                : "queries.entity.seq.type.interne"
            ),
        },
        {
          key: "motu",
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

<style lang="less" scoped>
</style>