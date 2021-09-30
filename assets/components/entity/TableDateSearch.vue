<template>
  <b-input-group size="sm">
    <b-input-group-prepend>
      <b-select v-model="operator" size="sm" :options="operators" />
    </b-input-group-prepend>
    <b-input
      v-model="value"
      v-mask="{ mask: '9999-99-99', placeholder: 'YYYY-MM-DD' }"
      class="date-input"
      debounce="500"
    />
    <template #append>
      <b-datepicker
        v-model="value"
        size="sm"
        :locale="locale"
        button-only
        right
      />
    </template>
  </b-input-group>
</template>

<script>
import Mask from "../../directives/InputMask";
export default {
  directives: { Mask },
  data() {
    return {
      value: "",
      operator: "before",
      operators: [
        { value: "before", text: "≤" },
        { value: "strictly_before", text: "<" },
        { value: "after", text: "≥" },
        { value: "strictly_after", text: ">" },
      ],
    };
  },
  computed: {
    locale() {
      return Translator.locale;
    },
  },
  watch: {
    value(newVal) {
      this.$emit("update", { [this.operator]: this.value });
    },
  },
};
</script>

<style lang="less" scoped>
.date-input {
  max-width: 9rem;
}
</style>
