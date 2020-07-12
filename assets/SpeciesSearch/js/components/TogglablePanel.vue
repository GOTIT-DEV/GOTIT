<template>
  <div class="card panel-default">
    <div class="card-header">
      <strong>
        {{ trans_title }}
      </strong>
      <ToggleButton
        class="toggle-btn"
        v-model="enabled"
        height="20"
        v-bind:labels="{ checked: 'On', unchecked: 'Off' }"
      />
    </div>
    <div class="card-body" ref="content">
      <slot></slot>
    </div>
    <div class="card-footer" v-if="hasFooter">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script>
// import TaxonomySelect from "./TaxonomySelect";

// import ButtonLoading from "../ButtonLoading";
import { ToggleButton } from "vue-js-toggle-button";

export default {
  components: {
    ToggleButton
  },
  props: {
    title: {
      type: String,
      required: true
    },
    withTaxname: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    trans_title() {
      return Translator.trans(this.title);
    },
    hasFooter() {
      return this.$slots.footer !== undefined;
    }
  },
  data() {
    return {
      enabled: true
    };
  },
  watch: {
    enabled: function(newValue, oldValue) {
      this.toggleActive(newValue);
    }
  },
  methods: {
    submit() {
      this.$refs.button.toggle(true);
    },
    toggleActive(value) {
      $(this.$refs.content)
        .find(":input")
        .prop("disabled", !value);
    }
  }
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
  .toggle-btn{
    margin: 0;
  }
}

</style>