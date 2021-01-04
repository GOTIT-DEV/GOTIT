<template>
  <b-modal
    id="proximity-map-modal"
    size="xl"
    :title="$t('modal_title', { radius: radius / 1000 })"
    static
    hide-footer
    @shown="$refs.proximityMap.invalidateSize()"
  >
    <proximity-map ref="proximityMap" :radius="radius" />
  </b-modal>
</template>

<i18n>
{
  "fr": {
    "modal_title": "Stations à proximité (rayon de {radius}km)"
  },
  "en": {
    "modal_title": "Nearby sites ({radius}km radius)"
  }
}
</i18n>


<script>
import ProximityMap from "./ProximityMap";
export default {
  components: { ProximityMap },
  data: {
    radius: 100000, // meters
  },
  methods: {
    async showProximityMap(latitude, longitude) {
      await this.$refs.proximityMap.setCoords(latitude, longitude);
      this.$bvModal.show("proximity-map-modal");
    },
  },
};
</script>

<style lang="less">
#proximity-map-modal .modal-body {
  padding: 0;
}
</style>