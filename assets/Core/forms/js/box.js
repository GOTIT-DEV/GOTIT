
$(() => {
  const $form = $("form[name='bbees_e3sbundle_boite']")
  const $code = $form.find('#bbees_e3sbundle_boite_codeBoite')
  if ($form.data('action') == "new") {
    $code.keyup(function (e) {
      if ($code.val().includes('$')) {
          alert(' ! the $ character is not allowed. Please change for another')
      }
      const codeBoite = $code.val().replace(/\$/g, '').replace(/ /g, '_');
      $code.val(codeBoite);
    })
  }
  if ($form.data('action') == "edit") {
    $code.keyup(function (e) {
      if ($code.val().includes('$')) {
          alert(' ! the $ character is not allowed. Please change for another')
      }
      const codeBoite = $code.val().replace(/\$/g, '');
      $code.val(codeBoite);
    })
  }
  // remove btn-entry-delete button
  
})
