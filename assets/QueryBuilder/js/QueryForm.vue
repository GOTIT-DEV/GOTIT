<template>
  <form action @submit.prevent="submit">
    <legend>
      <h2 id="query-fields-title">{{ $t("query") | capitalize }}</h2>
    </legend>

    <!-- Initial block -->
    <query-block
      class="mb-3"
      :schema="schema"
      ref="initBlock"
      @update:table="initialTable = $event"
    />

    <!-- Join blocks -->
    <query-block
      v-for="(block, index) in joins"
      :key="block.id"
      :id="block.id"
      class="mb-3"
      :schema="schema"
      ref="joinForm"
      v-bind:availableTables="availableTables.slice(0, index + 1)"
      @update:table="$set(joins, index, $event)"
      @delete-join="joins.splice(index, 1)"
      join
    />

    <div class="form-buttons">
      <b-button variant="success" @click="addJoin">
        <i class="fas fa-plus-circle"></i>
        {{ $t("join_table") }}
      </b-button>
      <button-loading id="submit" ref="submit" v-bind:loading="loading">
        {{ $t("search") | capitalize }}
      </button-loading>
      <b-button
        variant="light"
        class="border-warning text-secondary"
        @click="reset"
      >
        <font-awesome-icon class="text-primary" icon="redo" />
        {{ $t("reset") | capitalize }}
      </b-button>
    </div>
  </form>
</template>

<i18n>
{
  "en": {
    "query" : "query",
    "join_table": "Join new table",
    "search": "search",
    "reset": "reset"
  },
  "fr": {
    "query": "requête",
    "join_table": "Nouvelle jointure",
    "search": "rechercher",
    "reset": "réinitialiser"
  }
}
</i18n>

<script>
import QueryBlock from "./QueryBlock";

import ButtonLoading from "~Components/ButtonLoading";

export default {
  components: { QueryBlock, ButtonLoading },
  computed: {
    /** Tables involved in the query, available to join from */
    availableTables() {
      return [this.initialTable, ...this.joins]
        .map(({ table, entity, label, alias, prevAlias }) => ({
          table,
          entity,
          label,
          alias,
          prevAlias,
        }))
        .filter(({ entity }) => entity in this.schema);
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
  async created() {
    let response = await fetch("init");
    this.schema = await response.json();
    this.loading = false;
  },
  methods: {
    addJoin() {
      this.joins.push({ id: (this.joinsCount += 1) });
    },
    reset() {
      this.joins = [];
      this.$refs.initBlock.hasConstraints = false;
      this.$refs.initBlock.resetQuery();
    },
    async submit() {
      this.loading = true;
      const joinBlocks = this.$refs.joinForm || [];
      const jsonData = {
        initial: this.$refs.initBlock.getFormData(),
        joins: joinBlocks.map((block) => block.getFormData()).flat(),
      };
      this.$emit("submit", jsonData);

      const response = await fetch("query", {
        method: "POST",
        body: JSON.stringify(jsonData),
      });
      const data = await response.json();
      this.loading = false;
      this.$emit("update:results", data);
    },
  },
};
</script>

<style lang="less" scoped>
div.form-buttons {
  display: flex;
  justify-content: space-between;
  #submit {
    width: 200px;
  }
}
</style>