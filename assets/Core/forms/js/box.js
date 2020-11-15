
$(() => {
  const $form = $("form[name='bbees_e3sbundle_boite']")
  const $code = $form.find('#bbees_e3sbundle_boite_codeBoite')
  if ($form.data('action') == "new") {
    $code.keyup(function (e) {
      const codeBoite = $code.val().replace(/ /g, '_');
      $code.val(codeBoite);
    })
  }
})