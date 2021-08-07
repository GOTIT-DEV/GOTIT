<template>
  <div class="select-container">
    <b-form-group :label="$t('queries.label.dataset')" label-for="dataset">
      <b-skeleton-wrapper :loading="loading">
        <template #loading>
          <b-skeleton type="input" width="100%" />
        </template>
        <form-multiselect
          v-model="dataset"
          name="dataset"
          track-by="id"
          :options="motuList"
          label="name"
          :allow-empty="false"
          required
          :searchable="false"
          @select="$emit('update:dataset', $event)"
        />
      </b-skeleton-wrapper>
    </b-form-group>
    <b-form-group
      :label="$t('queries.methode.label')"
      label-for="methods-select"
    >
      <b-skeleton-wrapper :loading="loading">
        <template #loading>
          <b-skeleton type="input" width="100%" />
        </template>
        <form-multiselect
          v-model="methods"
          :options="methodList"
          :name="multiple ? 'methods[]' : 'methods'"
          :multiple="multiple"
          :searchable="false"
          label="method_code"
          track-by="method_id"
          :allow-empty="false"
          @select="$emit('update:methods', $event)"
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
    multiple: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      url: Routing.generate("methods-list"),
      loading: true,
      motuMethodList: [],
      dataset: undefined,
      methods: this.multiple ? [] : undefined,
    };
  },
  computed: {
    motuList() {
      let uniqueDatasets = this.motuMethodList.reduce((acc, record) => {
        acc[record.dataset_id] = {
          id: record.dataset_id,
          name: record.dataset_name,
        };
        return acc;
      }, {});
      return Object.values(uniqueDatasets);
    },
    methodList() {
      let methods = this.motuMethodList.filter(
        ({ dataset_id }) => dataset_id == this.dataset.id
      );
      return methods;
    },
  },
  watch: {
    dataset: function (newDataset, oldDataset) {
      this.methods = this.multiple ? this.methodList : this.methodList[0];
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
      return response.json().then((json) => {
        this.motuMethodList = json;
        this.dataset = this.motuList[0];
        this.loading = false;
        this.isInitialized = true;
        this.$emit("update:motuList", this.motuList);
        return true;
      });
    },
  },
};
</script>

<style lang="less" scoped></style>
