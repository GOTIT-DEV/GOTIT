<template>
  <div class="taxonomy-select" v-bind:class="{ loading: loading }">
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
          <option
            v-for="genus in genus_data"
            :value="genus.genus"
            :key="genus.genus"
          >
            {{ genus.genus }}
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
            v-for="species in species_data"
            :value="species.species"
            :key="species.species"
          >
            {{ species.species }}
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
            v-for="taxname in taxname_data"
            :value="taxname.taxname"
            :key="taxname.taxname"
          >
            {{ taxname.taxname }}
          </option>
        </select>
      </div>
    </div>
    <i class="fas fa-spinner fa-spin" id="taxon-spinner"> </i>
  </div>
</template>



<script>
import GenusSelect from "./GenusSelect";
import SpeciesSelect from "./SpeciesSelect";
import TaxnameSelect from "./TaxnameSelect";
import { createNamespacedHelpers } from "vuex";
const { mapState, mapMutations, mapActions } = createNamespacedHelpers(
  "taxonomy"
);
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
    genus: {
      get() {
        return this.$store.state.taxonomy.genus;
      },
      set(value) {
        this.setGenus(value);
      }
    },
    species: {
      get() {
        return this.$store.state.taxonomy.species;
      },
      set(value) {
        this.setSpecies(value);
      }
    },
    taxname: {
      get() {
        return this.$store.state.taxonomy.taxname;
      },
      set(value) {
        this.setTaxname(value);
      }
    },
    ...mapState([
      "genus_data",
      "species_data",
      "taxname_data",
      "loading",
      "ready"
    ])
  },
  data() {
    return {
      labels: {
        genus: Translator.trans("label.genre"),
        species: Translator.trans("label.espece"),
        taxname: Translator.trans("label.taxon")
      }
    };
  },
  methods: {
    ...mapActions([
      "fetchGenusSet",
      "fetchSpeciesSet",
      "fetchTaxnameSet",
      "init"
    ]),
    ...mapMutations(["setGenus", "setSpecies", "setTaxname"])
  },
  watch: {
    genus: function(newVal, oldVal) {
      this.fetchSpeciesSet();
    },
    species: function(newVal, oldVal) {
      if (this.withTaxname) this.fetchTaxnameSet();
    }
  },
  created() {
    this.init();
  }
};
</script>



<style lang="less" scoped>
.taxonomy-select {
  display: grid;
  grid-template-areas: "area";
  align-items: center;
  > * {
    grid-area: area;
  }
  > .select-container {
    width: 100%;
  }
  > .fa-spinner {
    width: fit-content;
    font-size: 50px;
    justify-self: center;
    visibility: hidden;
  }

  &.loading {
    > .select-container {
      visibility: hidden;
    }
    > .fa-spinner {
      visibility: visible;
    }
  }
}
</style>