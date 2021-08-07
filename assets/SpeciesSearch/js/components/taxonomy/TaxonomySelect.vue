<template>
  <div class="taxonomy-select">
    <!-- Genus select -->
    <b-form-group :label="$t('queries.label.genre')" label-for="genus">
      <b-skeleton-wrapper :loading="loading">
        <template #loading>
          <b-skeleton type="input" />
        </template>
        <form-multiselect
          v-model="genus"
          :options="genusList"
          name="genus"
          :disabled="disabled"
          :allow-empty="false"
          @select="$emit('update:genus', $event)"
        />
      </b-skeleton-wrapper>
    </b-form-group>

    <!-- Species select -->
    <b-form-group :label="$t('queries.label.espece')" label-for="species">
      <b-skeleton-wrapper :loading="loading">
        <template #loading>
          <b-skeleton type="input" />
        </template>
        <form-multiselect
          v-model="species"
          :options="speciesList"
          name="species"
          :disabled="disabled"
          :allow-empty="false"
          @select="$emit('update:species', $event)"
        />
      </b-skeleton-wrapper>
    </b-form-group>
    <!-- Taxname select -->
    <b-form-group
      v-if="withTaxname"
      :label="$t('queries.label.taxon')"
      label-for="taxname"
    >
      <b-skeleton-wrapper :loading="loading">
        <template #loading>
          <b-skeleton type="input" />
        </template>
        <form-multiselect
          v-model="taxname"
          :options="taxnameList"
          track-by="id"
          label="taxname"
          name="taxname"
          :disabled="disabled"
          :allow-empty="false"
          @select="$emit('update:taxname', $event)"
        />
      </b-skeleton-wrapper>
    </b-form-group>
  </div>
</template>

<script>
import FormMultiselect from "~Components/FormMultiselect";

export default {
  components: { FormMultiselect },
  props: {
    withTaxname: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      url: Routing.generate("species-list"),
      loading: true,
      taxonomy: [],
      genus: undefined,
      species: undefined,
      taxname: undefined,
    };
  },
  computed: {
    genusList() {
      return [...new Set(this.taxonomy.map((taxon) => taxon.genus))];
    },
    speciesList() {
      let species = this.taxonomy
        .filter(({ genus }) => genus === this.genus)
        .map((taxon) => taxon.species);
      return [...new Set(species)];
    },
    taxnameList() {
      let taxnames = this.taxonomy.filter(
        ({ genus, species }) => species == this.species && genus == this.genus
      );
      return [...new Set(taxnames)];
    },
  },
  watch: {
    genus: function (newVal, oldVal) {
      this.species = this.speciesList[0];
    },
    species: function (newVal, oldVal) {
      if (this.withTaxname) this.taxname = this.taxnameList[0];
    },
  },
  created() {
    this.isInitialized = false;
  },
  methods: {
    async init() {
      return this.isInitialized ? Promise.resolve(true) : this.fetch();
    },
    async fetch() {
      const response = await fetch(this.url);
      return response.json().then((data) => {
        this.taxonomy = data;
        this.genus = this.taxonomy[0].genus;
        this.loading = false;
        this.isInitialized = true;
        return true;
      });
    },
  },
};
</script>

<style lang="less" scoped>
.taxonomy-select {
  min-width: 220px;
}
</style>
