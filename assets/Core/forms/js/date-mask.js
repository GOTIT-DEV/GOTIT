import Inputmask from "inputmask"
import moment from "moment"

const dateMaskConfig = {
  shiftPositions: false,
  tabThrough: true,
  regex: "[0-3]\\d-[0-1]\\d-[1-2]\\d{3}",
  mask: "99-99-9999",
  placeholder: 'NA',
}

const dateMasks = [
  // DAY
  {
    mask: "99-99-9999",
    placeholder: "DD-MM-YYYY",
  },
  //MONTH
  {
    mask: "\\0\\1-99-9999",
    placeholder: "01-MM-YYYY",
  },
  //YEAR
  {
    mask: "\\0\\1-\\0\\1-9{4}",
    placeholder: "01-01-YYYY",
  },
  //DISABLED
  {
    mask: "",
    placeholder: 'NA',
  }
]


export function initDateMask(formBlockElement) {

  const dateMasker = new Inputmask(dateMaskConfig)
  const precisionWidget = formBlockElement.querySelector(".date-precision")
  const dateWidget = formBlockElement.querySelector('.date-autoformat')

  if (dateWidget && dateWidget.value) validateDate(dateWidget)

  $(dateWidget).change(event => {
    const dateInput = event.target
    validateDate(dateInput)
  })

  if (precisionWidget) {
    const $dateWidget = $(precisionWidget)
      .closest(".form-group").parent()
      .find(".date-autoformat:first")
    $dateWidget.data("precision", getPrecisionOf(precisionWidget))

    $(precisionWidget).change(event => {
      const precision = getPrecisionOf(event.currentTarget)
      if (precision !== -1)
        setPrecision($dateWidget, precision, dateMasker)
    })

    if ($(precisionWidget).find(':checked').length === 0)
      $dateWidget.prop("disabled", "disabled")
        .prop("placeholder", "Precision is not set")
    else
      $(precisionWidget).change()
  }
  // Apply to each embed collections
  formBlockElement.querySelectorAll('.collection-wrapper').forEach(initDateMask);
}

function validateDate(dateInput) {
  const dateValid = (
    moment(dateInput.value, 'DD-MM-YYYY', true).isValid() || dateInput.disabled
  )
  if (dateValid) {
    $(dateInput).addClass('is-valid').removeClass('is-invalid')
    $(dateInput)[0].setCustomValidity("");
  }
  else {
    $(dateInput).addClass('is-invalid').removeClass('is-valid')
    $(dateInput)[0].setCustomValidity("Invalid date.");
  }
  // $(dateInput).closest("form")
  //   .find("button[type='submit']")
  //   .prop('disabled', !dateValid)
}

export function getPrecisionOf(precisionFormElt) {
  const $checkedInput = $(precisionFormElt).find(':checked')
  const value = parseInt($checkedInput.val())
  const precisionValues = $(precisionFormElt).find("input").toArray()
    .map(input => parseInt(input.value))

  const precision = precisionValues.indexOf(value)
  return ($checkedInput.prop('disabled') && precision !== 3) ? -1 : precision
}

function setPrecision($dateWidget, precision, dateMasker) {

  $(`label[for='${$dateWidget.attr('id')}']`)
    .toggleClass('required text-danger', precision !== 3)
  $dateWidget
    .prop('disabled', precision === 3 ? "disabled" : false)
    .prop('required', precision !== 3)
    .prop("placeholder", dateMasks[precision].placeholder)

  dateMasker.option(dateMasks[precision])
  dateMasker.mask($dateWidget[0])
  if (precision === 3 || $dateWidget.data('precision') !== precision) {
    $dateWidget.val(null)
  }
  validateDate($dateWidget[0])
  $dateWidget.data("precision", precision)
}

