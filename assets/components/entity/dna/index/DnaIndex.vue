<template>
  <layout :title="$t('messages.Dna')" :help-text="$t('help.dna_index')">
    <template #content>
      <dna-table />
    </template>
    <template #header-extra>
      <b-button-toolbar class="mt-2">
        <b-button :href="urls.list" variant="primary" size="sm" class="mx-1">
          <i class="fas fa-list" />
          List all
        </b-button>
        <b-button :href="urls.create" variant="success" size="sm" class="mx-1">
          <i class="fas fa-plus" />
          Create new
        </b-button>
        <b-button variant="light" size="sm" class="border mx-1">
          <i class="fas fa-upload" />
          Import
        </b-button>
      </b-button-toolbar>
    </template>
  </layout>
</template>

<script>
import Layout from "~Components/Layout.vue";
import DnaTable from "./DnaTable.vue";
export default {
  components: { Layout, DnaTable },
  data() {
    return {
      urls: {
        create: this.generateRoute("dna_new"),
        list: this.generateRoute("dna_index"),
      },
    };
  },
};
</script>

<style lang="less" scoped></style>
