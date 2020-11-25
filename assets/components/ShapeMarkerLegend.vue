<template>
  <div class="layer-legend">
    <span class="legend-label">{{ label }}</span>
    <svg class="legend-marker" height="16px" width="20px">
      <rect
        v-if="['square', 'diamond'].includes(shape)"
        x="4"
        y="4"
        width="8"
        height="8"
        v-bind="markerStyle"
        :transform="shape == 'diamond' ? 'rotate(45, 8, 8)' : ''"
      ></rect>
      <circle
        v-if="shape == 'circle'"
        cx="8"
        cy="8"
        r="5"
        v-bind="markerStyle"
      ></circle>
      <polygon
        v-if="shape == 'triangle'"
        points="8,2 14,14 2,14"
        r="5"
        v-bind="markerStyle"
      ></polygon>
    </svg>
  </div>
</template>

<script>
export default {
  props: {
    shape: {
      type: String,
      required: true,
      validator: (shape) =>
        ["circle", "triangle", "square", "diamond"].includes(shape),
    },
    label: {
      type: String,
      required: true,
    },
    markerStyle: {
      type: Object,
      default: () => {
        return {};
      },
    },
  },
};
</script>

<style lang="less" scoped>
.layer-legend {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  .legend-label {
    margin-left: 0;
  }
}
</style>