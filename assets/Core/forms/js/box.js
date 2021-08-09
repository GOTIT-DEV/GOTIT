$(() => {
  const $form = $("form[name='store']");
  const $code = $form.find("#store_codeBoite");
  if ($form.data("action") == "new") {
    $code.keyup(function (e) {
      const codeBoite = $code.val().replace(/ /g, "_");
      $code.val(codeBoite);
    });
  }
});
