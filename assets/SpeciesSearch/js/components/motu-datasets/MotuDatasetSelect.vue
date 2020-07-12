<template>
  <div class="motu-select requires-loading" v-bind:class="{ loading: loading }">
    <div class="select-container">
      <div class="form-group">
        <label for="dataset">
          {{ labels.dataset }}
        </label>
        <select
          v-model="dataset"
          name="dataset"
          id="dataset"
          class="form-control"
          v-select-picker
        >
          <option
            v-for="motu in motuList"
            :value="motu.id"
            :key="motu.id"
          >
            {{ motu.name }}
          </option>
        </select>
      </div>
      <div class="form-group">
        <label>
          {{ labels.methods }}
        </label>
        <select
          v-bind:multiple="multiple"
          v-model="methods"
          name="methods[]"
          id="methods-select"
          class="form-control"
          v-select-picker
        >
          <option
            v-for="method in methodList"
            :value="method.method_id"
            :key="method.method_id"
          >
            {{ method.method_code }}
          </option>
        </select>
      </div>
    </div>
    <i class="fas fa-spinner fa-spin"> </i>
  </div>
</template>

<script>
// import { createNamespacedHelpers } from "vuex";
// const { mapState, mapMutations, mapActions } = createNamespacedHelpers("motu");
import { SelectPicker } from "../directives/SelectPickerDirective";

export default {
  directives: {
    SelectPicker
  },
  props: {
    multiple: {
      type: Boolean,
      default: false
    }
  },
  created() {
    this.fetch();
  },
  data() {
    return {
      url: Routing.generate("methods-list"),
      labels: {
        dataset: Translator.trans("label.dataset"),
        methods: Translator.trans("methode.label")
      },
      ready: false,
      loading: true,
      motuMethodList: [],
      dataset: undefined,
      methods: this.multiple ? [] : undefined
    };
  },
  computed: {
    motuList() {
      let uniqueDatasets = this.motuMethodList.reduce((acc, record) => {
        acc[record.dataset_id] = {
          id: record.dataset_id,
          name: record.dataset_name
        };
        return acc;
      }, {});
      console.log(uniqueDatasets);
      return Object.values(uniqueDatasets);
    },
    methodList() {
      let methods = this.motuMethodList.filter(
        ({ dataset_id }) => dataset_id == this.dataset
      );
      return methods;
    }
  },
  methods: {
    fetch() {
      this.ready = fetch(this.url)
        .then(response => response.json())
        .then(json => {
          this.motuMethodList = json;
          this.dataset = json[0].dataset_id;
          this.loading = false;
        });
      return this.ready;
    }
  },
  watch: {
    dataset: function(newDataset, oldDataset) {
      this.methods = this.multiple
        ? this.methodList.map(record => record.method_id)
        : this.methodList[0].method_id;
    }
  }
};
</script>

<style lang="less" scoped>
@import "../loading.less";
</style>