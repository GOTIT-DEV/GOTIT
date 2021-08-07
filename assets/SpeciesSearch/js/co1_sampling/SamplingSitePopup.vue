<template>
  <l-popup v-if="site" ref="popup" class="site-popup">
    <site-info :site="site" :extra-fields="fields" />
  </l-popup>
</template>

<script>
import { LPopup } from "vue2-leaflet";
import SiteInfo from "~Components/maps/SiteInfo";

export default {
  components: { LPopup, SiteInfo },
  props: {
    site: { type: Object, default: null },
  },
  data() {
    return {
      fields: [],
    };
  },
  methods: {},
};
</script>

<style lang="less">
.site-popup {
  display: flex;
  flex-direction: column;
}
</style>
