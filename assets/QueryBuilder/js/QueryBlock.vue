<template>
  <div>
    <b-card header-tag="header" footer-tag="footer">
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
            :options="['Inner join', 'Left join']"
            :allowEmpty="false"
            :preselectFirst="true"
            :searchable="false"
            :show-labels="false"
          >
          </multiselect>
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
          >
          </multiselect>
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
              {{ option.from }} <i class="fas fa-long-arrow-alt-right"></i>
              {{ option.to }}
            </template>
            <template slot="option" slot-scope="props">
              {{ props.option.from }}
              <i class="fas fa-long-arrow-alt-right"></i> {{ props.option.to }}
            </template>
          </multiselect>
        </div>
        <b-button
          variant="danger"
          class="remove-join"
          @click="$emit('deleteJoin')"
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

      <div>
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
        >
        </multiselect>
      </div>

      <div>
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

      <b-form-group
        class="constraints-container"
        label="Constraints"
        label-for="toggle-constraints"
      >
        <ToggleButton
          id="toggle-constraints"
          class="toggle-btn"
          v-model="hasConstraints"
        ></ToggleButton>
      </b-form-group>

      <b-collapse
        id="querybuilder-collapse"
        class="qb-container"
        v-model="hasConstraints"
      >
        <div
          id="query-builder"
          ref="querybuilder"
          class="collapsed-query-builder qb-form"
        ></div>

        <b-button
          variant="warning"
          class="qb-reset"
          data-target="#query-builder"
          @click="resetQueryBuilder"
          >Reset</b-button
        >
      </b-collapse>
    </b-card>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import { ToggleButton } from "vue-js-toggle-button";
import "jQuery-QueryBuilder";
import "./plugins.js";

export default {
  components: { ToggleButton, Multiselect },
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
        this.fields = newList;
      }
      if (newList.length) {
        $(this.$refs.querybuilder).queryBuilder("setFilters", true, newList);
      }
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
      this.$emit("tableChanged", {
        from: this.from,
        table: this.table,
        alias: this.alias,
      });
    },
    aliasChanged() {
      this.$emit("aliasChanged", {
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
  },
  mounted() {
    // Init query-builder with fields and filters
    $(this.$refs.querybuilder).queryBuilder({
      plugins: [
        // "bt-tooltip-errors",
        "bt-selectpicker",
        // "date-inputmask",
      ],
      filters: [{ id: "empty", label: "empty", type: "integer" }],
      lang: { delete_rule: " ", delete_group: " " },
    });
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

// span.multiselect__option{
//   padding: 6px 12px;
// }
.multiselect--disabled .multiselect__current,
.multiselect--disabled .multiselect__select {
  background: none;
  color: #a6a6a6;
}

.card-header {
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

.card-body {
  display: grid;
  gap: 10px;
  grid-template-columns: 2fr 1fr 0fr;
  padding: 10px 20px;
  grid-template-rows: auto;
  gap: 10px;
  grid-template-areas:
    "f-container a-container c-container"
    "qb-container qb-container qb-container";

  .fields-container {
    grid-area: f-container;
    justify-self: start;
    width: 100%;
  }

  .alias-container {
    grid-area: a-container;
    justify-self: start;
  }

  .constraints-container {
    grid-area: c-container;
    justify-self: end;
  }

  .qb-container {
    display: grid;
    grid-area: qb-container;
    justify-content: stretch;
    align-items: start;
    grid-template-columns: 1fr 0fr;
    grid-template-rows: auto;
    grid-template-areas: "qb-form qb-reset";
    gap: 10px;
    .qb-form {
      grid-area: qb-form;
    }

    .qb-reset {
      grid-area: qb-reset;
    }
  }
}
</style>
