<template>
  <b-form id="file-import-form" ref="form" @submit.prevent="submit">
    <b-form-group>
      <b-button :href="templateUrl" class="border">
        <i class="fas fa-download" />
        Download CSV template
      </b-button>
    </b-form-group>
    <b-form-group>
      <b-file
        v-model="csvFile"
        name="csvFile"
        placeholder="Select a file to upload"
        accept=".csv"
        required
      />
    </b-form-group>
    <b-form-group>
      <b-checkbox name="generateCode" inline>
        Generate code
      </b-checkbox>
      <b-checkbox name="overrideCode" inline>
        Override code
      </b-checkbox>
    </b-form-group>
    <button-loading type="submit" variant="success" :loading="loading" block>
      Send
    </button-loading>
  </b-form>
</template>

<script>
import ButtonLoading from "./ButtonLoading.vue";
export default {
  components: { ButtonLoading },
  props: {
    apiRoute: {
      type: String,
      default: null,
    },
    templateUrl: {
      type: String,
      default: null,
    },
    types: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  data() {
    return {
      loading: false,
      type: null,
      csvFile: null,
    };
  },
  computed: {},
  methods: {
    async submit() {
      this.loading = true;
      const response = await fetch(this.apiRoute, {
        method: "POST",
        body: new FormData(this.$refs.form),
      });
      const json = await response.json();
      if (response.ok) {
        this.$emit("success", json);
      } else {
        if (response.status === 400) {
          this.$emit("errors", json);
        } else {
          this.$emit("failure", json);
        }
      }
      this.loading = false;
    },
  },
};
</script>

<style lang="less" scoped>
// #file-import-form {
//   font-size: 1rem;
// }
</style>
