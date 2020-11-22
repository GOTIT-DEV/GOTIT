<template>
  <div
    class="taxonomy-select requires-loading"
    v-bind:class="{ loading: loading }"
  >
    <div class="select-container">
      <!-- Genus select -->
      <b-form-group :label="$t('queries.label.genre')" label-for="genus">
        <select
          name="genus"
          id="genus"
          class="form-control"
          data-live-search="true"
          data-size="10"
          v-model="genus"
          v-select-picker
          :disabled="disabled"
          @change="$emit('update:genus', $event)"
        >
          <option v-for="genus in genusList" :value="genus" :key="genus">
            {{ genus }}
          </option>
        </select>
      </b-form-group>
      <!-- Species select -->
      <b-form-group :label="$t('queries.label.espece')" label-for="species">
        <select
          name="species"
          id="species"
          class="form-control"
          data-live-search="true"
          data-size="10"
          v-model="species"
          v-select-picker
          :disabled="disabled"
          @change="$emit('update:species', $event)"
        >
          <option
            v-for="species in speciesList"
            :value="species"
            :key="species"
          >
            {{ species }}
          </option>
        </select>
      </b-form-group>
      <!-- Taxname select -->
      <b-form-group
        v-if="withTaxname"
        :label="$t('queries.label.taxon')"
        label-for="taxname"
      >
        <select
          name="taxname"
          id="taxname"
          class="form-control"
          v-select-picker
          :disabled="disabled"
          @change="$emit('update:taxname', $event)"
        >
          <option
            v-for="taxon in taxnameList"
            :value="taxon.id"
            :key="taxon.id"
          >
            {{ taxon.taxname }}
          </option>
        </select>
      </b-form-group>
    </div>
    <i class="fas fa-spinner fa-spin"> </i>
  </div>
</template>



<script>
import { SelectPicker } from "../directives/SelectPickerDirective";
import i18n from "../../i18n";

export default {
  i18n,
  directives: {
    SelectPicker,
  },
  props: {
    withTaxname: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    genusList() {
      return [...new Set(this.taxonomy.map((taxon) => taxon.genus))];
    },
    speciesList() {
      let species = this.taxonomy
        .filter(({ genus }) => genus === this.genus)
        .map((taxon) => taxon.species);
      return [...new Set(species)];
    },
    taxnameList() {
      let taxnames = this.taxonomy.filter(
        ({ genus, species }) => species == this.species && genus == this.genus
      );
      return [...new Set(taxnames)];
    },
  },
  data() {
    return {
      url: Routing.generate("species-list"),
      ready: false,
      loading: true,
      taxonomy: [],
      genus: undefined,
      species: undefined,
    };
  },
  methods: {
    fetch() {
      this.ready = fetch(this.url)
        .then((response) => response.json())
        .then((json) => {
          this.taxonomy = json;
          this.genus = this.taxonomy[0].genus;
          this.loading = false;
        });
      return this.ready;
    },
  },
  watch: {
    genus: function (newVal, oldVal) {
      this.species = this.speciesList[0];
    },
    species: function (newVal, oldVal) {
      if (this.withTaxname) this.taxname = this.taxnameList[0];
    },
  },
  created() {
    this.fetch();
  },
};
</script>



<style lang="less" scoped>
@import "../loading.less";
.taxonomy-select {
  min-width: 220px;
}
</style>