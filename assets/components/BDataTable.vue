<template>
  <div class="table-container" :class="classes">
    <b-button-toolbar :justify="true" class="mb-1">
      <b-form-group label-cols="auto" :label="$t('pageLength')" class="mb-0">
        <b-form-select v-model="perPage" size="sm">
          <b-form-select-option value="10">10</b-form-select-option>
          <b-form-select-option value="25">25</b-form-select-option>
          <b-form-select-option value="50">50</b-form-select-option>
          <b-form-select-option value="100">100</b-form-select-option>
        </b-form-select>
      </b-form-group>
      <b-form-group
        label-cols="auto"
        :label="$tc('field', 2)"
        class="fields-select mb-0 w-25"
      >
        <multiselect
          multiple
          v-model="selectedFields"
          :close-on-select="false"
          :options="fields"
          track-by="key"
          :searchable="false"
          placeholder="Fields"
          :selectedLabel="null"
          :showLabels="false"
          :optionHeight="30"
          :allowEmpty="false"
          :maxHeight="400"
        >
          <template slot="tag">{{ "" }}</template>
          <template slot="selection" slot-scope="{ values, isOpen }">
            <span class="multiselect__single" v-if="values.length && !isOpen">
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
              ></i>
            </div>
          </template>
        </multiselect>
      </b-form-group>

      <div class="search-bar">
        <i class="fas fa-search icon"></i>
        <b-input-group>
          <b-input
            class="search-bar-input"
            type="text"
            size="sm"
            :placeholder="$t('search')"
            @input="filter = new RegExp($event, 'i')"
          />
        </b-input-group>
      </div>
    </b-button-toolbar>

    <b-table
      responsive
      striped
      primary-key="code"
      :fields="visibleFields"
      :items="items"
      :per-page="perPage"
      :current-page="tablePage"
      sort-by="id"
      :filter="filter"
    >
      <template
        v-for="slotName in Object.keys($scopedSlots)"
        v-slot:[slotName]="slotScope"
      >
        <slot :name="slotName" v-bind="slotScope"></slot>
      </template>
    </b-table>

    <b-button-toolbar :justify="true">
      <b-button v-if="allowExport" size="sm" variant="light" class="border">
        <i class="fas fa-download"></i>
        {{ $t("exportCSV") }}
      </b-button>
      <span>
        {{
          $t("pagePosition", {
            first: displayedItemRange[0],
            last: displayedItemRange[1],
            total: rows,
          })
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
export default {
  name: "BDataTable",
  components: {
    Multiselect,
  },
  computed: {
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
  created() {
    if (this.selectedFields.length == 0) this.selectedFields = this.fields;
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
    "pagePosition": "Showing {first} to {last} out of {total} items"
  },
  "fr": {
    "exportCSV": "Exporter CSV",
    "pageLength": "Afficher",
    "search": "Rechercher",
    "field": "Champ | Champs",
    "visible" : "visible | visibles",
    "pagePosition": "Éléments {first} à {last} sur {total}"
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