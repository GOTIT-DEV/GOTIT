
$(() => {
  const $form = $("form[name='bbees_e3sbundle_voc']")
  const $code = $form.find('#bbees_e3sbundle_voc_code')
  if ($form.data('action') == "new") {
    $code.keyup(function (e) {
      const codeVoc = $code.val().replace(/\$/g, '');
      $code.val(codeVoc);
    })
  }
})

