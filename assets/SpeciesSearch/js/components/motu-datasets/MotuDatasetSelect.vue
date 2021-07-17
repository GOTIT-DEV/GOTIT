<template>
  <div class="select-container">
    <b-form-group :label="$t('queries.label.dataset')" label-for="dataset">
      <b-skeleton-wrapper :loading="loading">
        <template #loading>
          <b-skeleton type="input" width="100%"></b-skeleton>
        </template>
        <form-multiselect
          name="dataset"
          trackBy="id"
          v-model="dataset"
          :options="motuList"
          label="name"
          :allowEmpty="false"
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
          <b-skeleton type="input" width="100%"></b-skeleton>
        </template>
        <form-multiselect
          :options="methodList"
          v-model="methods"
          :name="multiple ? 'methods[]' : 'methods'"
          :multiple="multiple"
          :searchable="false"
          label="method_code"
          trackBy="method_id"
          :allowEmpty="false"
          @select="$emit('update:methods', $event)"
        />
      </b-skeleton-wrapper>
    </b-form-group>
  </div>
</template>

<script>
import { SelectPicker } from "../directives/SelectPickerDirective";
import FormMultiselect from "~Components/FormMultiselect";

export default {
  components: { FormMultiselect },
  directives: {
    SelectPicker,
  },
  props: {
    multiple: {
      type: Boolean,
      default: false,
    },
  },
  created() {
    this.ready = this.fetch();
  },
  data() {
    return {
      url: Routing.generate("methods-list"),
      ready: false,
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
  methods: {
    async fetch() {
      const response = await fetch(this.url);
      return response.json().then((json) => {
        this.motuMethodList = json;
        this.dataset = this.motuList[0];
        this.loading = false;
        this.$emit("update:motuList", this.motuList);
      });
    },
  },
  watch: {
    dataset: function (newDataset, oldDataset) {
      this.methods = this.multiple ? this.methodList : this.methodList[0];
    },
  },
};
</script>

<style lang="less" scoped>
</style>