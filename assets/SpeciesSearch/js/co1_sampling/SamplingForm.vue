<template>
  <form id="main-form" ref="form" action="#" @submit.prevent="submit()">
    <TogglablePanel class="species-select" title="queries.label.search.espece">
      <TaxonomySelect ref="taxonomy"> </TaxonomySelect>
      <template v-slot:footer>
        <ButtonLoading ref="submit" v-bind:loading="loading" block>
          {{ $t("ui.search") }}
        </ButtonLoading>
      </template>
    </TogglablePanel>
  </form>
</template>

<script>
// Components
import ButtonLoading from "../../../components/ButtonLoading";
import TogglablePanel from "../components/TogglablePanel";
import TaxonomySelect from "../components/taxonomy/TaxonomySelect";
import i18n from "../i18n";

export default {
  i18n,
  components: {
    TogglablePanel,
    TaxonomySelect,
    ButtonLoading,
  },
  data() {
    return {
      loading: true,
      url: Routing.generate("co1-sampling-query"),
    };
  },
  computed: {
    ready() {
      return Promise.all([this.$refs.taxonomy.ready]);
    },
  },
  methods: {
    async submit() {
      this.loading = true;
      const response = await fetch(this.url, {
        method: "POST",
        body: new FormData(this.$refs.form),
      });
      const data = await response.json();
      this.loading = false;
      this.$emit("update:results", data);
      return data;
    },
  },
};
</script>

<style lang="less" scoped>
</style>