<template>
  <form id="main-form" ref="form" action="#" @submit.prevent="submit()">
    <TogglablePanel class="species-select" title="queries.label.search.espece">
      <TaxonomySelect ref="taxonomy" />
      <template #footer>
        <ButtonLoading ref="submit" type="submit" :loading="loading" block>
          {{ $t("ui.search") }}
        </ButtonLoading>
      </template>
    </TogglablePanel>
  </form>
</template>

<script>
// Components
import ButtonLoading from "~Components/ButtonLoading";
import TogglablePanel from "~SpeciesSearch/js/components/TogglablePanel";
import TaxonomySelect from "~SpeciesSearch/js/components/taxonomy/TaxonomySelect";

export default {
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
  methods: {
    async init() {
      return Promise.all([this.$refs.taxonomy.init()]);
    },
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

<style lang="less" scoped></style>
