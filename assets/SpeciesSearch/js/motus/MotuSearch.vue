<template>
  <div>
    <motu-form
      id="motu-form"
      ref="motuForm"
      @update:results="results = $event"
      @update:query="query = $event"
    />
    <hr>
    <motu-result-table
      :items="results"
      :export-filename="
        query ? `${query.genus}_${query.species}_motu_summary.csv` : null
      "
      @show-details="showDetails($event)"
    />
    <motu-details-modal ref="details" :items="details" />
  </div>
</template>

<script>
import MotuDetailsModal from "./MotuDetailsModal";
import MotuForm from "./MotuForm";
import MotuResultTable from "./MotuResultTable";
export default {
  components: {
    MotuForm,
    MotuResultTable,
    MotuDetailsModal,
  },
  data() {
    return {
      results: [],
      query: undefined,
      details: [],
    };
  },
  methods: {
    async showDetails(item) {
      const formData = new FormData();
      formData.append("taxon", item.id);
      formData.append("method", item.id_method);
      formData.append("dataset", item.id_dataset);
      formData.append("level", this.query.level);
      this.query.criteria.forEach((criterion) => {
        formData.append("criteria[]", criterion);
      });

      const detailsUrl = Routing.generate("motu-modal");
      const response = await fetch(detailsUrl, {
        method: "POST",
        body: formData,
      });

      this.details = await response.json();
      this.$refs.details.$bvModal.show("details-modal");
    },
  },
};
</script>

<style lang="less" scoped></style>
