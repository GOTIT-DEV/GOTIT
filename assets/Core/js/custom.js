
// Progressbar
if ($(".progress .progress-bar")[0]) {
	$('.progress .progress-bar').progressbar();
}
// /Progressbar

/* INPUT MASK */

function init_InputMask() {

	if (typeof ($.fn.inputmask) === 'undefined') { return; }
	console.log('init_InputMask');

	$(":input").inputmask();

};


$(document).ready(function () {
	$('[data-toggle="tooltip"]').tooltip({ container: 'body' });
	init_InputMask();
});


