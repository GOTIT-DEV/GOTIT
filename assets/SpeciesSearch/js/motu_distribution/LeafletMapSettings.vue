<template>
  <l-control :position="position">
    <div
      class="leaflet-control-collapse leaflet-control-layers leaflet-control"
      @mouseover="active = true"
      @mouseleave="active = false"
    >
      <i v-if="!active" class="fas fa-cog fa-2x"></i>
      <div v-if="active" class="map-settings">
        <div
          v-for="(slider, name) in sliders"
          :key="name"
          class="slider-control"
        >
          <label>{{ slider.label }}</label>
          <vue-slider
            :direction="slider.direction || 'btt'"
            height="100px"
            v-bind="slider"
            :value="settings[name]"
            @change="
              $emit(
                'update:settings',
                Object.assign(settings, { [name]: $event })
              )
            "
          >
          </vue-slider>
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
    settings: Object,
    sliders: {
      type: Object,
      default: [],
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