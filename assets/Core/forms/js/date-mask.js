import Inputmask from "inputmask"
import moment from "moment"

const dateMaskConfig = {
  shiftPositions: false,
  tabThrough: true,
  regex: "[0-3]\\d-[0-1]\\d-[1-2]\\d{3}",
  mask: "99-99-9999",
  placeholder: "DD-MM-YYYY"
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
    mask: "\\0\\1-\\0\\1-9999",
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

  if (dateWidget !== null)
    dateMasker.mask(dateWidget)

  if (precisionWidget !== null) {
    $(precisionWidget).change(event => {
      const precision = getPrecisionOf(event.currentTarget)
      const $dateWidget = $(event.currentTarget)
        .closest(".form-group").parent()
        .find(".date-autoformat:first")
      if (precision !== -1)
        setPrecision($dateWidget, precision, dateMasker)
    })

    if ($(precisionWidget).find(':checked').length === 0)
      $(precisionWidget).closest(".form-group").parent()
        .find(".date-autoformat:first").prop("disabled", true)
        .prop("placeholder", "Precision is not set")
    else
      $(precisionWidget).change()
  }
  // Apply to each embed collections
  formBlockElement.querySelectorAll('.collection-wrapper').forEach(initDateMask);
}

function validateDate(dateInput) {
  const dateValid = (
    moment(dateInput.value, 'DD-MM-YYYY').isValid() || dateInput.disabled
  )
  if (dateValid) {
    $(dateInput).addClass('is-valid').removeClass('is-invalid')
  }
  else {
    $(dateInput).addClass('is-invalid').removeClass('is-valid')
  }
  $(dateInput).closest("form")
    .find("button[type='submit']")
    .prop('disabled', !dateValid)
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
  $dateWidget
    .prop('disabled', precision === 3)
    .prop('required', precision !== 3)
    .prop("placeholder", dateMasks[precision].placeholder)

  $(`label[for='${$dateWidget.attr('id')}']`)
    .toggleClass('required text-danger', precision !== 3)

  const currentValue = $dateWidget.val()
  if (precision === 3) {
    $dateWidget.val('NA')
  }
  else if (currentValue.length >= 4) {
    if (currentValue.length > 3 * (3 - precision)) {
      let paddedDate = "X".repeat(10 - currentValue.length) + currentValue
      $dateWidget.val("01-".repeat(precision) + paddedDate.slice(3 * precision))
    }
  }
  validateDate($dateWidget[0])
  dateMasker.option(dateMasks[precision])
  dateMasker.mask($dateWidget[0])
}

