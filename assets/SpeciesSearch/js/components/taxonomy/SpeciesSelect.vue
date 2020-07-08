<template>
  <div class="form-group">
    <label class="control-label" for="species">{{ label }}</label>
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
        v-for="species in species_set"
        :value="species.species"
        :key="species.species"
      >
        {{ species.species }}
      </option>
    </select>
  </div>
</template>

<script>
import { createNamespacedHelpers } from 'vuex'
const { mapState, mapMutations } = createNamespacedHelpers('taxonomy')
import { SelectPicker } from "../directives/SelectPickerDirective";

export default {
  directives: {
    SelectPicker
  },
  computed: {
    ...mapState(['genus']),
    species: {
      set(value) {
        this.setSpecies(value);
      },
      get() {
        return this.$store.state.taxonomy.species;
      }
    }
  },
  data() {
    return {
      url: Routing.generate("species-in-genus"),
      label: Translator.trans("label.espece"),
      species_set: []
    };
  },
  watch: {
    genus: function(newGenus, oldGenus) {
      if (newGenus !== oldGenus) this.fetchSpecies();
    }
  },
  methods: {
    ...mapMutations(["setSpecies"]),
    fetchSpecies() {
      fetch(this.url, {
        method: "POST",
        body: JSON.stringify({ genus: this.genus }),
        credentials: "include",
        headers: { "Content-Type": "application/json" }
      })
        .then(response => response.json())
        .catch(response => {
          console.warn(`Error fetching species from URL : ${this.url}`);
          console.warn(response);
        })
        .then(json => {
          this.species_set = json;
          this.species = json[0].species;
        });
    }
  }
};
</script>

<style scoped>
</style>