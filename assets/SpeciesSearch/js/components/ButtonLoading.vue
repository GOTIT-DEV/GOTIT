<template>
  <button
    type="submit"
    class="btn btn-primary btn-block"
    id="submit-button"
    data-loading-text="<i class='fas fa-spinner fa-spin'></i>"
    v-on:click="$emit('click')"
  >
    <slot></slot>
  </button>
</template>

<script>
export default {
  props: {
    loading: {
      type: Boolean,
      default: true
    }
  },
  mounted(){
    this.toggle(this.loading)
  },
  watch:{
    loading: function(newValue, oldValue){
      this.toggle(newValue)
    }
  },
  methods:{
    toggle(loading){
      $(this.$el).button(loading === true ? "loading" : "reset")
    }
  }
};
</script>

<style lang="less" scoped>
</style>