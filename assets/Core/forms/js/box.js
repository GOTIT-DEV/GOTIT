$(() => {
  const $form = $("form[name='boite']");
  const $code = $form.find("#boite_codeBoite");
  if ($form.data("action") == "new") {
    $code.keyup(function (e) {
      const codeBoite = $code.val().replace(/ /g, "_");
      $code.val(codeBoite);
    });
  }
});
