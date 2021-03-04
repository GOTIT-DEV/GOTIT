<template>
  <b-card class="query-block mb-5" header-tag="header" footer-tag="footer">
    <!-- Join Block header -->
    <template v-if="join" v-slot:header class="header-container">
      <div>
        <label class="text-uppercase">{{ $t("from") }}</label>
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
            {{ option.label }} | {{ option.alias }}
          </template>
          <template slot="option" slot-scope="props">
            {{ props.option.label }} | {{ props.option.alias }}
          </template>
        </multiselect>
      </div>

      <div>
        <label class="text-uppercase">{{ $t("join") }}</label>
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
        <label class="text-uppercase">{{ $t("join_to") }}</label>
        <multiselect
          id="adjtables-select"
          v-model="table"
          :options="tableList"
          label="label"
          :searchable="false"
          :allow-empty="false"
          @change="tableChanged"
          required
          :disabled="from == undefined"
          :show-labels="false"
        >
          <template slot="singleLabel" slot-scope="{ option }">
            <span class="text-muted">
              {{ option.through ? `[${option.through.label}]` : "" }}
            </span>
            {{ option.label }}
          </template>
          <template slot="option" slot-scope="props">
            <span class="text-muted">
              {{
                props.option.through ? `[${props.option.through.label}]` : ""
              }}
            </span>
            {{ props.option.label }}
          </template>
        </multiselect>
      </div>

      <div v-if="joinPathList.length > 1">
        <label class="text-uppercase">{{ $t("join_on") }}</label>
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
            {{ option.from.label }}
            <i class="fas fa-long-arrow-alt-right"></i>
            {{ option.to.label }}
          </template>
          <template slot="option" slot-scope="props">
            {{ props.option.from.label }}
            <i class="fas fa-long-arrow-alt-right"></i>
            {{ props.option.to.label }}
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
        <label class="text-uppercase">{{ $t("table") }}</label>
        <multiselect
          id="table-select"
          ref="table"
          :options="groupedTableList"
          label="label"
          track-by="name"
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
      <label class="text-uppercase">{{ $t("alias") }}</label>
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
      <label class="text-uppercase">{{ $t("select") }}</label>
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
          :table="table"
          :query.sync="query"
          :active.sync="hasConstraints"
          @reset="resetQuery"
        />
      </template>
    </query-filter>
  </b-card>
</template>

<i18n>
{
  "en": {
    "table": "table",
    "alias": "alias",
    "from": "from",
    "join": "join",
    "join_to": "to",
    "select": "select",
    "join_on": "on"
  },
  "fr": {
    "table": "table",
    "alias": "alias",
    "from": "de",
    "join": "jointure",
    "join_to": "vers",
    "select": "champs",
    "join_on": "par"
  }
}
</i18n>

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
        entity: this.table.entity,
        label: this.table.label,
        alias: this.alias,
        prevAlias: this.prevAlias,
      };
    },
    relations() {
      return this.from == undefined
        ? {}
        : this.schema[this.from.entity].relations || {};
    },
    tableList() {
      return this.join ? this.joinTableList : this.groupedTableList;
    },
    joinTableList() {
      return Object.values(this.schema)
        .filter(({ entity }) => entity in this.relations)
        .map((table) => {
          if (table.type === 1) {
            const related = []
              .concat(...Object.values(table.relations))
              .filter(({ entity }) => entity != this.from.entity)
              .shift();
            const through = {
              entity: table.entity,
              name: table.name,
              label: table.label,
              alias: `${table.name}_${this.id}`,
              in: this.schema[this.from.entity].relations[table.entity][0],
              out: table.relations[related.entity][0],
            };

            table = {
              ...this.schema[related.entity],
              through,
            };
          }
          return table;
        })
        .sort((tableA, tableB) => tableA.label.localeCompare(tableB.label));
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
          grp.entities.sort((a, b) => a.label.localeCompare(b.label));
          return grp;
        });
    },
    rules() {
      return this.table.name == "vocabulary"
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
        ? this.table.through.entity
        : this.table.entity;
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
          this.path.entity == this.table.entity
            ? this.path.from.id
            : this.table.through.out.from.id;
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
        table: this.table.entity,
        alias: this.alias,
        fields: this.fields.map(({ id, label }) => ({ id, label })),
        rules: this.query,
      };
    },
    getJoinFormData() {
      const assoc = this.table.through;
      return assoc
        ? {
            from: {
              table: assoc.entity,
              alias: assoc.alias,
              column: assoc.out.from.id,
            },
            type: this.joinType,
            column: assoc.out.to.id,
          }
        : {
            from: { ...this.from, column: this.path.from.id },
            type: this.joinType,
            column: this.path.to.id,
          };
    },
    getJoinAssocData() {
      const assoc = this.table.through;
      return {
        table: assoc.entity,
        alias: assoc.alias,
        join: {
          from: { ...this.from, column: assoc.in.from.id },
          type: this.joinType,
          column: assoc.in.to.id,
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

span.join-path-label {
  display: flex;
  justify-content: space-between;
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

    #querybuilder {
      grid-area: qbuilder;
    }
  }
}
</style>
