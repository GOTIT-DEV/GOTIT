<template>
  <b-button-group>
    <b-button size="sm" class="border px-0 py-1" variant="light">
      <a
        :href="generateRoute(showRoute, { id: item.id })"
        class="px-2 py-1 align-middle"
      >
        <i class="fas fa-eye" />
      </a>
    </b-button>
    <b-button size="sm" class="border p-0" variant="light">
      <a
        :href="generateRoute(editRoute, { id: item.id })"
        class="px-2 py-1 align-middle"
      >
        <i class="fas fa-edit" />
      </a>
    </b-button>
    <b-button
      size="sm"
      class="border p-0"
      variant="light"
      @click="$emit('delete', item)"
    >
      <a class="px-2 py-1 align-middle">
        <i class="fas fa-trash" />
      </a>
    </b-button>
  </b-button-group>
</template>

<script>
export default {
  props: {
    item: { type: Object, required: true },
    showRoute: { type: String, required: true },
    editRoute: { type: String, required: true },
  },
};
</script>

<style lang="less" scoped></style>
