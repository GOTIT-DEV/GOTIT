<template>
  <div class="form-component">
    <!-- <slot> </slot> -->
    <TogglablePanel class="species-select" title="label.search.espece">
      <TaxonomySelect> </TaxonomySelect>
      <template v-slot:footer>
        <ButtonLoading ref="submit" v-bind:loading="loading" @click="submit">
          Search
        </ButtonLoading>
      </template>
    </TogglablePanel>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";
// import { createNamespacedHelpers } from 'vuex'
// const { mapState, mapMutations } = createNamespacedHelpers('taxonomy')

import ButtonLoading from "./ButtonLoading";
import TogglablePanel from "./taxonomy/TogglablePanel";
import TaxonomySelect from "./taxonomy/TaxonomySelect";

export default {
  components: {
    TogglablePanel,
    TaxonomySelect,
    ButtonLoading
  },
  computed: {
    ...mapState(["loading"])
  },
  methods: {
    ...mapMutations(["setLoading"]),

    submit() {
      this.setLoading(true);
      this.$emit("submit");
    },

    received() {
      this.setLoading(false);
    }
  }
};
</script>

<style lang="less" scoped>
</style>