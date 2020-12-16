<template>
  <l-popup ref="popup" class="site-popup">
    <site-info :site="site" :extra-fields="fields" />

    <b-button-group class="mt-3">
      <b-button
        size="sm"
        variant="dark"
        :title="$t('show_seqs')"
        @click="$emit('show-seq-modal')"
      >
        <i class="fas fa-dna"></i> {{ site.sequences.length }}
      </b-button>

      <b-button
        size="sm"
        variant="dark"
        :title="$t('filter_motu')"
        @click="$emit('filter-display', site.motu)"
      >
        <i class="fas fa-eye"></i>
      </b-button>
      <b-button
        size="sm"
        variant="dark"
        :title="$t('fit_motu')"
        @click="fitMotu(site.motu)"
      >
        <i class="fas fa-crosshairs"></i>
      </b-button>
    </b-button-group>
  </l-popup>
</template>



<script>
import { LPopup } from "vue2-leaflet";
import SiteInfo from "../../../components/maps/SiteInfo";

export default {
  components: { LPopup, SiteInfo },
  props: {
    site: { type: Object },
  },
  data() {
    return {
      fields: [
        {
          key: "motu",
          label: "MOTU",
        },
      ],
    };
  },
  methods: {
    fitMotu(motu) {
      this.$refs.popup.mapObject.closePopup();
      this.$emit("fit-motu", motu);
    },
  },
};
</script>

<style lang="less" >
.site-popup {
  display: flex;
  flex-direction: column;
}
</style>