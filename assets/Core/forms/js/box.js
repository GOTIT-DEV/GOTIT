
$(() => {
  const $form = $("form[name='bbees_e3sbundle_boite']")
  const $code = $form.find('#bbees_e3sbundle_boite_codeBoite')
  if ($form.data('action') == "new") {
    $code.keyup(function (e) {
      const codeBoite = $code.val().replace(/ /g, '_');
      if ($code.val().includes('$')) {
          alert(' ! the $ character is not allowed. Please change for another')
          const codeBoite = $code.val().replace(/\$/g, '');
      }
      $code.val(codeBoite);
    })
  }
  // remove btn-entry-delete button
  
})
