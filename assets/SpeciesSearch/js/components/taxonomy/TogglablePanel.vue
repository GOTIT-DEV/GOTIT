<template>
  <div class="panel panel-default">
    <div class="panel-heading">
      <strong>
        {{ trans_title }}
      </strong>
      <span class="pull-right">
        <ToggleButton
          v-model="enabled"
          v-bind:labels="{ checked: 'On', unchecked: 'Off' }"
        />
      </span>
    </div>
    <div class="panel-body" ref="content">
      <!-- <TaxonomySelect v-bind:with-taxname="withTaxname" ref="core" /> -->
      <slot></slot>
    </div>
    <div class="panel-footer" v-if="hasFooter">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script>
// import TaxonomySelect from "./TaxonomySelect";
import { createNamespacedHelpers } from "vuex";
const { mapState, mapMutations } = createNamespacedHelpers("taxonomy");

// import ButtonLoading from "../ButtonLoading";
import { ToggleButton } from "vue-js-toggle-button";

export default {
  components: {
    ToggleButton
    // ButtonLoading
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
    ...mapState(["loading"]),
    trans_title(){
      return Translator.trans(this.title) 
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
    ...mapMutations(["setLoading"]),
    submit() {
      this.$refs.button.toggle(true);
    },
    toggleActive(value) {
      $(this.$refs.content).find(":input").prop("disabled", !value);
    }
  }
};
</script>

<style lang="less" scoped>
</style>