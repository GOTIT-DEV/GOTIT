<template>
  <div class="taxonomy-select" v-bind:class="{ loading: loading }">
    <div class="select-container">
      <GenusSelect />
      <SpeciesSelect />
      <TaxnameSelect v-if="withTaxname" />
    </div>
    <i class="fas fa-spinner fa-spin" id="taxon-spinner"> </i>
  </div>
</template>



<script>
import GenusSelect from "./GenusSelect";
import SpeciesSelect from "./SpeciesSelect";
import TaxnameSelect from "./TaxnameSelect";
import { createNamespacedHelpers } from 'vuex'
const { mapState, mapMutations } = createNamespacedHelpers('taxonomy')

export default {
  components: {
    GenusSelect,
    SpeciesSelect,
    TaxnameSelect
  },
  props: {
    withTaxname: {
      type: Boolean,
      default: false
    }
  },
  computed: mapState(["genus", "species", "taxname", "loading", "ready"]),
  methods: {
    ...mapMutations(["setLoading"]),
    toggleActive(state) {
      $(this.$el)
        .find(":input")
        .prop("disabled", !state);
    }
  },
  watch: {
    taxname: function(newVal, oldVal) {
      this.setLoading(false)
    },
    species: function(newVal, oldVal) {
      this.setLoading(!this.withTaxname)
    }
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