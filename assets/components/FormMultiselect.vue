<template>
  <div>
    <multiselect
      ref="multiselect"
      v-bind="$props"
      :show-labels="false"
      v-on="$listeners"
    >
      <template v-for="(index, slotName) in $scopedSlots" #[slotName]="data">
        <slot :name="slotName" v-bind="data" />
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
        :key="trackBy ? opt[trackBy] : index"
        :value="trackBy ? opt[trackBy] : opt"
        selected
      />
    </select>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
export default {
  name: "FormMultiselect",
  components: { Multiselect },
  mixins: [Multiselect],
  props: {
    trackBy: {
      type: String,
      default: null,
    },
    name: {
      type: String,
      required: true,
    },
  },
};
</script>

<style lang="less" scoped></style>
