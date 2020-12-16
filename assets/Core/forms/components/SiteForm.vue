<template>
  <b-modal
    id="proximity-map-modal"
    size="xl"
    :title="$t('modal_title')"
    static
    hide-footer
    @shown="$refs.proximityMap.invalidateSize()"
  >
    <proximity-map ref="proximityMap" />
  </b-modal>
</template>

<i18n>
{
  "fr": {
    "modal_title": "Stations à proximité (rayon de 10km)"
  },
  "en": {
    "modal_title": "Nearby sites (10km radius)"
  }
}
</i18n>


<script>
import ProximityMap from "./ProximityMap";
export default {
  components: { ProximityMap },
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