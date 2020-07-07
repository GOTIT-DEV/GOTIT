<template>
  <div>
    <div class="form-group">
      <label class="control-label" for="taxname">{{ label }} </label>
      <select name="taxname" id="taxname" class="form-control" v-select-picker>
        <option
          v-for="taxname in taxname_set"
          :value="taxname.taxname"
          :key="taxname.taxname"
        >
          {{ taxname.taxname }}
        </option>
      </select>
    </div>
  </div>
</template>

<script>
import { SelectPicker } from "../directives/SelectPickerDirective";
import { mapState, mapMutations } from "vuex";

export default {
  directives: {
    SelectPicker
  },
  computed: {
    taxname: {
      get() { return this.$store.state.taxname},
      set(value) {this.setTaxname(value)}
    },
    ...mapState(["genus", "species"])
  },
  data() {
    return {
      url: Routing.generate("taxname-search"),
      label: Translator.trans("label.taxon"),
      taxname_set: [],
    };
  },
  watch: {
    species: function(newSp, oldSp) {
      if(newSp !== oldSp) this.searchTaxname();
    }
  },
  methods: {
    ...mapMutations(["setTaxname"]),
    searchTaxname() {
      fetch(this.url, {
        method: "POST",
        body: JSON.stringify({
          genus: this.genus,
          species: this.species
        }),
        credentials: "include",
        headers: { "Content-Type": "application/json" }
      })
        .then(response => response.json())
        .catch(response => {
          console.warn(`Error fetching taxons from URL : ${this.url}`);
          console.warn(response);
        })
        .then(json => {
          this.taxname_set = json;
          this.taxname = json[0].taxname;
        });
    }
  }
};
</script>

<style lang="less" scoped>
</style>