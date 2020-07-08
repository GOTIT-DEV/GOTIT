<template>
  <div class="form-group">
    <label class="control-label" for="genus">{{ label }}</label>
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
        v-for="genus in genus_set"
        :value="genus.genus"
        :key="genus.genus"
      >
        {{ genus.genus }}
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
    genus: {
      get() {return this.$store.state.taxonomy.genus},
      set(value) {
        return this.setGenus(value);
      }
    }
  },
  data() {
    return {
      url: Routing.generate("genus-list"),
      label: Translator.trans("label.genre"),
      genus_set: []
    };
  },
  created() {
    fetch(this.url)
      .then(response => response.json())
      .catch(response => {
        console.warn(`Error fetching genus from URL : ${this.url}`);
        console.warn(response);
      })
      .then(json => {
        this.genus_set = json;
        this.setGenus(json[0].genus);
      });
  },
  methods: {
    ...mapMutations(["setGenus"])
  }
};
</script>


<style scoped>
</style>