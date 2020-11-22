<template>
  <l-polygon :lat-lngs="coords" v-bind="$attrs">
    <slot></slot>
  </l-polygon>
</template>

<script>
import L from "leaflet";
import { LPolygon, LCircleMarker } from "vue2-leaflet";
export default {
  components: {
    LPolygon,
    LCircleMarker,
  },
  props: {
    latLng: {
      type: Array,
      required: true,
      validator: (value) => {
        return value.length == 2;
      },
    },
    size: {
      type: Number,
      default: 5,
    },
  },
  computed: {
    triangleSide() {
      return Math.sqrt(3) * this.size;
    },
    latShift() {
      return Math.sqrt(
        Math.pow(this.size, 2) - Math.pow(this.triangleSide / 2, 2)
      );
    },
    coords() {
      return [
        [
          this.latLng[0] - this.latShift * 0.02,
          this.latLng[1] - (this.triangleSide / 2) * 0.04,
        ],
        [
          this.latLng[0] - this.latShift * 0.02,
          this.latLng[1] + (this.triangleSide / 2) * 0.04,
        ],
        [this.latLng[0] + this.size * 0.02, this.latLng[1]],
      ];
    },
  },
};
</script>

<style lang="less" scoped>
</style>