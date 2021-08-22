<template>
  <layout :title="$t('messages.Dna')" :help-text="$t('help.dna_index')">
    <template #content>
      <dna-table />
    </template>
    <template #header-extra>
      <dna-header :urls="urls" />
    </template>
  </layout>
</template>

<script>
import Layout from "~Components/Layout.vue";
import DnaTable from "./DnaTable.vue";
import DnaHeader from "../DnaHeader.vue";
export default {
  components: { Layout, DnaTable, DnaHeader },
  data() {
    return {
      urls: {
        create: this.generateRoute("dna_new"),
        list: this.generateRoute("dna_index"),
        import: this.generateRoute("dna_import"),
      },
    };
  },
};
</script>

<style lang="less" scoped></style>
