<template>
  <div style="display: none">
    <slot v-if="ready" />
  </div>
</template>

<script>
import {
  optionsMerger,
  propsBinder,
  findRealParent,
} from "vue2-leaflet/src/utils/utils.js";
import CircleMixin from "vue2-leaflet/src/mixins/Circle.js";
import Options from "vue2-leaflet/src/mixins/Options.js";
import L from "leaflet";
import "leaflet-svg-shape-markers";

/**
 * A marker in the shape of a circle
 */
const shapeChoices = ["circle", "triangle", "square", "diamond", "x"];
export default {
  shapeChoices,
  name: "ShapeMarker",
  mixins: [CircleMixin, Options],
  props: {
    latLng: {
      type: [Object, Array],
      default: () => [0, 0],
    },
    pane: {
      type: String,
      default: "markerPane",
    },
    shape: {
      type: String,
      required: true,
      validator: (shape) => shapeChoices.includes(shape),
    },
  },
  data() {
    return {
      ready: false,
    };
  },
  mounted() {
    const options = optionsMerger(
      {
        ...this.circleOptions,
        shape: this.shape,
      },
      this
    );
    this.mapObject =
      this.shape == "circle"
        ? L.circleMarker(this.latLng, options)
        : L.shapeMarker(this.latLng, options);
    L.DomEvent.on(this.mapObject, this.$listeners);
    propsBinder(this, this.mapObject, this.$options.props);
    this.ready = true;
    this.parentContainer = findRealParent(this.$parent);
    this.parentContainer.addLayer(this, !this.visible);
    this.$nextTick(() => {
      /**
       * Triggers when the component is ready
       * @type {object}
       * @property {object} mapObject - reference to leaflet map object
       */
      this.$emit("ready", this.mapObject);
    });
  },
};
</script>
