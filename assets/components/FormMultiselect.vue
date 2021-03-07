<template>
  <div>
    <multiselect
      ref="multiselect"
      v-bind="$props"
      v-on="$listeners"
      :showLabels="false"
    >
      <template
        v-for="(index, slotName) in $scopedSlots"
        v-slot:[slotName]="data"
      >
        <slot :name="slotName" v-bind="data"></slot>
      </template>
    </multiselect>

    <select
      :multiple="multiple"
      :name="name"
      class="d-none"
      :disabled="disabled"
    >
      <option
        v-for="(opt, index) in internalValue"
        :value="trackBy ? opt[trackBy] : opt"
        :key="trackBy ? opt[trackBy] : index"
        selected
      />
    </select>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
export default {
  name: "FormMultiselect",
  mixins: [Multiselect],
  components: { Multiselect },
  props: {
    trackBy: {
      type: String,
      required: false,
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