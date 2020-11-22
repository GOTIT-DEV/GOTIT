<template>
  <l-control :position="position">
    <div
      class="leaflet-control-collapse leaflet-control-layers leaflet-control"
      @mouseover="active = true"
      @mouseleave="active = false"
    >
      <i v-if="!active" class="fas fa-cog fa-2x"></i>
      <div v-if="active" class="map-settings">
        <div class="slider-control">
          <label>Radius</label>
          <vue-slider
            direction="btt"
            height="100px"
            :min="3"
            :max="20"
            :interval="0.5"
            :adsorb="true"
            :value="radius"
            @change="$emit('update:radius', $event)"
          ></vue-slider>
        </div>
        <div class="slider-control">
          <label>Opacity</label>
          <vue-slider
            direction="btt"
            height="100px"
            :min="0"
            :max="1"
            :interval="0.1"
            :adsorb="true"
            :value="opacity"
            @change="$emit('update:opacity', $event)"
          ></vue-slider>
        </div>
      </div>
    </div>
  </l-control>
</template>

<script>
import { LControl } from "vue2-leaflet";
import VueSlider from "vue-slider-component";

export default {
  components: {
    LControl,
    VueSlider,
  },
  props: {
    radius: {
      type: Number,
      required: true,
    },
    opacity: {
      type: Number,
      required: true,
    },
    position: {
      type: String,
      required: true,
      validator: (value) =>
        ["topleft", "topright", "bottomleft", "bottomright"].includes(value),
    },
  },
  data() {
    return {
      active: false,
    };
  },
  methods: {},
};
</script>

<style lang="less" scoped>
@import "~vue-slider-component/theme/default.css";
.map-settings {
  padding: 15px;
  display: grid;
  grid-auto-columns: 1fr;
  grid-template-rows: auto;
  grid-auto-flow: column;
  gap: 10px;
  .slider-control {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
}
</style>