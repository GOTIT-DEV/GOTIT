<template>
  <div
    class="taxonomy-select requires-loading"
    v-bind:class="{ loading: loading }"
  >
    <div class="select-container">
      <!-- Genus select -->
      <div class="form-group">
        <label class="control-label" for="genus">{{ labels.genus }}</label>
        <select
          name="genus"
          id="genus"
          class="form-control"
          data-live-search="true"
          data-size="10"
          v-model="genus"
          v-select-picker
        >
          <option v-for="genus in genusList" :value="genus" :key="genus">
            {{ genus }}
          </option>
        </select>
      </div>
      <!-- Species select -->
      <div class="form-group">
        <label class="control-label" for="species">{{ labels.species }}</label>
        <select
          name="species"
          id="species"
          class="form-control"
          data-live-search="true"
          data-size="10"
          v-model="species"
          v-select-picker
        >
          <option
            v-for="species in speciesList"
            :value="species"
            :key="species"
          >
            {{ species }}
          </option>
        </select>
      </div>
      <!-- Taxname select -->
      <div class="form-group" v-if="withTaxname">
        <label class="control-label" for="taxname">{{ labels.taxname }} </label>
        <select
          name="taxname"
          id="taxname"
          class="form-control"
          v-select-picker
        >
          <option
            v-for="taxname in taxnameList"
            :value="taxname"
            :key="taxname"
          >
            {{ taxname }}
          </option>
        </select>
      </div>
    </div>
    <i class="fas fa-spinner fa-spin"> </i>
  </div>
</template>



<script>
import { SelectPicker } from "../directives/SelectPickerDirective";

export default {
  directives: {
    SelectPicker
  },
  props: {
    withTaxname: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    genusList() {
      return [...new Set(this.taxonomy.map(taxon => taxon.genus))];
    },
    speciesList() {
      let species = this.taxonomy
        .filter(({ genus }) => genus === this.genus)
        .map(taxon => taxon.species);
      return [...new Set(species)];
    },
    taxnameList() {
      let taxnames = this.taxonomy
        .filter(
          ({ genus, species }) => species == this.species && genus == this.genus
        )
        .map(taxon => taxon.taxname);
      return [...new Set(taxnames)];
    }
  },
  data() {
    return {
      url: Routing.generate("species-list"),
      ready: false,
      loading: true,
      taxonomy: [],
      genus: undefined,
      species: undefined,
      labels: {
        genus: Translator.trans("label.genre"),
        species: Translator.trans("label.espece"),
        taxname: Translator.trans("label.taxon")
      }
    };
  },
  methods: {
    fetch() {
      this.ready = fetch(this.url)
        .then(response => response.json())
        .then(json => {
          this.taxonomy = json;
          this.genus = this.taxonomy[0].genus;
          this.loading = false;
        });
      return this.ready;
    }
  },
  watch: {
    genus: function(newVal, oldVal) {
      this.species = this.speciesList[0];
    },
    species: function(newVal, oldVal) {
      if (this.withTaxname) this.taxname = this.taxnameList[0];
    }
  },
  created() {
    this.fetch();
  }
};
</script>



<style lang="less" scoped>
@import "../loading.less";
.taxonomy-select {
  min-width: 220px;
}
</style>