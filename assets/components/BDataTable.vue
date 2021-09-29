<template>
  <div class="table-container" :class="classes">
    <b-button-toolbar :justify="true" class="mb-1">
      <div class="d-flex">
        <b-form-group label-cols="auto" :label="$t('pageLength')" class="mb-0">
          <b-form-select v-model="pagination.perPage" size="sm">
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
                :value="searchTermAsString"
                @update="searchTerm = new RegExp($event, 'i')"
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
          @change="$set(fieldSearchTerms, 'logicalOr', $event.value)"
        />
        <b-button
          size="sm"
          variant="light"
          class="ml-1 border"
          @click="resetSearchTerms"
        >
          Clear
          <i class="fas fa-undo text-secondary" />
        </b-button>
      </div>
    </b-button-toolbar>
    <slot name="toolbar-top" />
    <b-overlay :show="busy" rounded="sm">
      <b-table
        ref="table"
        responsive
        striped
        :busy="busy"
        primary-key="code"
        :fields="visibleFields"
        :items="hasItemsProvider ? itemProvider : items"
        :per-page="pagination.perPage"
        :current-page="pagination.currentPage"
        :filter="searchByField ? fieldSearchTerms : searchTerm"
        v-bind="$attrs"
        :filter-function="searchByField ? filterOnSearchFields : undefined"
        @filtered="localFilteredItems = $event"
      >
        <template
          v-for="slotName in Object.keys($scopedSlots)"
          #[slotName]="slotScope"
        >
          <slot :name="slotName" v-bind="slotScope" />
        </template>
        <template #thead-top>
          <b-tr
            v-show="hasItemsProvider || searchByField"
            class="head-search-fields"
          >
            <!-- v-show="visibleFields.some((vf) => vf.key === f.key)" -->
            <b-th v-for="f in visibleFields" :key="f.key" class="p-1">
              <b-input
                v-if="f.searchable"
                size="sm"
                placeholder="Search term"
                debounce="500"
                :value="fieldSearchTerms.fields[f.key].term"
                @update="
                  $set(fieldSearchTerms.fields, f.key, {
                    formatter: f.formatter,
                    searchKey: f.searchKey,
                    term: $event,
                  })
                "
              />
            </b-th>
          </b-tr>
        </template>
      </b-table>
    </b-overlay>
    <slot name="toolbar-bottom" />
    <b-button-toolbar :justify="true">
      <div>
        <button-loading
          size="sm"
          variant="light"
          class="border mr-1"
          :disabled="!allowExport || !totalItems || busy"
          :loading="downloading"
          @click="downloadCSV()"
        >
          <i class="fas fa-download" />
          {{ $t("exportCSV") }}
        </button-loading>
        <b-checkbox v-model="exportFiltered" size="sm" inline>
          Filter
        </b-checkbox>
      </div>
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
        v-model="pagination.currentPage"
        :total-rows="totalItems"
        :per-page="pagination.perPage"
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
import ExportCsvMixin from "./mixins/ExportCsvMixin";
export default {
  name: "BDataTable",
  components: {
    FadeTransition,
    CollapseTransition,
    ToggleButton,
    Multiselect,
    ButtonLoading,
  },
  mixins: [ExportCsvMixin],
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
  },
  data() {
    return {
      pagination: {
        currentPage: 1,
        perPage: 10,
      },
      remotePagination: {},
      selectedFields: [],
      downloading: false,
      searchByField: false,
      searchTerm: new RegExp("", "i"),
      fieldSearchTerms: {
        logicalOr: false,
        fields: {},
      },
      localFilteredItems: [],
      exportFiltered: true,
    };
  },
  computed: {
    hasSearchableFields() {
      return this.fields.some((f) => f.searchable);
    },
    hasItemsProvider() {
      return this.items instanceof Function;
    },
    searchTermAsString() {
      return this.searchTerm.source === "(?:)" ? "" : this.searchTerm.source;
    },
    itemProvider() {
      return this.hasItemsProvider
        ? async (ctx) => {
            const query = this.queryOfLocalContext(ctx);
            let json = await this.items(query);
            let { pagination, items } = this.hydraAccessor(json);
            this.remotePagination = pagination;
            return items;
          }
        : this.items;
    },
    locale() {
      return this.$i18n.locale;
    },
    displayedItemRange() {
      const first =
        (this.pagination.currentPage - 1) * this.pagination.perPage + 1;
      const last = Math.min(
        this.totalItems,
        first + this.pagination.perPage - 1
      );
      return [first, last ? last : this.totalItems];
    },
    totalItems() {
      return this.hasItemsProvider
        ? this.remotePagination?.total_items
        : this.localFilteredItems.length;
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
    items(newItems) {
      this.resetSearchTerms();
    },
  },
  created() {
    this.resetSearchTerms();
    this.searchByField = this.hasItemsProvider;

    if (this.selectedFields.length == 0)
      this.selectedFields = this.fields.filter((field) => field.visible);
  },
  methods: {
    hydraAccessor(json) {
      return {
        pagination: { total_items: json["hydra:totalItems"] },
        items: json["hydra:member"],
      };
    },
    queryOfLocalContext(ctx) {
      const {
        perPage: itemsPerPage,
        currentPage: page,
        sortBy,
        sortDesc,
        pagination = true,
      } = ctx;
      let query = {
        itemsPerPage,
        page,
        pagination,
        order: sortBy ? { [sortBy]: sortDesc ? "desc" : "asc" } : undefined,
      };
      const context = Object.fromEntries(
        Object.entries(ctx).filter(([_, v]) => v !== "")
      );
      if (context.filter instanceof RegExp) {
        context.filter = context.filter.source;
      } else if (context.filter?.fields) {
        const search_query = Object.fromEntries(
          Object.entries(context.filter.fields)
            .map(([key, { _, term, searchKey }]) => [
              searchKey ? `${key}.${searchKey}` : key,
              term,
            ])
            .filter(([key, term]) => term)
        );
        if (context.filter.logicalOr) {
          query.searchOr = search_query;
        } else {
          query = { ...query, ...search_query };
        }
      }
      return query;
    },
    resetSearchTerms() {
      this.fieldSearchTerms = {
        logicalOr: false,
        fields: Object.fromEntries(
          this.fields
            .filter((f) => f.searchable)
            .map(({ key, formatter, searchKey }) => [
              key,
              { formatter, searchKey, term: "" },
            ])
        ),
      };
      this.searchTerm = new RegExp("", "i");
    },
    /**
     * Local filter function searching terms per field,
     * passed to inner BTable component
     * @see https://bootstrap-vue.org/docs/components/table#custom-filter-function
     */
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
    async downloadCSV() {
      if (!this.allowExport) {
        console.warn(
          "Blocked attempt to export CSV from BDataTable with allowExport prop set to false."
        );
        return false;
      }
      this.downloading = true;
      let items = null;
      if (this.items instanceof Function) {
        const currentPagination = this.remotePagination;
        items = await this.itemProvider({
          pagination: false,
          filter: this.exportFiltered ? this.fieldSearchTerms : undefined,
        });
        this.remotePagination = currentPagination;
      } else {
        items = this.exportFiltered ? this.localFilteredItems : this.items;
      }
      return this.downloadItemsCSV(items);
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
