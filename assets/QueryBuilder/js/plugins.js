import Inputmask from "inputmask"

$.fn.queryBuilder.define(
  "date-inputmask",
  function (options) {

    const dateMasker = new Inputmask(options)
    const dateTimeMasker = new Inputmask({
      mask: "9999-99-99 99:99:99",
      placeholder: "YYYY-MM-DD hh:mm:ss",
      regex: "[1-2]\\d{3}-[0-1]\\d-[0-3]\\d [0-2]\\d:[0-5]\\d:[0-5]\\d",
      shiftPositions: false,
      tabThrough: true,
    })

    let Selectors = $.fn.queryBuilder.constructor.selectors;

    this.on("afterCreateRuleInput", function (e, rule) {
      if (rule.filter.type === "date") {
        rule.$el
          .find(Selectors.value_container)
          .find("input")
          .prop("placeholder", options.placeholder);
        dateMasker.mask(rule.$el.get(0))
      }
      if (rule.filter.type === "datetime") {
        rule.$el
          .find(Selectors.value_container)
          .find("input")
          .prop("placeholder", "YYYY-MM-DD hh:mm:ss");
        dateTimeMasker.mask(rule.$el.get(0));
      }
    });
  },
  {
    mask: "9999-99-99",
    placeholder: "YYYY-MM-DD",
    regex: "[1-2]\\d{3}-[0-1]\\d-[0-3]\\d",
    shiftPositions: false,
    tabThrough: true,
  }
);
