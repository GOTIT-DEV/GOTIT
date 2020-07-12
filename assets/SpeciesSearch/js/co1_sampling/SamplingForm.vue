<template>
  <TogglablePanel class="species-select" title="label.search.espece">
    <TaxonomySelect ref="taxonomy"> </TaxonomySelect>
    <template v-slot:footer>
      <ButtonLoading ref="submit" v-bind:loading="loading" @click="submit">
        Search
      </ButtonLoading>
    </template>
  </TogglablePanel>
</template>

<script>
// Components
import ButtonLoading from "../components/ButtonLoading";
import TogglablePanel from "../components/TogglablePanel";
import TaxonomySelect from "../components/taxonomy/TaxonomySelect";

export default {
  components: {
    TogglablePanel,
    TaxonomySelect,
    ButtonLoading
  },
  data() {
    return {
      loading: true
    }
  },
  computed: {
    ready() {
      return Promise.all([this.$refs.taxonomy.ready]);
    }
  },
  methods: {
    submit(){
      this.loading = true
    }
  }
};
</script>

<style lang="less" scoped>
</style>