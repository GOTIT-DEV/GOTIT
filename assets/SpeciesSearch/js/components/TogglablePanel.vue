<template>
  <b-card>
    <template v-slot:header>
      <strong>
        {{ $t(title) }}
      </strong>
      <ToggleButton
        class="toggle-btn"
        v-model="enabled"
        v-bind:height="20"
        v-bind:labels="{ checked: 'On', unchecked: 'Off' }"
      />
    </template>

    <div ref="content">
      <slot></slot>
    </div>

    <!-- <b-card-footer v-if="hasFooter"> -->
    <template v-slot:footer v-if="hasFooter">
      <slot name="footer"></slot>
    </template>
    <!-- </b-card-footer> -->
  </b-card>
</template>

<script>
// import TaxonomySelect from "./TaxonomySelect";

// import ButtonLoading from "../ButtonLoading";
import { ToggleButton } from "vue-js-toggle-button";
import i18n from '../i18n'
export default {
  i18n,
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
  computed: {
    trans_title() {
      return Translator.trans(this.title);
    },
    hasFooter() {
      return this.$slots.footer !== undefined;
    },
  },
  data() {
    return {
      enabled: true,
    };
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