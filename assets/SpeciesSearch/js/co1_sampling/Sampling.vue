<template>
  <div>
    <h2>
      {{ $t("queries.label.search.param") }}
    </h2>
    <b-row align-h="center">
      <b-col cols="3" class="mb-3">
        <sampling-form ref="form" @update:results="items = $event" />
      </b-col>
      <b-col cols="12" xl="9">
        <sampling-table :items="items" @update:selection="fetchSites($event)" />
      </b-col>
    </b-row>
    <hr>

    <b-row class="mt-5">
      <b-col>
        <h2>
          {{ $t("sampling") }}
          <span v-if="selected.taxon_name">
            :
            <small class="text-monospace">{{ selected.taxon_name }}</small>
          </span>
        </h2>
        <sampling-details :item="selected" :sites="sites" />
      </b-col>
    </b-row>
  </div>
</template>

<i18n>
{
  "en": {
    "sampling": "Sampling"
  },
  "fr": {
    "sampling": "Échantillonnage"
  }
}
</i18n>

<script>
import SamplingForm from "./SamplingForm";
import SamplingTable from "./SamplingTable";
import SamplingDetails from "./SamplingDetails";

export default {
  components: { SamplingForm, SamplingTable, SamplingDetails },
  data() {
    return {
      items: [],
      selected: {},
      sites: [],
    };
  },
  async mounted() {
    const form = this.$refs.form;
    await form.init();
    await form.submit();
  },
  methods: {
    async fetchSites(item) {
      this.selected = item;
      if (item.id) {
        const response = await fetch(
          Routing.generate("co1-species-sampling", { id: item.id }),
          {
            method: "GET",
            credentials: "include",
          }
        );
        const data = await response.json();
        this.sites = data.sites;
      } else {
        this.sites = [];
      }
    },
  },
};
</script>

<style lang="less" scoped></style>
