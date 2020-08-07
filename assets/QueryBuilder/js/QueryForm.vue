<template>
  <form action @submit.prevent="getForm">
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
      <b-button type="submit" id="search-btn" variant="primary" size="lg">Search</b-button>
      <b-button variant="warning" @dblclick="reset">Clear</b-button>
    </div>
  </form>
</template>

<script>
import QueryBlock from "./QueryBlock";
import { dtconfig } from "../../SpeciesSearch/js/datatables_utils";

export default {
  components: { QueryBlock },
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
    getForm() {
      let initData = this.$refs.initForm.getFormData();
      var joinsData = [];
      if (this.$refs.joinForm !== undefined) {
        for (let j of this.$refs.joinForm) {
          joinsData.push(j.getFormData());
        }
      }

      let jsonData = { initial: initData, joins: joinsData };

      $.ajax({
        url: "query",
        type: "POST",
        data: jsonData,
        dataType: "json",
        success: (response) => {
          $("#contentModalQuery").html(response.dql);
          $("#contentModalQuerySql").html(response.sql);
          $("#result-container").html(response.results);
          $("#result-table").DataTable(
            Object.assign(
              {
                dom: "lfrtipB",
                responsive: {
                  orthogonal: "responsive",
                },
                autoWidth: false,
              },
              dtconfig
            )
          );
        },
      });
      
      document.getElementById("getSqlButton").disabled = false;
    },
  },
  async created() {
    let response = await fetch("init");
    let json = await response.json();
    this.schema = json;
  },
};
</script>

<style lang="less" scoped>
div.form-buttons {
  display: flex;
  justify-content: space-between;
  #search-btn {
    min-width: 200px;
  }
}
</style>