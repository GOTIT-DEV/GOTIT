<template>
  <form action @submit.prevent="submit">
    <!-- Initial block -->
    <QueryBlock
      class="mb-3"
      :schema="schema"
      ref="initForm"
      @update:alias="initialAliasUpdated($event)"
      @update:table="initialTable = $event"
    ></QueryBlock>

    <!-- Join blocks -->
    <QueryBlock
      v-for="(block, index) in joins"
      :key="block.id"
      :id="block.id"
      class="mb-3"
      :schema="schema"
      ref="joinForm"
      v-bind:availableTables="availableTables.slice(0, index + 1)"
      @update:alias="joinAliasUpdated(index, $event)"
      @update:table="$set(joins, index, { ...$event, id: joins[index].id })"
      @delete-join="joins.splice(index, 1)"
      join
    ></QueryBlock>

    <div class="form-buttons">
      <b-button variant="success" @click="addJoin">
        <i class="fas fa-plus-circle"></i>
        Join new table
      </b-button>
      <ButtonLoading id="submit" ref="submit" v-bind:loading="loading">
        Search
      </ButtonLoading>
      <b-button variant="warning" @dblclick="reset">Clear</b-button>
    </div>
  </form>
</template>

<script>
import QueryBlock from "./QueryBlock";
import { dtconfig } from "../../SpeciesSearch/js/datatables_utils";

import ButtonLoading from "../../components/ButtonLoading";
import MultiSelect from "vue-multiselect";

import SQLFormat from "sql-formatter";
import hljs from "highlight.js/lib/core";
import sql from "highlight.js/lib/languages/sql";
import "highlight.js/styles/monokai.css";
hljs.registerLanguage("sql", sql);

export default {
  components: { QueryBlock, ButtonLoading },
  computed: {
    availableTables() {
      return [this.initialTable, ...this.joins]
        .map(({ table, alias, prevAlias }) => {
          return { table, alias, prevAlias };
        })
        .filter(({ table }) => table in this.schema);
    },
  },

  data() {
    return {
      joinsCount: 0,
      schema: {},
      initialTable: { table: undefined, alias: undefined },
      joins: [],
      loading: true,
    };
  },
  methods: {
    addJoin() {
      this.joins.push({ id: (this.joinsCount += 1) });
    },
    initialAliasUpdated(value) {
      this.initialTable = { ...value, prevAlias: this.initialTable.alias };
    },
    joinAliasUpdated(index, value) {
      let join = this.joins[index];
      if (join.table === value.table) {
        this.$set(this.joins, index, {
          ...value,
          prevAlias: join.alias,
          id: join.id,
        });
      }
    },
    reset() {
      this.joins = [];
    },
    submit() {
      this.loading = true;
      const joinBlocks = this.$refs.joinForm || [];
      const jsonData = {
        initial: this.$refs.initForm.getFormData(),
        joins: joinBlocks.map((block) => block.getFormData()).flat(),
      };

      document.getElementById("getSqlButton").disabled = true;
      $.ajax({
        url: "query",
        type: "POST",
        data: jsonData,
        dataType: "json",
        success: (response) => {
          // $("#contentModalQuery").html(SQLFormat.format(response.dql));
          const sqlContainer = document.getElementById("contentModalQuerySql");
          sqlContainer.textContent = SQLFormat.format(response.sql);
          hljs.highlightBlock(sqlContainer);

          $("#result-container").html(response.results);

          $("#result-table").DataTable({
            ...dtconfig,
            dom: "lfrtipB",
            autoWidth: false,
            responsive: true,
            language: dtconfig.language[Translator.locale]
          });
          this.loading = false;
          $("#results").collapse("show");
          document.getElementById("getSqlButton").disabled = false;
        },
      });
    },
  },
  async created() {
    let response = await fetch("init");
    let json = await response.json();
    json.Voc.filters = json.Voc.filters.map((rule) => {
      if (rule.id == "parent") {
        return {
          ...rule,
          component: MultiSelect,
          operators: ["=", "!=", "in", "not in", "is null", "is not null"],
          props: {
            options: [
              ...new Set(json.Voc.content.map((voc) => voc.parent)),
            ].sort(),
            searchable: true,
            allowEmpty: false,
            required: true,
            showLabels: false,
          },
        };
      } else {
        return rule;
      }
    });
    this.schema = json;
    this.loading = false;
  },
};
</script>

<style lang="less" scoped>
#submit {
  width: 200px;
}

div.form-buttons {
  display: flex;
  justify-content: space-between;
  #search-btn {
    min-width: 200px;
  }
}
</style>