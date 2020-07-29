require("bootstrap-select")

export const SelectPicker = {
  twoWay: true,

  bind(el) {
    // Create selectpicker once DOM is ready
    $(() => {
      $(el).selectpicker({
        style: 'btn-light border'
      })
    })
  },

  // Refresh selectpicker when updating children
  componentUpdated(el, binding) {
    $(el).selectpicker("refresh")
  },

  unbind(el) {
    $(el).selectpicker('destroy');
  }
};