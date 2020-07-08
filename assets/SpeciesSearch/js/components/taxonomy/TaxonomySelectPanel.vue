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
      <ButtonLoading v-bind:loading="loading" v-on:click="submit" ref="button">
        Search
      </ButtonLoading>
    </div>
  </div>
</template>

<script>
import TaxonomySelect from "./TaxonomySelect";
import ButtonLoading from "../ButtonLoading";
import { ToggleButton } from "vue-js-toggle-button";

import { createNamespacedHelpers } from 'vuex'
const { mapState, mapMutations } = createNamespacedHelpers('taxonomy')

export default {
  components: {
    TaxonomySelect,
    ToggleButton,
    ButtonLoading
  },
  props: {
    withTaxname: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    ...mapState(["loading", "ready"]),
    ready(){
      !this.loading
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
  methods:{
    ...mapMutations(["setLoading"]),
    submit(){
      this.$refs.button.toggle(true)
    }
  }
};
</script>

<style lang="less" scoped>
</style>