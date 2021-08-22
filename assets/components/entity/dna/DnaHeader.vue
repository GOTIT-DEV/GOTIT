<template>
  <b-button-toolbar class="mt-2">
    <b-button :href="urls.list" variant="primary" size="sm" class="mx-1">
      <i class="fas fa-list" />
      List all
    </b-button>
    <b-button :href="urls.create" variant="success" size="sm" class="mx-1">
      <i class="fas fa-plus" />
      Create new
    </b-button>
    <b-button :href="urls.import" variant="light" size="sm" class="border mx-1">
      <i class="fas fa-upload" />
      Import
    </b-button>
  </b-button-toolbar>
</template>

<script>
export default {
  props: {
    urls: {
      type: Object,
      required: true,
      validator(urls) {
        return ["create", "list", "import"].every((key) => key in urls);
      },
    },
  },
};
</script>

<style lang="less" scoped></style>
