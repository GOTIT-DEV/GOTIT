<template>
  <div class="panel panel-default">
    <div class="panel-heading">
      <strong>
        {{ title }}
      </strong>
      <span class="pull-right">
        <ToggleButton
          v-model="enabled"
          v-bind:labels="{ checked: 'On', unchecked: 'Off' }"
        />
      </span>
    </div>
    <div class="panel-body">
      <TaxonomySelect v-bind:with-taxname="withTaxname" ref="core" />
    </div>
    <div class="panel-footer">
      <button
        type="submit"
        value="Rechercher"
        class="btn btn-primary btn-block"
        id="submit-button"
        data-loading-text="<i class='fas fa-spinner fa-spin'></i>"
      >
        "label.search.bouton"
      </button>
    </div>
  </div>
</template>

<script>
import TaxonomySelect from "./TaxonomySelect";
import { ToggleButton } from "vue-js-toggle-button";

export default {
  components: {
    TaxonomySelect,
    ToggleButton
  },
  props: {
    withTaxname: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      enabled: true,
      title: Translator.trans("label.search.espece")
    };
  },
  watch: {
    enabled: function(newValue, oldValue) {
      this.$refs.core.toggleActive(newValue);
    }
  },
  mounted() {
    // $(this.$refs.switch).bootstrapToggle();
  }
};
</script>

<style lang="less" scoped>
</style>