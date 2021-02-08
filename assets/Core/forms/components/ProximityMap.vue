<template>
  <leaflet-map
    ref="map"
    :data="[{ latitude, longitude }]"
    :markerSettings="markerSettings"
    :disableAutoFit="true"
    regions
  >
    <l-marker :lat-lng="[latitude, longitude]" />
    <l-circle
      :lat-lng="[latitude, longitude]"
      :radius="radius"
      :opacity="0.5"
    />
    <l-layer-group layerType="overlay" :name="$t('nearby_sites_legend')">
      <shape-marker
        v-for="site in nearbySites"
        :key="site.id"
        :lat-lng="[site.latitude, site.longitude]"
        v-bind="markerSettings"
        :fillOpacity="markerSettings.opacity / 2"
        color="red"
        :weight="1"
        fillColor="red"
        shape="triangle"
      >
        <l-popup ref="sitePopups">
          <site-info :site="site"></site-info>
        </l-popup>
      </shape-marker>
    </l-layer-group>
  </leaflet-map>
</template>

<i18n>
{
  "en": {
    "nearby_sites_legend" : "Nearby sites"
  },
  "fr": {
    "nearby_sites_legend" : "Stations à proximité"
  }
}
</i18n>

<script>
import LeafletMap from "../../../components/maps/LeafletMap";
import ShapeMarker from "../../../components/maps/ShapeMarker";
import SiteInfo from "../../../components/maps/SiteInfo";
import { LMarker, LLayerGroup, LCircle, LPopup } from "vue2-leaflet";

export default {
  components: {
    LeafletMap,
    ShapeMarker,
    LMarker,
    LLayerGroup,
    LCircle,
    LPopup,
    SiteInfo,
  },
  props: {
    radius: {
      // meters
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      latitude: 0,
      longitude: 0,
      nearbySites: [],
      markerSettings: {
        radius: 6,
        opacity: 1,
        grow: 1,
      },
    };
  },
  methods: {
    async setCoords(latitude, longitude) {
      this.$refs.map.zoom = 8.5;
      this.latitude = latitude;
      this.longitude = longitude;
      await this.fetch();
    },
    async invalidateSize() {
      const innerMap = this.$refs.map;
      innerMap.mapObject.invalidateSize(false);
      await this.$nextTick();
      this.$refs.map.center = [this.latitude, this.longitude];
    },
    async fetch() {
      const postData = new FormData();
      postData.append("latitude", this.latitude);
      postData.append("longitude", this.longitude);
      postData.append("radius", this.radius);
      const response = await fetch(Routing.generate("nearby_stations"), {
        method: "POST",
        body: postData,
      });
      const fetchData = await response.json();
      this.nearbySites = fetchData.sites.map((site) => {
        return {
          ...site,
          station_url: Routing.generate("station_show", { id: site.id }),
        };
      });
    },
  },
};
</script>

<style lang="less" scoped>
</style>