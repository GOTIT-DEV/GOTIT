<template>
  <div class="motu-select requires-loading" v-bind:class="{ loading: loading }">
    <div class="select-container">
      <b-form-group :label="$t('queries.label.dataset')" label-for="dataset">
        <select
          v-model="dataset"
          name="dataset"
          id="dataset"
          class="form-control"
          v-select-picker
          @change="$emit('update:dataset', $event)"
        >
          <option v-for="motu in motuList" :value="motu.id" :key="motu.id">
            {{ motu.name }}
          </option>
        </select>
      </b-form-group>
      <b-form-group
        :label="$t('queries.methode.label')"
        label-for="methods-select"
      >
        <select
          v-bind:multiple="multiple"
          v-model="methods"
          :name="methodInputName"
          id="methods-select"
          class="form-control"
          v-select-picker
          @change="$emit('update:methods', $event)"
        >
          <option
            v-for="method in methodList"
            :value="method.method_id"
            :key="method.method_id"
          >
            {{ method.method_code }}
          </option>
        </select>
      </b-form-group>
    </div>
    <i class="fas fa-spinner fa-spin"> </i>
  </div>
</template>

<script>
// import { createNamespacedHelpers } from "vuex";
// const { mapState, mapMutations, mapActions } = createNamespacedHelpers("motu");
import { SelectPicker } from "../directives/SelectPickerDirective";
import i18n from "../../i18n";
export default {
  i18n,
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
    this.fetch();
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
    methodInputName() {
      return this.multiple ? "methods[]" : "methods";
    },
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
        ({ dataset_id }) => dataset_id == this.dataset
      );
      return methods;
    },
  },
  methods: {
    fetch() {
      this.ready = fetch(this.url)
        .then((response) => response.json())
        .then((json) => {
          this.motuMethodList = json;
          this.dataset = json[0].dataset_id;
          this.loading = false;
          this.$emit("update:motuList", this.motuList);
        });
      return this.ready;
    },
  },
  watch: {
    dataset: function (newDataset, oldDataset) {
      this.methods = this.multiple
        ? this.methodList.map((record) => record.method_id)
        : this.methodList[0].method_id;
    },
  },
};
</script>

<style lang="less" scoped>
@import "../loading.less";
</style>