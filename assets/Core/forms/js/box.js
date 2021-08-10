$(() => {
  const $form = $("form[name='store']");
  const $code = $form.find("#store_code");
  if ($form.data("action") == "new") {
    $code.keyup(function (e) {
      const code = $code.val().replace(/ /g, "_");
      $code.val(code);
    });
  }
});
