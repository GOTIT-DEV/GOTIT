<template>
  <div>
    <query-form @update:results="updateResults($event)" @submit="onSubmit" />
    <hr>
    <div v-if="submitted" ref="results">
      <legend class="mb-3">
        <h3>
          Results
          <b-button v-b-modal.sql-modal variant="primary" :disabled="loading">
            Get SQL
          </b-button>
        </h3>
      </legend>
      <b-modal
        id="sql-modal"
        size="xl"
        body-bg-variant="dark"
        title="Query as SQL"
        scrollable
        @show="copyDone = false"
      >
        <template #modal-title>
          Query as SQL
        </template>

        <template #modal-footer="{ ok }">
          <b-button
            v-clipboard:copy="formattedSql"
            v-clipboard:success="onCopy"
            v-clipboard:error="onCopyFail"
            :variant="copyDone ? 'success' : 'info'"
          >
            {{ $t(copyDone ? "Copied" : "Copy") }}
            <font-awesome-icon :icon="copyDone ? 'check' : 'copy'" />
          </b-button>
          <b-button variant="primary" @click="ok()">
            OK
          </b-button>
        </template>

        <pre
          id="sql-query"
          v-highlightjs="formattedSql"
        ><code class="SQL" /></pre>
      </b-modal>

      <b-data-table ref="table" :items="results" :fields="fields" />
    </div>
  </div>
</template>

<script>
import QueryForm from "./QueryForm";
import BDataTable from "~Components/BDataTable";

import SQLFormat from "sql-formatter";

export default {
  components: {
    QueryForm,
    BDataTable,
  },
  data() {
    return {
      results: [],
      fields: [],
      querySql: "",
      submitted: false,
      loading: false,
      copyDone: false,
    };
  },
  computed: {
    formattedSql() {
      return SQLFormat.format(this.querySql);
    },
  },
  methods: {
    onCopy() {
      this.copyDone = true;
    },
    onCopyFail() {},
    onSubmit() {
      this.loading = true;
    },
    updateResults(data) {
      this.loading = false;
      this.submitted = true;
      this.results = data.results;
      this.querySql = data.sql;
      this.fields = this.formatFields(data.fields);
    },
    formatFields(fields) {
      return Array.from(Object.entries(fields)).reduce(
        (acc, [table, columns]) => {
          return acc.concat(
            columns.map(({ id, label }) => ({
              key: `${table}_${id}`,
              label: `${table}.${label}`,
              table,
            }))
          );
        },
        []
      );
    },
  },
};
</script>

<style lang="less">
@import "~highlight.js/styles/monokai.css";

#sql-modal {
  .modal-body {
    padding: 0;
    #sql-query {
      max-height: 75vh;
      font-size: 12pt;
      margin: 0;
    }
  }
}
</style>
