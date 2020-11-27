<template>
  <div>
    <multiselect ref="multiselect" v-bind="$props" v-on="$listeners">
      <template
        v-for="(index, slotName) in $scopedSlots"
        v-slot:[slotName]="data"
      >
        <slot :name="slotName" v-bind="data"></slot>
      </template>
    </multiselect>

    <select :multiple="multiple" :name="name" class="d-none">
      <option
        v-for="opt in internalValue"
        :value="opt[trackBy]"
        :key="opt[trackBy]"
        selected
      />
    </select>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
export default {
  name: "FormMultiselect",
  extends: Multiselect,
  components: { Multiselect },
  props: {
    // ...Multiselect.props,
    trackBy: {
      type: String,
      required: true,
    },
    name: {
      type: String,
      required: true,
    },
  },
};
</script>

<style lang="less" scoped>
</style>