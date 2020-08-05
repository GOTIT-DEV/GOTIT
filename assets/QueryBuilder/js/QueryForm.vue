<template>
  <form action="">
    <!-- Initial block -->
    <QueryBlock
      class="mb-3"
      :schema="schema"
      @aliasChanged="initialAliasUpdated($event)"
      @tableChanged="initialTable = $event"
    >
    </QueryBlock>

    <!-- Join blocks -->
    <QueryBlock
      v-for="(block, index) in joins"
      :key="block.id"
      class="mb-3"
      :schema="schema"
      v-bind:availableTables="availableTables.slice(0, index + 1)"
      @aliasChanged="joinAliasUpdated(index, $event)"
      @tableChanged="$set(joins, index, { ...$event, id: joins[index].id })"
      @deleteJoin="joins.splice(index, 1)"
      join
    >
    </QueryBlock>

    <div class="form-buttons">
      <b-button variant="success" @click="addJoin">
        <i class="fas fa-plus-circle"></i>
        Join new table
      </b-button>
      <b-button id="search-btn" variant="primary" size="lg">Search</b-button>
      <b-button variant="warning" @dblclick="reset">Clear</b-button>
    </div>
  </form>
</template>

<script>
import QueryBlock from "./QueryBlock";
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
      joins: [
        // { from : {table, alias}, table, alias, id }
      ],
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