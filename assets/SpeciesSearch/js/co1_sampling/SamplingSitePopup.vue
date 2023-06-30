<template>
  <l-popup ref="popup" class="site-popup">
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

<style lang="less"></style>
