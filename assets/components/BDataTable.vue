<template>
  <div class="table-container" :class="classes">
    <b-button-toolbar :justify="true" class="mb-1">
      <b-form-group label-cols="auto" :label="$t('pageLength')" class="mb-0">
        <b-form-select v-model="perPage" size="sm">
          <b-form-select-option value="10">
            10
          </b-form-select-option>
          <b-form-select-option value="25">
            25
          </b-form-select-option>
          <b-form-select-option value="50">
            50
          </b-form-select-option>
          <b-form-select-option value="100">
            100
          </b-form-select-option>
        </b-form-select>
      </b-form-group>
      <b-form-group
        label-cols="auto"
        :label="$tc('field', 2)"
        class="fields-select mb-0 w-25"
      >
        <multiselect
          v-model="selectedFields"
          multiple
          :close-on-select="false"
          :options="fields"
          track-by="key"
          :searchable="false"
          placeholder="Fields"
          :selected-label="null"
          :show-labels="false"
          :option-height="30"
          :allow-empty="false"
          :max-height="400"
        >
          <template slot="tag">
            {{ "" }}
          </template>
          <template slot="selection" slot-scope="{ values, isOpen }">
            <span v-if="values.length && !isOpen" class="multiselect__single">
              {{ values.length }} {{ $tc("visible", values.length) }}
            </span>
          </template>
          <template slot="option" slot-scope="props">
            <div class="option__desc">
              <span class="option__title">
                {{ props.option.label || capitalize(props.option.key) }}
              </span>
              <i
                v-if="isSelected(props.option)"
                class="fas fa-check fa-xs text-primary"
              />
            </div>
          </template>
        </multiselect>
      </b-form-group>

      <div class="search-bar">
        <i class="fas fa-search icon" />
        <b-input-group>
          <b-input
            class="search-bar-input"
            type="text"
            size="sm"
            :placeholder="searchbarPlaceholder || $t('search')"
            @input="filter = new RegExp($event, 'i')"
          />
        </b-input-group>
      </div>
    </b-button-toolbar>

    <b-table
      responsive
      striped
      :busy="busy"
      primary-key="code"
      :fields="visibleFields"
      :items="items"
      :per-page="perPage"
      :current-page="tablePage"
      sort-by="id"
      :filter="filter"
      v-bind="$attrs"
    >
      <template
        v-for="slotName in Object.keys($scopedSlots)"
        #[slotName]="slotScope"
      >
        <slot :name="slotName" v-bind="slotScope" />
      </template>
    </b-table>

    <b-button-toolbar :justify="true">
      <json-csv
        v-if="allowExport"
        :data="exportedItems"
        :labels="exportedHeader"
        :name="exportFilename"
      >
        <b-button size="sm" variant="light" class="border" :disabled="!rows">
          <i class="fas fa-download" />
          {{ $t("exportCSV") }}
        </b-button>
      </json-csv>

      <span>
        {{
          rows
            ? $t("pagePosition", {
              first: displayedItemRange[0],
              last: displayedItemRange[1],
              total: rows,
            })
            : $t("noData")
        }}
      </span>

      <b-pagination
        v-model="tablePage"
        :total-rows="rows"
        :per-page="perPage"
        class="float-right mb-0"
      />
    </b-button-toolbar>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import JsonCsv from "vue-json-csv";
export default {
  name: "BDataTable",
  components: {
    Multiselect,
    JsonCsv,
  },
  props: {
    allowExport: {
      type: Boolean,
      default: true,
    },
    items: {
      type: Array,
      required: true,
    },
    fields: {
      type: Array,
      required: true,
    },
    classes: {
      type: String,
      default: "",
    },
    busy: {
      type: Boolean,
    },
    searchbarPlaceholder: {
      type: String,
      default: null,
    },
    exportColumnsByKey: {
      type: Boolean,
      default: false,
    },
    exportFilename: {
      type: String,
      default: "data.csv",
    },
  },
  data() {
    return {
      tablePage: 1,
      perPage: 10,
      selectedFields: [],
      filter: "",
    };
  },
  computed: {
    exportedItems() {
      return this.items.map((item) =>
        this.fields.reduce((acc, field) => {
          if (field.unpacker) {
            acc = {
              ...acc,
              ...field.unpacker(item[field.key]),
            };
          } else {
            acc[field.key] = field.formatter
              ? field.formatter(item[field.key])
              : item[field.key];
          }
          return acc;
        }, {})
      );
    },
    exportedHeader() {
      return Object.fromEntries(
        this.fields.map((f) => [
          f.key,
          !this.exportColumnsByKey && f.label ? f.label : f.key,
        ])
      );
    },
    locale() {
      return this.$i18n.locale;
    },
    displayedItemRange() {
      const first = (this.tablePage - 1) * this.perPage + 1;
      return [first, Math.min(this.rows, first + this.perPage - 1)];
    },
    rows() {
      return this.items.length;
    },
    visibleFields() {
      const visibleKeys = new Set(this.selectedFields.map((f) => f.key));
      return this.fields.filter((f) => visibleKeys.has(f.key));
    },
  },
  watch: {
    fields(newFields) {
      this.selectedFields = newFields;
    },
  },
  created() {
    if (this.selectedFields.length == 0) this.selectedFields = this.fields;
  },
  methods: {
    capitalize(str) {
      return str[0].toUpperCase() + str.slice(1);
    },
    isSelected(option) {
      return this.selectedFields.some((f) => f.key === option.key);
    },
  },
};
</script>

<i18n>
{
  "en": {
    "exportCSV": "Export to CSV",
    "search": "Search",
    "pageLength": "Page length",
    "field": "Field | Fields",
    "visible" : "shown",
    "pagePosition": "Showing {first} to {last} out of {total} items",
    "noData": "No data to display"
  },
  "fr": {
    "exportCSV": "Exporter CSV",
    "pageLength": "Afficher",
    "search": "Rechercher",
    "field": "Champ | Champs",
    "visible" : "visible | visibles",
    "pagePosition": "Éléments {first} à {last} sur {total}",
    "noData": "Aucune donnée à afficher"
  }
}
</i18n>

<style lang="less">
@import "~vue-multiselect/dist/vue-multiselect.min.css";

.table-container {
  .fields-select {
    min-width: 200px;
  }
  .btn-toolbar {
    display: flex;
    align-items: center;
  }
  .multiselect {
    min-height: 30px;
    .multiselect__select {
      height: 28px;
    }
    .multiselect__tags {
      min-height: 30px;
      padding-top: 4px;
      .multiselect__single {
        margin-bottom: 3px;
      }
    }
    .multiselect__content-wrapper {
      min-width: 100%;
      width: auto;
    }
    .multiselect__option {
      min-height: 30px;
      padding: 7px;
      font-weight: normal;
      .option__desc {
        display: flex;
        justify-content: space-between;
        > i {
          margin-left: 5px;
        }
      }
    }
  }

  .b-table .column {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 20em;
  }

  .search-bar {
    display: flex;
    align-items: center;
    .icon {
      transform: translate(20px);
      height: auto;
      z-index: 10;
    }
    .search-bar-input {
      padding-left: 25px;
    }
  }
}
</style>
