
$(() => {
  const $form = $("form[name='bbees_e3sbundle_voc']")
  const $code = $form.find('#bbees_e3sbundle_voc_code')
  if ($form.data('action') == "new") {
    $code.keyup(function (e) {
      if ($code.val().includes('$')) {
          alert(' ! the $ character is not allowed. Please change for another')
      }
      const codeVoc = $code.val().replace(/\$/g, '').replace(/ /g, '_');
      $code.val(codeVoc);
    })
  }
})

