<template>
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
        />
      </div>
      <div id="join-target">
        <label>TO</label>
        <multiselect
          id="adjtables-select"
          v-model="table"
          :options="tableList"
          label="label"
          :searchable="false"
          @change="tableChanged"
          required
          :disabled="from == undefined"
          :show-labels="false"
        />
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
          label="name"
          v-model="table"
          required
          group-values="entities"
          group-label="type"
          :allowEmpty="false"
          :show-labels="false"
          @change="tableChanged"
        />
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
        :disabled="!table.name"
        @blur="validateAlias()"
      />
    </div>

    <div class="fields-select">
      <label>SELECT</label>
      <multiselect
        id="fields-select"
        v-model="fields"
        :options="groupedfilters"
        :close-on-select="false"
        group-values="fields"
        group-label="group"
        :group-select="true"
        multiple
        required
        label="label"
        trackBy="label"
        :searchable="false"
        :disabled="!table.name"
      />
    </div>

    <query-filter id="querybuilder" :rules="rules" :query.sync="query">
      <template v-slot:default="slotProps">
        <query-filter-group
          v-bind="slotProps"
          :query.sync="query"
          :active.sync="hasConstraints"
          @reset="resetQuery"
        />
      </template>
    </query-filter>
  </b-card>
</template>

<script>
import Multiselect from "vue-multiselect";
import { ToggleButton } from "vue-js-toggle-button";

import QueryFilter from "./QueryFilter";
import QueryFilterGroup from "./QueryFilterGroup";

export default {
  components: { ToggleButton, Multiselect, QueryFilter, QueryFilterGroup },
  props: {
    id: { type: Number },
    join: { type: Boolean, default: false },
    schema: { type: Object, required: true },
    availableTables: { type: Array, default: () => [] },
  },
  computed: {
    state() {
      return {
        id: this.id,
        from: this.from,
        table: this.table.name,
        alias: this.alias,
        prevAlias: this.prevAlias,
      };
    },
    relations() {
      return this.from == undefined
        ? {}
        : this.schema[this.from.table].relations || {};
    },
    tableList() {
      return this.join ? this.joinTableList : this.groupedTableList;
    },
    joinTableList() {
      return (
        Object.values(this.schema)
          // Extract related tables
          .filter(({ name }) => name in this.relations)
          .map((table) => {
            if (table.type === 1) {
              // Follow relationship through intermediary table
              const related = Object.values(table.relations)
                .flat()
                .filter(({ entity }) => entity != this.from.table)
                .shift();
              const through = {
                table: table.name,
                alias: `${table.name}_${this.id}`,
                in: this.schema[this.from.table].relations[table.name][0],
                out: table.relations[related.entity][0],
              };

              table = {
                ...this.schema[related.entity],
                through,
                get label() {
                  return `[${this.through.table}] ${this.name}`;
                },
              };
            } else {
              table.label = table.name;
            }
            return table;
          })
          .sort((tableA, tableB) => {
            function makeSortId(table) {
              return (
                `${table.name}` +
                `${table.label == table.name ? "" : table.label}`
              );
            }
            return makeSortId(tableA).localeCompare(makeSortId(tableB));
          })
      );
    },
    groupedTableList() {
      return Object.values(this.schema)
        .reduce(
          (acc, entity) => {
            acc[entity.type].entities.push(entity);
            return acc;
          },
          [
            { type: "Primary", entities: [] },
            { type: "Secondary", entities: [] },
          ]
        )
        .map((grp) => {
          grp.entities.sort((a, b) => a.name.localeCompare(b.name));
          return grp;
        });
    },
    rules() {
      return this.table.name == "Voc"
        ? this.setupVocFilters(this.filters)
        : this.filters;
    },
    filters() {
      return this.table.filters || [];
    },
    groupedfilters() {
      return this.filters.reduce(
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
      const target_table = this.table.through
        ? this.table.through.table
        : this.table.name;
      return this.join ? this.relations[target_table] || [] : [];
    },
  },
  data() {
    return {
      hasConstraints: false,
      from: undefined,
      joinType: undefined,
      table: {},
      path: undefined,
      alias: "",
      prevAlias: "",
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
    filters: function (newList) {
      this.resetQuery();
      this.fields = this.join
        ? []
        : newList.filter(
            (field) => !(field.id.endsWith("Maj") || field.id.endsWith("Cre"))
          );
    },
    tableList: function (newList) {
      if (this.join) this.table = newList[0] || {};
    },
    groupedTableList: function (newList) {
      if (!this.join) {
        this.table = newList[0].entities[0];
        this.tableChanged();
      }
    },
    table: function (newTable) {
      this.alias = this.generateAlias(newTable.name);
      this.prevAlias = "";
      this.tableChanged();
    },
    alias: function (newAlias, oldAlias) {
      this.prevAlias = oldAlias;
      this.tableChanged();
    },
    joinPathList: function (newList) {
      if (this.join) this.path = newList[0];
    },
  },
  methods: {
    setupVocFilters(vocFilters) {
      let content = this.table.content;
      if (this.join) {
        const fieldToMatch =
          this.path.entity == this.table.name
            ? this.path.from
            : this.table.through.out.from;
        const match = fieldToMatch.match(/(?<parent>\w+)VocFk/);
        content = this.table.content.filter(
          (voc) => match && match.groups.parent == voc.parent
        );
      }
      return (
        vocFilters
          // Remove `parent` rule when in a join block
          .filter((rule) => !this.join || rule.id != "parent")
          .map((rule) => {
            return ["parent", "code", "libelle"].includes(rule.id)
              ? {
                  ...rule,
                  type: "custom-component",
                  component: Multiselect,
                  operators: [
                    "=",
                    "!=",
                    "in",
                    "not in",
                    "is null",
                    "is not null",
                  ],
                  props: {
                    ...rule.props,

                    options: [
                      ...new Set(content.map((voc) => voc[rule.id])),
                    ].sort(),
                    searchable: true,
                    allowEmpty: false,
                    required: true,
                    showLabels: false,
                  },
                }
              : rule;
          })
      );
    },
    resetQuery() {
      this.query = { logicalOperator: "and", children: [] };
    },
    tableChanged() {
      this.$emit("update:table", this.state);
    },
    generateAlias(table) {
      if (!table) return undefined;
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
    validateAlias() {
      if (this.alias == "") this.alias = this.generateAlias(this.table.name);
    },
    getBaseFormData() {
      return {
        table: this.table.name,
        alias: this.alias,
        fields: this.fields.map((f) => f.label),
        rules: this.query,
      };
    },
    getJoinFormData() {
      const assoc = this.table.through;
      return assoc
        ? {
            from: {
              table: assoc.table,
              alias: assoc.alias,
              column: assoc.out.from,
            },
            type: this.joinType,
            column: assoc.out.to,
          }
        : {
            from: { ...this.from, column: this.path.from },
            type: this.joinType,
            column: this.path.to,
          };
    },
    getJoinAssocData() {
      const assoc = this.table.through;
      return {
        table: assoc.table,
        alias: assoc.alias,
        join: {
          from: { ...this.from, column: assoc.in.from },
          type: this.joinType,
          column: assoc.in.to,
        },
      };
    },
    getFormData() {
      if (this.join) {
        let data = [
          {
            ...this.getBaseFormData(),
            join: this.join ? this.getJoinFormData() : null,
          },
        ];
        if (this.table.through) data.unshift(this.getJoinAssocData());
        return data;
      } else {
        return this.getBaseFormData();
      }
    },
  },
};
</script>



<style lang="less">
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
  box-shadow: 3px 3px 6px grey;
  > .card-header {
    padding: 0.5rem 1rem;
    display: grid;
    align-items: start;
    grid-template-columns: 1.2fr auto 1fr 1fr 0fr;
    grid-template-areas: "table join target path delete";
    gap: 10px;

    #join-target {
      grid-area: target;
      min-width: 150px;
    }

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

    // .filter-switch {
    //   grid-area: filter-switch;
    //   justify-self: start;
    //   align-self: end;
    //   display: flex;
    //   align-items: center;
    // }

    #querybuilder {
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
