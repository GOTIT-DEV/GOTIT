<template>
  <div>
    <b-card class="query-block mb-5" header-tag="header" footer-tag="footer">
      <!-- Join Block header -->
      <template v-if="join" v-slot:header class="header-container">
        <div>
          <label>FROM</label>
          <multiselect
            id="table-select"
            :options="availableTables"
            label="alias"
            track-by="alias"
            :show-labels="false"
            :searchable="false"
            :allow-empty="false"
            required
            v-model="from"
          >
            <template slot="singleLabel" slot-scope="{ option }">
              {{ option.table }} | {{ option.alias }}
            </template>
            <template slot="option" slot-scope="props">
              {{ props.option.table }} | {{ props.option.alias }}
            </template>
          </multiselect>
        </div>

        <div>
          <label>JOIN</label>
          <multiselect
            id="join-type"
            required
            v-model="joinType"
            :options="['Inner Join', 'Left Join']"
            :allowEmpty="false"
            :preselectFirst="true"
            :searchable="false"
            :show-labels="false"
          ></multiselect>
        </div>
        <div>
          <label>TO</label>
          <multiselect
            id="adjtables-select"
            v-model="table"
            :options="tableList"
            :searchable="false"
            @change="tableChanged"
            required
            :disabled="from == undefined"
            :show-labels="false"
          ></multiselect>
        </div>
        <div v-if="joinPathList.length > 1">
          <label>BY</label>
          <multiselect
            id="joinPath-select"
            v-model="path"
            required
            :options="joinPathList"
            :allowEmpty="false"
            :searchable="false"
            :show-labels="false"
          >
            <template slot="singleLabel" slot-scope="{ option }">
              {{ option.from }}
              <i class="fas fa-long-arrow-alt-right"></i>
              {{ option.to }}
            </template>
            <template slot="option" slot-scope="props">
              {{ props.option.from }}
              <i class="fas fa-long-arrow-alt-right"></i>
              {{ props.option.to }}
            </template>
          </multiselect>
        </div>
        <b-button
          variant="danger"
          class="remove-join"
          @click="$emit('delete-join')"
        >
          <i class="fas fa-times"></i>
        </b-button>
      </template>

      <!-- Initial Block header -->
      <template v-else v-slot:header class="header-container">
        <div>
          <label>TABLE</label>
          <multiselect
            id="table-select"
            ref="table"
            :options="groupedTableList"
            v-model="table"
            required
            group-values="entities"
            group-label="type"
            :allowEmpty="false"
            :show-labels="false"
            @change="tableChanged"
          >
          </multiselect>
        </div>
      </template>
      <!-- Block content -->

      <div class="alias">
        <label>ALIAS</label>
        <b-input
          id="alias"
          type="text"
          v-model="alias"
          required
          :disabled="table === undefined"
          @input="aliasChanged"
        ></b-input>
      </div>

      <div class="fields-select">
        <label>SELECT</label>
        <multiselect
          id="fields-select"
          v-model="fields"
          :options="groupedFieldList"
          :close-on-select="false"
          group-values="fields"
          group-label="group"
          :group-select="true"
          multiple
          required
          label="label"
          trackBy="label"
          :searchable="false"
          :disabled="table === undefined"
        ></multiselect>
      </div>

      <!-- <b-form-group
        class="constraints-container"
        label="Constraints"
        label-for="toggle-constraints"
      > -->
      <div class="filter-switch">
        <label class="mr-2"> FILTER </label>
        <ToggleButton
          id="toggle-constraints"
          class="toggle-btn float-right"
          v-model="hasConstraints"
          :labels="true"
          :width="60"
          :height="25"
        ></ToggleButton>
      </div>
      <!-- </b-form-group> -->

      <b-collapse
        id="querybuilder-collapse"
        class="qbuilder"
        v-model="hasConstraints"
      >
        <QueryBuilder :rules="fieldList">
          <template v-slot:default="slotProps">
            <QueryBuilderGroup v-bind="slotProps" :query.sync="query" />
          </template>
        </QueryBuilder>
      </b-collapse>
    </b-card>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import { ToggleButton } from "vue-js-toggle-button";
import QueryBuilder from "./QueryBuilder";
import QueryBuilderGroup from "./QueryBuilderGroup";
import "jQuery-QueryBuilder";
import "./plugins.js";

export default {
  components: { ToggleButton, Multiselect, QueryBuilder, QueryBuilderGroup },
  props: {
    join: { type: Boolean, default: false },
    schema: { type: Object, required: true },
    availableTables: { type: Array, default: () => [] },
  },
  computed: {
    relations() {
      return this.from == undefined
        ? {}
        : this.schema[this.from.table].relations;
    },
    tableList() {
      return this.join
        ? Object.keys(this.relations).sort()
        : Object.keys(this.schema).sort();
    },
    groupedTableList() {
      if (!this.joins)
        return Object.values(this.schema)
          .reduce(
            (acc, entity) => {
              acc[entity.type].entities.push(entity.human_readable_name);
              return acc;
            },
            [
              { type: "Primary", entities: [] },
              { type: "Secondary", entities: [] },
            ]
          )
          .map((grp) => {
            grp.entities.sort();
            return grp;
          });
    },
    fieldList() {
      return this.table in this.schema ? this.schema[this.table].filters : [];
    },
    groupedFieldList() {
      return this.fieldList.reduce(
        (acc, f) => {
          const group = f.id.endsWith("Maj") || f.id.endsWith("Cre") ? 1 : 0;
          acc[group].fields.push(f);
          return acc;
        },
        [
          { group: "Fields", fields: [] },
          { group: "Metadata", fields: [] },
        ]
      );
    },
    joinPathList() {
      if (this.join) return this.relations[this.table] || [];
      else [];
    },
  },
  data() {
    return {
      hasConstraints: false,
      from: undefined,
      joinType: undefined,
      table: undefined,
      path: undefined,
      alias: "",
      fields: [],
      query: {
        logicalOperator: "and",
        children: [],
      },
    };
  },
  watch: {
    availableTables: {
      deep: true,
      handler(newList, oldList) {
        if (this.from) {
          if (!newList.map((el) => el.alias).includes(this.from.alias)) {
            const mutated = newList.find(
              (el) => el.prevAlias === this.from.alias
            );
            if (mutated) this.from.alias = mutated.alias;
            else this.from = undefined;
          }
        }
      },
    },
    fieldList: function (newList, _) {
      if (!this.join) {
        this.fields = newList.filter(
          (field) => !(field.id.endsWith("Maj") || field.id.endsWith("Cre"))
        );
      }
      // if (newList.length) {
      //   $(this.$refs.querybuilder).queryBuilder("setFilters", true, newList);
      // }
    },
    tableList: function (newList, oldList) {
      if (this.join) {
        this.table =
          oldList.length == 0 && newList.length > 0 ? newList[0] : undefined;
        this.tableChanged();
      }
    },
    groupedTableList: function (newList, _) {
      if (!this.join) {
        this.table = newList[0].entities[0];
        this.tableChanged();
      }
    },
    table: function (newTable, oldTable) {
      this.alias = this.generateAlias(newTable);
      this.tableChanged();
    },
    joinPathList: function (newList, oldList) {
      if (this.join) {
        this.path = newList[0];
      }
    },
  },
  methods: {
    tableChanged() {
      this.$emit("update:table", {
        from: this.from,
        table: this.table,
        alias: this.alias,
      });
    },
    aliasChanged() {
      this.$emit("update:alias", {
        from: this.from,
        table: this.table,
        alias: this.alias,
      });
    },
    generateAlias(table) {
      if (table === undefined) {
        return undefined;
      }
      const aliases = this.availableTables
        .filter((item) => item.table == table && item.alias)
        .map((item) => item.alias);
      let i = 1;
      let alias = table + "_" + i;
      while (aliases.includes(alias)) {
        alias = table + "_" + (i += 1);
      }
      return alias;
    },
    resetQueryBuilder() {
      $(this.$refs.querybuilder).queryBuilder("reset");
    },

    getBaseFormData() {
      return {
        table: this.table,
        alias: this.alias,
        fields: this.fields.map((f) => f.label),
        rules: this.hasConstraints ? this.query : [],
      };
    },
    getJoinFormData() {
      return {
        from: this.from,
        join: this.joinType,
        joinColumns: this.path,
      };
    },
    getFormData() {
      let data = this.getBaseFormData();
      if (this.join) data = { ...data, ...this.getJoinFormData() };
      return data
    },
  },
};
</script>



<style lang="less">
@import "~jQuery-QueryBuilder/dist/css/query-builder.default.css";
@import "~vue-multiselect/dist/vue-multiselect.min.css";

input,
select {
  width: auto;
}

.multiselect--disabled .multiselect__current,
.multiselect--disabled .multiselect__select {
  background: none;
  color: #a6a6a6;
}

.card.query-block {
  > .card-header {
    padding: 0.5rem 1rem;
    display: grid;
    align-items: start;
    grid-template-columns: 1.2fr auto 1fr 1fr 0fr;
    grid-template-areas: "table join target path delete";
    gap: 10px;

    label {
      font-weight: bold;
    }
    .remove-join {
      grid-area: delete;
      justify-self: end;
    }
  }

  > .card-body {
    display: grid;
    gap: 10px;
    grid-template-columns: 1fr 2fr;
    padding: 10px 20px;
    grid-template-rows: auto;
    gap: 10px;
    grid-template-areas:
      "alias select"
      "filter-switch select"
      "qbuilder qbuilder";

    .fields-select {
      grid-area: select;
      justify-self: start;
      width: 100%;
    }

    .alias {
      grid-area: alias;
      justify-self: start;
    }

    .filter-switch {
      grid-area: filter-switch;
      justify-self: start;
      align-self: end;
      display: flex;
      align-items: center;
    }

    .qbuilder {
      grid-area: qbuilder;
    }
    //   display: grid;
    //   justify-content: stretch;
    //   align-items: start;
    //   grid-template-columns: 1fr 0fr;
    //   grid-template-rows: auto;
    //   grid-template-areas: "qb-form qb-reset";
    //   gap: 10px;
    //   .qb-form {
    //     grid-area: qb-form;
    //   }

    //   .qb-reset {
    //     grid-area: qb-reset;
    //   }
    // }
  }
}
</style>
