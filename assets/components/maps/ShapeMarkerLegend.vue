<template>
  <div class="layer-legend">
    <span class="legend-label">{{ label }}</span>
    <svg class="legend-marker" height="16px" :width="width">
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
        v-else-if="shape == 'circle'"
        cx="8"
        cy="8"
        r="5"
        v-bind="markerStyle"
      ></circle>
      <polygon
        v-else-if="shape == 'triangle'"
        points="8,2 14,14 2,14"
        r="5"
        v-bind="markerStyle"
      ></polygon>
      <line
        v-else-if="shape == 'line'"
        x1="0"
        x2="100"
        y1="8"
        y2="8"
        v-bind="markerStyle"
      ></line>
    </svg>
  </div>
</template>

<script>
let ShapeMarkerLegend = {
  props: {
    shape: {
      type: String,
      required: true,
      validator: (shape) =>
        ["circle", "triangle", "square", "diamond", "line"].includes(shape),
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
  computed: {
    width() {
      return this.shape == "line" ? "50px" : "20px";
    },
  },
};

import Vue from "vue";

export default ShapeMarkerLegend;
export function generateLegend(label, shape = null, markerStyle = null) {
  if (shape === null) {
    return label;
  } else {
    const legendShape = new Vue({
      ...ShapeMarkerLegend,
      inheritAttrs: false,
      propsData: {
        label,
        shape,
        markerStyle,
      },
    }).$mount();
    return legendShape.$el.outerHTML;
  }
}
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