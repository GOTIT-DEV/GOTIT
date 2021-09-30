<template>
  <b-input-group size="sm">
    <b-input-group-prepend>
      <b-select v-model="operator" size="sm" :options="operators" />
    </b-input-group-prepend>
    <b-input
      v-mask="{ mask: '9999-99-99', placeholder: 'YYYY-MM-DD' }"
      :value="value[operator]"
      class="date-input"
      :debounce="debounce"
      @update="$emit('update', { [operator]: $event })"
    />
    <template #append>
      <b-datepicker
        :value="value[operator]"
        size="sm"
        :locale="locale"
        button-only
        right
        @input="$emit('update', { [operator]: $event })"
      />
    </template>
  </b-input-group>
</template>

<script>
import Mask from "../../directives/InputMask";
export default {
  directives: { Mask },
  props: {
    value: {
      type: Object,
      required: true,
    },
    debounce: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
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
};
</script>

<style lang="less" scoped>
.date-input {
  max-width: 9rem;
}
</style>
