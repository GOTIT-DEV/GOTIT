<template>
  <div>
    <motu-distribution-form
      id="distribution-form"
      ref="form"
      class="mb-5"
      @update:results="results = $event"
    >
    </motu-distribution-form>
    <motu-distribution-results :results="results"></motu-distribution-results>
  </div>
</template>

<script>
import MotuDistributionForm from "./MotuDistributionForm";
import MotuDistributionResults from "./MotuDistributionResults";

export default {
  components: {
    MotuDistributionForm,
    MotuDistributionResults,
  },
  data() {
    return {
      results: [],
    };
  },
};
</script>

<style lang="less" scoped>
</style>