<template>
  <div class="station-popup">
    <label style="grid-column: 1 / span 2">
      <a :href="station.station_url">
        <b>{{ station.station_code }}</b>
      </a>
    </label>
    <label>MOTU:</label>
    <span>{{ station.motu }}</span>
    <label>Lat:</label>
    <span>{{ station.latitude }}</span>
    <label>Lon:</label>
    <span>{{ station.longitude }}</span>
    <label>Alt:</label>
    <span>{{ station.altitude }} m</span>
    <span style="grid-column: 1 / span 2; text-align: right">{{
      station.municipality
    }}</span>
    <span style="grid-column: 1 / span 2; text-align: right">{{
      station.country
    }}</span>

    <span style="grid-column: 1 / span 2">
      <hr />
      <button
        class="btn btn-sm btn-seq-modal btn-dark"
        title="Sequences"
        @click="$emit('show-seq-modal')"
      >
        <i class="fas fa-dna">{{ station.sequences.length }}</i>
      </button>

      <button
        class="btn btn-sm btn-dark btn-marker-isolate"
        title="maps.controls.filter.motu"
        @click="$emit('filter-display', station.motu)"
      >
        <i class="fas fa-eye"></i>
      </button>
    </span>
  </div>
</template>

<script>
export default {
  props: {
    station: { type: Object },
  },
};
</script>

<style lang="less" scoped>
</style>