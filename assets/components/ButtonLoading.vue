<template>
  <b-button
    id="submit-button"
    type="submit"
    variant="primary"
    v-bind="$props"
    v-on:click="$emit('click')"
  >
    <slot></slot>
    <i v-if="loading" class="fas fa-spinner fa-spin"></i>
  </b-button>
</template>

<script>
import {BButton} from "bootstrap-vue"
export default {
  props: {
    ...BButton.options.props,
    loading: {
      type: Boolean,
      default: true,
    },
  },
};
</script>

<style lang="less" scoped>
</style>