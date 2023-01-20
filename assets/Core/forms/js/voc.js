
$(() => {
  const $form = $("form[name='bbees_e3sbundle_voc']")
  const $code = $form.find('#bbees_e3sbundle_voc_code')
  if ($form.data('action') == "new" || $form.data('action') == "edit") {
    $code.keyup(function (e) {
      if ($code.val().includes('$')) {
          alert(' ! the $ character is not allowed. Please change for another')
      }
      if ($form.data('action') == "new") {
        const codeVoc = $code.val().replace(/\$/g, '').replace(/ /g, '_');
      }
      if ($form.data('action') == "edit") {
        const codeVoc = $code.val().replace(/\$/g, '');
      }
      $code.val(codeVoc);
    })
  }
})

