import Circle from 'vue2-leaflet/src/mixins/Path';

const shapeChoices = ["triangle", "square", "diamond", "x"];

export default {
  mixins: [Circle],
  props: {
    shape: {
      type: NumberString,
      required: true,
      validator: (shape) => shapeChoices.includes(shape),
    }
  },
  mounted() {
    this.shapeOptions = {
      ...this.circleOptions,
      shape: this.shape
    };
  }
};
