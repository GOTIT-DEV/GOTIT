<template>
  <div class="table-container" :class="classes">
    <b-button-toolbar :justify="true" class="mb-1">
      <div class="d-flex">
        <b-form-group label-cols="auto" :label="$t('pageLength')" class="mb-0">
          <b-form-select v-model="context.perPage" size="sm">
            <b-form-select-option
              v-for="rows in rowsPerPageOptions"
              :key="rows"
              :value="rows"
            >
              {{ rows }}
            </b-form-select-option>
          </b-form-select>
        </b-form-group>
        <b-form-group
          label-cols="auto"
          :label="$tc('field', 2)"
          class="fields-select mb-0 ml-2"
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
      </div>
      <div class="search-toolbar">
        <collapse-transition
          v-if="!hasItemsProvider"
          dimension="width"
          :duration="300"
        >
          <div v-show="!searchByField" class="search-bar">
            <i class="fas fa-search icon" />
            <b-input-group>
              <b-input
                class="search-bar-input"
                type="text"
                size="sm"
                debounce="500"
                :placeholder="searchbarPlaceholder || $t('search')"
                @update="filter = new RegExp($event, 'i')"
              />
            </b-input-group>
          </div>
        </collapse-transition>
        <toggle-button
          v-if="!hasItemsProvider && hasSearchableFields"
          v-model="searchByField"
          :labels="{ checked: 'Field search', unchecked: 'Simple search' }"
          :width="100"
        />
        <!-- v-model="searchByField" -->
        <label v-if="hasItemsProvider" class="m-0">Search operator</label>
        <toggle-button
          v-show="hasItemsProvider || searchByField"
          :labels="{ checked: 'OR', unchecked: 'AND' }"
          :width="55"
          :color="{ checked: 'skyblue', unchecked: 'orange' }"
          @change="$set(filter, 'logicalOr', $event.value)"
        />
      </div>
    </b-button-toolbar>

    <b-overlay :show="busy" rounded="sm">
      <b-table
        responsive
        striped
        :busy="busy"
        primary-key="code"
        :fields="visibleFields"
        :items="hasItemsProvider ? itemProvider : items"
        :per-page="context.perPage"
        :current-page="context.currentPage"
        :filter="filter"
        v-bind="$attrs"
        :filter-function="searchByField ? filterOnSearchFields : undefined"
        @context-changed="context = $event"
      >
        <template
          v-for="slotName in Object.keys($scopedSlots)"
          #[slotName]="slotScope"
        >
          <slot :name="slotName" v-bind="slotScope" />
        </template>
        <template #thead-top>
          <fade-transition :duration="300">
            <b-tr
              v-show="hasItemsProvider || searchByField"
              class="head-search-fields"
            >
              <b-th
                v-for="f in fields"
                v-show="visibleFields.some((vf) => vf.key === f.key)"
                :key="f.key"
                class="p-1"
              >
                <b-input
                  v-if="f.searchable"
                  size="sm"
                  placeholder="Search term"
                  debounce="500"
                  @update="
                    $set(filter.fields, f.key, {
                      formatter: f.formatter,
                      term: $event,
                    })
                  "
                />
              </b-th>
            </b-tr>
          </fade-transition>
        </template>
      </b-table>
    </b-overlay>

    <b-button-toolbar :justify="true">
      <button-loading
        size="sm"
        variant="light"
        class="border"
        :disabled="!allowExport || !totalItems"
        :loading="downloading"
        @click="downloadCSV"
      >
        <i class="fas fa-download" />
        {{ $t("exportCSV") }}
      </button-loading>
      <span>
        {{
          totalItems
            ? $t("pagePosition", {
              first: displayedItemRange[0],
              last: displayedItemRange[1],
              total: totalItems,
            })
            : $t("noData")
        }}
      </span>

      <b-pagination
        v-model="context.currentPage"
        :total-rows="totalItems"
        :per-page="context.perPage"
        class="float-right mb-0"
      />
    </b-button-toolbar>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import ButtonLoading from "./ButtonLoading.vue";
import { ToggleButton } from "vue-js-toggle-button";
import { FadeTransition } from "vue2-transitions";
import { CollapseTransition } from "@ivanv/vue-collapse-transition";
export default {
  name: "BDataTable",
  components: {
    FadeTransition,
    CollapseTransition,
    ToggleButton,
    Multiselect,
    ButtonLoading,
  },
  props: {
    rowsPerPageOptions: {
      type: Array,
      default() {
        return [10, 20, 30, 50];
      },
    },
    /**
     * @see https://bootstrap-vue.org/docs/components/table#using-items-provider-functions
     */
    items: {
      type: [Array, Function],
      required: true,
    },
    /**
     * @see https://bootstrap-vue.org/docs/components/table
     */
    fields: {
      type: Array,
      required: true,
    },
    /**
     * CSS classes of the main container in the component.
     */
    classes: {
      type: String,
      default: "",
    },
    /**
     * @see https://bootstrap-vue.org/docs/components/table#table-busy-state
     */
    busy: {
      type: Boolean,
    },
    searchbarPlaceholder: {
      type: String,
      default: null,
    },
    /**
     * Allow exporting data as CSV.
     * When using an item provider, all data are fetched from source and exported.
     */
    allowExport: {
      type: Boolean,
      default: true,
    },
    /**
     * When exporting, use field `key` instead of `label` in the CSV header
     */
    exportColumnsByKey: {
      type: Boolean,
      default: false,
    },
    /**
     * The name of the file to export
     */
    exportFilename: {
      type: String,
      default: "data.csv",
    },
    providerPagination: {
      type: Object,
      default: null,
      validator(value) {
        return ["items", "pagination"].every((k) => k in value);
      },
    },
  },
  data() {
    return {
      context: {
        currentPage: 1,
        perPage: 10,
      },
      remotePagination: {},
      selectedFields: [],
      filter: "",
      downloading: false,
      searchByField: false,
    };
  },
  computed: {
    hasSearchableFields() {
      return this.fields.some((f) => f.searchable);
    },
    itemProvider() {
      if (this.hasItemsProvider) {
        return this.providerPagination
          ? async (ctx) => {
              const context = Object.fromEntries(
                Object.entries(ctx).filter(([_, v]) => v !== "")
              );
              if (context.filter instanceof RegExp) {
                context.filter = context.filter.source;
              } else if (context.filter?.fields) {
                context.filterop = context.filter?.logicalOr ? "OR" : "AND";
                context.filter = Object.fromEntries(
                  Object.entries(context.filter?.fields).map(
                    ([key, { _, term }]) => [key, term]
                  )
                );
              }
              let json = await this.items(context);
              this.remotePagination = json[this.providerPagination.pagination];
              return json[this.providerPagination.items];
            }
          : this.items;
      }
      return null;
    },
    hasItemsProvider() {
      return this.items instanceof Function;
    },
    exportedHeader() {
      return this.exportedFields.reduce(
        (header, field) => [
          ...header,
          field.unpacker
            ? Object.keys(field.unpacker(field))
            : field.export?.label ||
              (this.exportColumnsByKey ? field.key : field.label) ||
              field.key,
        ],
        []
      );
    },
    exportedFields() {
      return this.fields.filter((f) => !f.export?.exclude);
    },
    locale() {
      return this.$i18n.locale;
    },
    displayedItemRange() {
      const first = (this.context.currentPage - 1) * this.context.perPage + 1;
      const last = Math.min(this.totalItems, first + this.context.perPage - 1);
      return [first, last ? last : this.totalItems];
    },
    totalItems() {
      return this.hasItemsProvider
        ? this.remotePagination?.total_items
        : this.items.length;
    },
    visibleFields() {
      const visibleKeys = new Set(this.selectedFields.map((f) => f.key));
      return this.fields.filter((f) => visibleKeys.has(f.key));
    },
  },
  watch: {
    searchByField(isActive) {
      toggleFilters(isActive);
    },
    fields(newFields) {
      this.selectedFields = newFields;
    },
    items(newItems) {
      this.toggleFilters(this.hasItemsProvider);
    },
  },
  created() {
    this.toggleFilters(this.hasItemsProvider);
    if (this.selectedFields.length == 0)
      this.selectedFields = this.fields.filter((field) => field.visible);
  },
  methods: {
    toggleFilters(isActive) {
      this.filter = isActive ? { logicalOr: false, fields: {} } : "";
    },
    filterOnSearchFields(item, filter) {
      const filterEntries = [...Object.entries(filter.fields)].filter(
        ([key, { formatter, term }]) => term !== ""
      );
      if (filterEntries.length === 0) return true;

      function filterPass([key, { formatter, term }]) {
        const searchTarget = formatter
          ? formatter(item[key], key, item)
          : item[key];
        return new RegExp(`${term}`, "i").test(searchTarget);
      }
      return filter.logicalOr
        ? filterEntries.some(filterPass)
        : filterEntries.every(filterPass);
    },
    itemToCsv(item) {
      function getDeepValue(object, key) {
        return key
          .split(".")
          .reduce((val, key) => (val && val[key] ? val[key] : null), object);
      }
      return this.exportedFields
        .reduce((acc, field) => {
          /**
           * Attempt to get composite value with "[key].[export]"",
           * fallback on field key
           */
          let value = getDeepValue(item, field.key);
          if (field.unpacker) {
            acc = [
              ...acc,
              Object.values(field.unpacker(value)).map(JSON.stringify),
            ];
          } else {
            value = JSON.stringify(
              field.export?.formatter
                ? field.export.formatter(value)
                : field.formatter
                ? field.formatter(value)
                : value,
              (k, v) => (v === null ? "" : v)
            );
            acc = [...acc, value];
          }
          return acc;
        }, [])
        .join(",");
    },
    itemsToCsv(items) {
      return [this.exportedHeader.join(","), ...items.map(this.itemToCsv)].join(
        "\r\n"
      );
    },
    async downloadCSV() {
      if (!this.allowExport) {
        console.warn(
          "Blocked attempt to export CSV from BDataTable with allowExport prop set to false."
        );
        return false;
      }
      this.downloading = true;
      let items = this.items;
      if (this.items instanceof Function) {
        items = await this.itemProvider({ perPage: 0, currentPage: 1 });
      }
      const csv = this.itemsToCsv(items);
      const encodedUri = encodeURI("data:text/csv;charset=utf-8," + csv);
      let link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", this.exportFilename);
      link.click();
      this.downloading = false;
    },
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

  table.table.b-table {
    thead > tr.head-search-fields > th {
      border-bottom: none;
    }
    .column {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      max-width: 20em;
    }
  }

  .search-toolbar {
    display: flex;
    align-items: center;
    .vue-js-switch {
      margin-bottom: 0;
      margin-left: 10px;
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
}
</style>

<docs>
The main component to display data tables in Gotit.
Integrates components from [BootstrapVue](https://bootstrap-vue.org/).
</docs>
