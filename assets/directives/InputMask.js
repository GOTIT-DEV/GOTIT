import Inputmask from 'inputmask'

export default {
  twoWay: true,

  bind(el, binding) {
    // Create selectpicker once DOM is ready
    Inputmask(binding.value).mask(el)
  }
};