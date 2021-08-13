<template>
  <b-card>
    <template #header>
      <strong>
        {{ $t(title) }}
      </strong>
      <ToggleButton
        v-model="enabled"
        class="toggle-btn"
        :height="20"
        :labels="{ checked: 'On', unchecked: 'Off' }"
      />
    </template>

    <div ref="content">
      <slot />
    </div>

    <!-- <b-card-footer v-if="hasFooter"> -->
    <template v-if="hasFooter" #footer>
      <slot name="footer" />
    </template>
    <!-- </b-card-footer> -->
  </b-card>
</template>

<script>
// import TaxonomySelect from "./TaxonomySelect";

// import ButtonLoading from "../ButtonLoading";
import { ToggleButton } from "vue-js-toggle-button";
export default {
  components: {
    ToggleButton,
  },
  props: {
    title: {
      type: String,
      required: true,
    },
    withTaxname: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      enabled: true,
    };
  },
  computed: {
    trans_title() {
      return Translator.trans(this.title);
    },
    hasFooter() {
      return this.$slots.footer !== undefined;
    },
  },
  watch: {
    enabled: function (newValue, oldValue) {
      this.toggleActive(newValue);
    },
  },
  methods: {
    toggleActive(value) {
      $(this.$refs.content).find(":input").prop("disabled", !value);
    },
  },
};
</script>

<style lang="less" scoped>
.card {
  min-width: 250px;
}

.panel-toggle {
  margin-left: 10px;
}
.card-header {
  display: flex;
  justify-content: space-between;
  flex-wrap: nowrap;
  word-wrap: nowrap;
  .toggle-btn {
    margin: 0;
  }
}
</style>
