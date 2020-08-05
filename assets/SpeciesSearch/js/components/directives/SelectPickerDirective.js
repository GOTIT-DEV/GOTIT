require("bootstrap-select")

export const SelectPicker = {
  twoWay: true,

  inserted(el) {
    // Create selectpicker once DOM is ready
    $(() => $(el).selectpicker({
      style: 'btn-light border'
    }))
  },

  update(el) {
    $(el).selectpicker("render")
    $(el).selectpicker("refresh")
  },
  
  // Refresh selectpicker when updating children
  componentUpdated(el, binding) {
    $(el).selectpicker("render")
    $(el).selectpicker("refresh")
  },

  unbind(el) {
    $(el).selectpicker('destroy');
  }
};