<template>
  <div id="main-container" class="px-2">
    <header class="">
      <slot name="header">
        <div
          class="
            title-container
            d-flex
            justify-content-between
            align-items-center
          "
        >
          <h1>{{ title }}</h1>
          <b-button
            id="page-help-btn"
            v-b-toggle="'page-help-block'"
            variant="light"
            class="border-primary text-secondary"
          >
            <i class="fas fa-question" />
          </b-button>
        </div>
        <b-collapse id="page-help-block">
          <b-card>{{ helpText }}</b-card>
        </b-collapse>
        <slot name="header-extra" />
      </slot>
    </header>
    <div class="content">
      <slot name="content" />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true,
    },
    helpText: {
      type: String,
      default: "No help text was provided for this page.",
    },
  },
};
</script>

<style lang="less" scoped>
#main-container {
  display: flex;
  flex-direction: column;
  gap: 3em;
  header {
    .title-container {
      button#page-help-btn {
        border-radius: 50%;
        height: 30px;
        padding: 0;
        width: 30px;
      }
    }
  }
}
</style>
