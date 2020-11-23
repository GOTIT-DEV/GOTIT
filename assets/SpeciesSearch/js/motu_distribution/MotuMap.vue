<template>
  <div class="map-container">
    <l-map
      ref="map"
      :zoom="zoom"
      :center="center"
      :options="mapOptions"
      :minZoom="minZoom"
      :maxZoom="maxZoom"
      :maxBounds="maxBounds"
      @update:zoom="zoomUpdate"
      :bounds.sync="bounds"
    >
      <l-control-fullscreen
        position="topleft"
        :options="{ title: { false: 'Fullscreen', true: 'Windowed' } }"
      />
      <l-control-layers position="topright"></l-control-layers>
      <l-control position="topleft" class="leaflet-control leaflet-bar">
        <button
          class="btn btn-sm btn-light btn-map-control"
          @click="fitBounds(data)"
        >
          <a class="fas fa-crosshairs fa-1x"></a>
        </button>
      </l-control>
      <l-control position="topleft" class="leaflet-control leaflet-bar">
        <button
          class="btn btn-sm btn-light btn-map-control"
          @click="filterMotuDisplay(null)"
        >
          <a class="fas fa-eye"></a>
        </button>
      </l-control>
      <leaflet-map-settings 
      position="bottomright" 
      :sliders="settingSliders"
      :settings.sync="markerSettings">
      </leaflet-map-settings>
      <l-tile-layer
        v-bind="tileProviders[0]"
        :subdomains="['server', 'services']"
      >
      </l-tile-layer>
      <l-tile-layer
        v-bind="tileProviders[1]"
        layer-type="overlay"
        :subdomains="['server', 'services']"
      >
      </l-tile-layer>
      <l-layer-group
        v-for="(motu, motu_id, index) in indexedData"
        :key="motu_id"
        :visible.sync="motu.visible"
        layerType="overlay"
        :name="layerName(motu_id, index)"
      >
        <shape-marker
          v-for="(station, station_id) in motu.stations"
          :key="station_id"
          :lat-lng="[station.latitude, station.longitude]"
          :radius="markerRadius(station.sequences.length)"
          :opacity="markerSettings.opacity"
          :fillOpacity="markerSettings.opacity"
          v-bind="markerStyle(index)"
        >
          <l-popup>
            {{ index }}
            <map-tooltip
              :station="station"
              :options="{ permanent: true }"
              @show-seq-modal="showSequences(station)"
              @filter-display="filterMotuDisplay($event)"
            />
          </l-popup>
        </shape-marker>
      </l-layer-group>
      </shape-marker>
    </l-map>
    <sequence-modal ref="seqModal"> </sequence-modal>
    
  </div>
</template>

<script>
import L from "leaflet";
import {
  LMap,
  LTileLayer,
  LControl,
  LControlLayers,
  LLayerGroup,
  LCircleMarker,
  LPopup,
} from "vue2-leaflet";
import { basemapLayer, Util } from "esri-leaflet";
import LControlFullscreen from "vue2-leaflet-fullscreen";
import LeafletMapSettings from "./LeafletMapSettings";
import MapTooltip from "./MapTooltip";
import SequenceModal from "./SequenceModal";
import ShapeMarker from "./ShapeMarker";
import chroma from "chroma-js";
import ShapeMarkerLegend from "./ShapeMarkerLegend";
import Vue from "vue";

const LegendShape = Vue.extend(ShapeMarkerLegend);

export default {
  name: "MotuDistributionMap",
  components: {
    ShapeMarker,
    LMap,
    LTileLayer,
    LLayerGroup,
    LPopup,
    LCircleMarker,
    LControl,
    LControlFullscreen,
    LControlLayers,
    LeafletMapSettings,
    MapTooltip,
    SequenceModal,
    ShapeMarker,
  },
  mounted() {
    Util.setEsriAttribution(this.map);
    this._getAttributionData(this.tileProviders[0].attributionUrl);
    let attr = this._updateMapAttribution();
  },
  created() {},
  computed: {
    map() {
      return this.$refs.map.mapObject;
    },
    nShapes() {
      return this.shapes.length;
    },
    nColors() {
      return this.colorBrewer.length;
    },
    nScale() {
      return this.nShapes * this.nColors;
    },
    maxGrowth() {
      return Array.from(Object.values(this.indexedData)).reduce(
        (acc, motu_data) => {
          return Math.max(
            acc,
            ...Array.from(Object.values(motu_data.stations)).map(
              (station) => station.sequences.length
            )
          );
        },
        0
      );
    },
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      // url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
      colorBrewer: chroma.brewer.Set1,
      shapes: ["circle", "triangle", "square", "diamond"],
      indexedData: {},
      tileProviders: [
        {
          name: "Base Layer",
          visible: true,
          opacity: 0.8,
          attribution: "",
          attributionUrl: "https://static.arcgis.com/attribution/World_Imagery",
          url:
            "https://{s}.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
        },
        {
          name: "Regions",
          visible: true,
          opacity: 0.75,
          attribution: "",
          url:
            "https://{s}.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}",
        },
      ],
      zoom: 1,
      center: [0, 0],
      currentZoom: 10,
      bounds: L.latLngBounds(L.latLng(90, -360), L.latLng(-90, 360)),
      maxBounds: L.latLngBounds(L.latLng(90, -360), L.latLng(-90, 360)),
      minZoom: 2,
      maxZoom: 12,
      markerSettings: {
        radius: 6,
        opacity: 1,
        grow: 1,
      },
      settingSliders: {
        grow: {
          label: "Grow",
          min: 1,
          max: 5,
          interval: 0.5,
          adsorb: true,
          tooltipFormatter: (val) => `×${val}`,
        },
        radius: {
          label: "Radius",
          min: 3,
          max: 20,
          interval: 0.5,
          adsorb: true,
        },
        opacity: {
          label: "Opacity",
          min: 0,
          max: 1,
          interval: 0.1,
          adsorb: true,
        },
      },
      mapOptions: {
        // center: [0, 0],
        // zoom: 10,
        worldCopyJump: true,
        wheelPxPerZoomLevel: 100,
        zoomSnap: 0.5,
      },
    };
  },
  watch: {
    data: function (newData, _) {
      if (newData) this.fitBounds(newData);
      this.indexedData = this.organizeByMotu(newData);
    },
  },
  methods: {
    layerName(motu_id, index) {
      const shape = this.markerShape(index);
      const color = this.markerColor(index);
      const legendShape = new LegendShape({
        inheritAttrs: false,
        propsData: {
          label: `Motu ${motu_id}`,
          shape: shape,
          markerStyle:
            index < this.nScale
              ? {
                  stroke: "black",
                  fill: color,
                  "stroke-width": 1,
                }
              : {
                  stroke: color,
                  fill: "transparent",
                  "stroke-width": 3,
                },
        },
      });
      legendShape.$mount();
      return legendShape.$el.outerHTML;
    },
    markerShape(layerIndex) {
      return this.shapes[layerIndex % this.nShapes];
    },
    markerColor(layerIndex) {
      return this.colorBrewer[Math.floor(layerIndex / this.nShapes)];
    },
    markerRadius(scale) {
      const grow = this.markerSettings.grow;
      return (
        this.markerSettings.radius * (1 + (scale / this.maxGrowth) * (grow - 1))
      );
    },
    markerStyle(layerIndex) {
      const color = this.markerColor(layerIndex);
      const shape = this.markerShape(layerIndex);
      return layerIndex < this.nScale
        ? {
            color: "black",
            shape,
            weight: 1,
            fill: true,
            fillColor: color,
          }
        : {
            color,
            shape,
            weight: 3,
            fill: false,
          };
    },
    filterMotuDisplay(motuFilter) {
      Object.entries(this.indexedData).forEach(([motu, motuData]) => {
        motuData.visible = motu == motuFilter || motuFilter === null;
      });
    },
    showSequences(station) {
      this.$refs.seqModal.station = station;
      this.$refs.seqModal.$bvModal.show("modal-sequences");
    },
    zoomUpdate(zoom) {
      this.currentZoom = zoom;
      this._updateMapAttribution();
    },
    fitBounds(dataset) {
      const minMaxCoords = dataset.reduce((acc, item) => {
        return acc === null
          ? {
              lat: [item.latitude, item.latitude],
              lon: [item.longitude, item.longitude],
            }
          : {
              lat: [
                Math.min(acc.lat[0], item.latitude),
                Math.max(acc.lat[1], item.latitude),
              ],
              lon: [
                Math.min(acc.lon[0], item.longitude),
                Math.max(acc.lon[1], item.longitude),
              ],
            };
      }, null);
      this.bounds = L.latLngBounds([
        [minMaxCoords.lat[0], minMaxCoords.lon[0]],
        [minMaxCoords.lat[1], minMaxCoords.lon[1]],
      ]);
    },
    organizeByMotu(data) {
      function reduceOrganizer(motuDict, item) {
        item.station_url = Routing.generate("station_show", {
          id: item.id_sta,
          _locale: Translator.locale,
        });

        item.seq_url = Routing.generate(
          item.seq_type
            ? "sequenceassembleeext_show"
            : "sequenceassemblee_show",
          { id: item.id, _locale: Translator.locale }
        );

        if (item.altitude === null) item.altitude = "-";

        if (!(item.motu in motuDict))
          motuDict[item.motu] = {
            visible: true,
            stations: {},
          };

        if (!(item.id_sta in motuDict[item.motu]["stations"]))
          motuDict[item.motu]["stations"][item.id_sta] = Object.assign(
            {
              sequences: [],
              latitude: parseFloat(item.latitude),
              longitude: parseFloat(item.longitude),
            },
            item
          );
        delete item.sequences;
        motuDict[item.motu]["stations"][item.id_sta].sequences.push(item);
        return motuDict;
      }
      return data.reduce(reduceOrganizer, {});
    },
    _updateMapAttribution() {
      var oldAttributions = this.map._esriAttributions;

      if (oldAttributions) {
        var wrappedBounds = L.latLngBounds(
          this.bounds.getSouthWest().wrap(),
          this.bounds.getNorthEast().wrap()
        );
        var zoom = this.map.getZoom();

        const attribs = oldAttributions
          .filter((attribution) => {
            return (
              attribution.bounds.intersects(wrappedBounds) &&
              zoom >= attribution.minZoom &&
              zoom <= attribution.maxZoom
            );
          })
          .map((attribution) => attribution.attribution);
        this.tileProviders[0].attribution = [...new Set(attribs)].join(", ");
      }
    },
    _getAttributionData(url) {
      function reducer(acc, contrib) {
        contrib.coverageAreas.forEach((area) => {
          acc.push({
            attribution: contrib.attribution,
            score: area.score,
            minZoom: area.zoomMin,
            maxZoom: area.zoomMax,
            bounds: L.latLngBounds(
              L.latLng(area.bbox[0], area.bbox[1]),
              L.latLng(area.bbox[2], area.bbox[3])
            ),
          });
        });
        return acc;
      }
      fetch(url)
        .then((response) => response.json())
        .then((attributions) => {
          this.map._esriAttributions = attributions.contributors
            .reduce(reducer, [])
            .sort((a, b) => b.score - a.score);
          this._updateMapAttribution();
        });
    },
  },
};
</script>

<style lang="less" scoped>
.map-container {
  width: 100%;
  height: 80vh;
}
.btn-map-control {
  padding: 0;
}
</style>