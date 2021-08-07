<template>
  <div class="assignment-cell">
    <span class="d-flex justify-content-between">
      <b class="text-truncate text-dark" :title="item.taxname">
        {{ item.taxname }}
      </b>
      <b-badge
        v-b-tooltip.hover
        size="lg"
        variant="secondary"
        :title="item.criterion.title"
      >
        {{ item.criterion.code }}
      </b-badge>
    </span>
    <div v-if="entity == 'specimen'">
      <div class="text-truncate">
        Morpho :
        <a :href="route" class="text-monospace" :title="item.code.morpho">
          {{ item.code.morpho }}
        </a>
      </div>
      <div class="text-truncate">
        {{ $t("biomol") }} :
        <a :href="route" class="text-monospace" :title="item.code.biomol">
          {{ item.code.biomol }}
        </a>
      </div>
    </div>
    <a
      v-else
      :href="route"
      class="text-monospace text-truncate"
      :title="item.code"
    >
      {{ item.code }}
    </a>
  </div>
</template>

<i18n>
{
  "en": {
    "biomol": "Mol. bio"
  },
  "fr": {
    "biomol": "Bio mol"
  }
}
</i18n>

<script>
const route_names = {
  biomaterial: "lotmateriel_show",
  specimen: "individu_show",
  sequence: "sequenceassemblee_show",
};
export default {
  props: {
    item: { type: Object, default: null },
    entity: {
      type: String,
      required: true,
      validator: (entity) =>
        ["biomaterial", "specimen", "sequence"].includes(entity),
    },
  },
  computed: {
    route() {
      return this.item.id
        ? this.generateRoute(route_names[this.entity], { id: this.item.id })
        : "#";
    },
  },
};
</script>

<style lang="less" scoped>
.assignment-cell {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
</style>
