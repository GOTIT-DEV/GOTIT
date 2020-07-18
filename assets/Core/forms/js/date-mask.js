const dateMaskConfig = {
  shiftPositions: false,
  regex: "[0-3]\\d-[0-1]\\d-[1-2]\\d{3}",
  tabThrough: true,
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
  const precisionWidget = formBlockElement.querySelector(".date-precision")
  const dateMasker = new Inputmask(dateMaskConfig)
  const dateWidget = formBlockElement.querySelector('.date-autoformat')
  if (dateWidget !== null)
    dateMasker.mask(dateWidget)

  if (precisionWidget !== null) {
    $(precisionWidget).change(event => {
      const $precisionChanged = $(event.currentTarget)
      const value = parseInt($precisionChanged.find(':checked').val())
      const precisionValues = $precisionChanged.find("input").toArray()
        .map(input => parseInt(input.value))

      const $dateWidget = $precisionChanged.closest(".form-group").parent()
        .find(".date-autoformat:first")

      const precision = precisionValues.indexOf(value)
      if (precision !== -1) {
        $dateWidget
          .prop('disabled', precision === 3)
          .prop('required', precision !== 3)
          .prop("placeholder", dateMasks[precision].placeholder)
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
        dateMasker.option(dateMasks[precision])
        dateMasker.mask($dateWidget)
      }
    })

    if ($(precisionWidget).find(':checked').length === 0) {
      $(precisionWidget).closest(".form-group").parent()
        .find(".date-autoformat:first").prop("disabled", true)
        .prop("placeholder", "Precision is not set")
    } else {
      $(precisionWidget).change()
    }
  }
}