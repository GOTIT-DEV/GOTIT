/**
 * Resize function without multiple trigger
 * 
 * Usage:
 * $(window).smartresize(function(){  
 *     // code here
 * });
 */
(function ($, sr) {
	// debouncing function from John Hann
	// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
	var debounce = function (func, threshold, execAsap) {
		var timeout;

		return function debounced() {
			var obj = this, args = arguments;
			function delayed() {
				if (!execAsap)
					func.apply(obj, args);
				timeout = null;
			}

			if (timeout)
				clearTimeout(timeout);
			else if (execAsap)
				func.apply(obj, args);

			timeout = setTimeout(delayed, threshold || 100);
		};
	};

	// smartresize 
	jQuery.fn[sr] = function (fn) { return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery, 'smartresize');


var randNum = function () {
	return (Math.floor(Math.random() * (1 + 40 - 20))) + 20;
};


// Panel toolbox
$(document).ready(function () {
	$('.collapse-link').on('click', function () {
		var $BOX_PANEL = $(this).closest('.x_panel'),
			$ICON = $(this).find('i'),
			$BOX_CONTENT = $BOX_PANEL.find('.x_content');

		// fix for some div with hardcoded fix class
		if ($BOX_PANEL.attr('style')) {
			$BOX_CONTENT.slideToggle(200, function () {
				$BOX_PANEL.removeAttr('style');
			});
		} else {
			$BOX_CONTENT.slideToggle(200);
			$BOX_PANEL.css('height', 'auto');
		}

		$ICON.toggleClass('fa-chevron-up fa-chevron-down');
	});

	$('.close-link').click(function () {
		var $BOX_PANEL = $(this).closest('.x_panel');

		$BOX_PANEL.remove();
	});
});
// /Panel toolbox

// Tooltip
$(document).ready(function () {
	$('[data-toggle="tooltip"]').tooltip({
		container: 'body'
	});
});
// /Tooltip

// Progressbar
if ($(".progress .progress-bar")[0]) {
	$('.progress .progress-bar').progressbar();
}
// /Progressbar


// iCheck
$(document).ready(function () {
	if ($("input.flat")[0]) {
		$(document).ready(function () {
			$('input.flat').iCheck({
				checkboxClass: 'icheckbox_flat-green',
				radioClass: 'iradio_flat-green'
			});
		});
	}
});
// /iCheck

// Table
$('table input').on('ifChecked', function () {
	checkState = '';
	$(this).parent().parent().parent().addClass('selected');
	countChecked();
});
$('table input').on('ifUnchecked', function () {
	checkState = '';
	$(this).parent().parent().parent().removeClass('selected');
	countChecked();
});

var checkState = '';

$('.bulk_action input').on('ifChecked', function () {
	checkState = '';
	$(this).parent().parent().parent().addClass('selected');
	countChecked();
});
$('.bulk_action input').on('ifUnchecked', function () {
	checkState = '';
	$(this).parent().parent().parent().removeClass('selected');
	countChecked();
});
$('.bulk_action input#check-all').on('ifChecked', function () {
	checkState = 'all';
	countChecked();
});
$('.bulk_action input#check-all').on('ifUnchecked', function () {
	checkState = 'none';
	countChecked();
});

function countChecked() {
	if (checkState === 'all') {
		$(".bulk_action input[name='table_records']").iCheck('check');
	}
	if (checkState === 'none') {
		$(".bulk_action input[name='table_records']").iCheck('uncheck');
	}

	var checkCount = $(".bulk_action input[name='table_records']:checked").length;

	if (checkCount) {
		$('.column-title').hide();
		$('.bulk-actions').show();
		$('.action-cnt').html(checkCount + ' Records Selected');
	} else {
		$('.column-title').show();
		$('.bulk-actions').hide();
	}
}



// Accordion
$(document).ready(function () {
	$(".expand").on("click", function () {
		$(this).next().slideToggle(200);
		$expand = $(this).find(">:first-child");

		if ($expand.text() == "+") {
			$expand.text("-");
		} else {
			$expand.text("+");
		}
	});
});

// NProgress
if (typeof NProgress != 'undefined') {
	$(document).ready(function () {
		NProgress.start();
	});

	$(window).load(function () {
		NProgress.done();
	});
}


//hover and retain popover when on popover content
var originalLeave = $.fn.popover.Constructor.prototype.leave;
$.fn.popover.Constructor.prototype.leave = function (obj) {
	var self = obj instanceof this.constructor ?
		obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type);
	var container, timeout;

	originalLeave.call(this, obj);

	if (obj.currentTarget) {
		container = $(obj.currentTarget).siblings('.popover');
		timeout = self.timeout;
		container.one('mouseenter', function () {
			//We entered the actual popover – call off the dogs
			clearTimeout(timeout);
			//Let's monitor popover content instead
			container.one('mouseleave', function () {
				$.fn.popover.Constructor.prototype.leave.call(self, self);
			});
		});
	}
};

$('body').popover({
	selector: '[data-popover]',
	trigger: 'click hover',
	delay: {
		show: 50,
		hide: 400
	}
});


function gd(year, month, day) {
	return new Date(year, month - 1, day).getTime();
}


/* INPUTS */

function onAddTag(tag) {
	alert("Added a tag: " + tag);
}

function onRemoveTag(tag) {
	alert("Removed a tag: " + tag);
}

function onChangeTag(input, tag) {
	alert("Changed a tag: " + tag);
}


/* WYSIWYG EDITOR */

function init_wysiwyg() {

	if (typeof ($.fn.wysiwyg) === 'undefined') { return; }
	console.log('init_wysiwyg');

	function init_ToolbarBootstrapBindings() {
		var fonts = ['Serif', 'Sans', 'Arial', 'Arial Black', 'Courier',
			'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
			'Times New Roman', 'Verdana'
		],
			fontTarget = $('[title=Font]').siblings('.dropdown-menu');
		$.each(fonts, function (idx, fontName) {
			fontTarget.append($('<li><a data-edit="fontName ' + fontName + '" style="font-family:\'' + fontName + '\'">' + fontName + '</a></li>'));
		});
		$('a[title]').tooltip({
			container: 'body'
		});
		$('.dropdown-menu input').click(function () {
			return false;
		})
			.change(function () {
				$(this).parent('.dropdown-menu').siblings('.dropdown-toggle').dropdown('toggle');
			})
			.keydown('esc', function () {
				this.value = '';
				$(this).change();
			});

		$('[data-role=magic-overlay]').each(function () {
			var overlay = $(this),
				target = $(overlay.data('target'));
			overlay.css('opacity', 0).css('position', 'absolute').offset(target.offset()).width(target.outerWidth()).height(target.outerHeight());
		});

		if ("onwebkitspeechchange" in document.createElement("input")) {
			var editorOffset = $('#editor').offset();

			$('.voiceBtn').css('position', 'absolute').offset({
				top: editorOffset.top,
				left: editorOffset.left + $('#editor').innerWidth() - 35
			});
		} else {
			$('.voiceBtn').hide();
		}
	}

	function showErrorAlert(reason, detail) {
		var msg = '';
		if (reason === 'unsupported-file-type') {
			msg = "Unsupported format " + detail;
		} else {
			console.log("error uploading file", reason, detail);
		}
		$('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>' +
			'<strong>File upload error</strong> ' + msg + ' </div>').prependTo('#alerts');
	}

	$('.editor-wrapper').each(function () {
		var id = $(this).attr('id');	//editor-one

		$(this).wysiwyg({
			toolbarSelector: '[data-target="#' + id + '"]',
			fileUploadError: showErrorAlert
		});
	});


	window.prettyPrint;
	prettyPrint();

};

/* CROPPER */

function init_cropper() {


	if (typeof ($.fn.cropper) === 'undefined') { return; }
	console.log('init_cropper');

	var $image = $('#image');
	var $download = $('#download');
	var $dataX = $('#dataX');
	var $dataY = $('#dataY');
	var $dataHeight = $('#dataHeight');
	var $dataWidth = $('#dataWidth');
	var $dataRotate = $('#dataRotate');
	var $dataScaleX = $('#dataScaleX');
	var $dataScaleY = $('#dataScaleY');
	var options = {
		aspectRatio: 16 / 9,
		preview: '.img-preview',
		crop: function (e) {
			$dataX.val(Math.round(e.x));
			$dataY.val(Math.round(e.y));
			$dataHeight.val(Math.round(e.height));
			$dataWidth.val(Math.round(e.width));
			$dataRotate.val(e.rotate);
			$dataScaleX.val(e.scaleX);
			$dataScaleY.val(e.scaleY);
		}
	};


	// Tooltip
	$('[data-toggle="tooltip"]').tooltip();


	// Cropper
	$image.on({
		'build.cropper': function (e) {
			console.log(e.type);
		},
		'built.cropper': function (e) {
			console.log(e.type);
		},
		'cropstart.cropper': function (e) {
			console.log(e.type, e.action);
		},
		'cropmove.cropper': function (e) {
			console.log(e.type, e.action);
		},
		'cropend.cropper': function (e) {
			console.log(e.type, e.action);
		},
		'crop.cropper': function (e) {
			console.log(e.type, e.x, e.y, e.width, e.height, e.rotate, e.scaleX, e.scaleY);
		},
		'zoom.cropper': function (e) {
			console.log(e.type, e.ratio);
		}
	}).cropper(options);


	// Buttons
	if (!$.isFunction(document.createElement('canvas').getContext)) {
		$('button[data-method="getCroppedCanvas"]').prop('disabled', true);
	}

	if (typeof document.createElement('cropper').style.transition === 'undefined') {
		$('button[data-method="rotate"]').prop('disabled', true);
		$('button[data-method="scale"]').prop('disabled', true);
	}


	// Download
	if (typeof $download[0].download === 'undefined') {
		$download.addClass('disabled');
	}


	// Options
	$('.docs-toggles').on('change', 'input', function () {
		var $this = $(this);
		var name = $this.attr('name');
		var type = $this.prop('type');
		var cropBoxData;
		var canvasData;

		if (!$image.data('cropper')) {
			return;
		}

		if (type === 'checkbox') {
			options[name] = $this.prop('checked');
			cropBoxData = $image.cropper('getCropBoxData');
			canvasData = $image.cropper('getCanvasData');

			options.built = function () {
				$image.cropper('setCropBoxData', cropBoxData);
				$image.cropper('setCanvasData', canvasData);
			};
		} else if (type === 'radio') {
			options[name] = $this.val();
		}

		$image.cropper('destroy').cropper(options);
	});


	// Methods
	$('.docs-buttons').on('click', '[data-method]', function () {
		var $this = $(this);
		var data = $this.data();
		var $target;
		var result;

		if ($this.prop('disabled') || $this.hasClass('disabled')) {
			return;
		}

		if ($image.data('cropper') && data.method) {
			data = $.extend({}, data); // Clone a new one

			if (typeof data.target !== 'undefined') {
				$target = $(data.target);

				if (typeof data.option === 'undefined') {
					try {
						data.option = JSON.parse($target.val());
					} catch (e) {
						console.log(e.message);
					}
				}
			}

			result = $image.cropper(data.method, data.option, data.secondOption);

			switch (data.method) {
				case 'scaleX':
				case 'scaleY':
					$(this).data('option', -data.option);
					break;

				case 'getCroppedCanvas':
					if (result) {

						// Bootstrap's Modal
						$('#getCroppedCanvasModal').modal().find('.modal-body').html(result);

						if (!$download.hasClass('disabled')) {
							$download.attr('href', result.toDataURL());
						}
					}

					break;
			}

			if ($.isPlainObject(result) && $target) {
				try {
					$target.val(JSON.stringify(result));
				} catch (e) {
					console.log(e.message);
				}
			}

		}
	});

	// Keyboard
	$(document.body).on('keydown', function (e) {
		if (!$image.data('cropper') || this.scrollTop > 300) {
			return;
		}

		switch (e.which) {
			case 37:
				e.preventDefault();
				$image.cropper('move', -1, 0);
				break;

			case 38:
				e.preventDefault();
				$image.cropper('move', 0, -1);
				break;

			case 39:
				e.preventDefault();
				$image.cropper('move', 1, 0);
				break;

			case 40:
				e.preventDefault();
				$image.cropper('move', 0, 1);
				break;
		}
	});

	// Import image
	var $inputImage = $('#inputImage');
	var URL = window.URL || window.webkitURL;
	var blobURL;

	if (URL) {
		$inputImage.change(function () {
			var files = this.files;
			var file;

			if (!$image.data('cropper')) {
				return;
			}

			if (files && files.length) {
				file = files[0];

				if (/^image\/\w+$/.test(file.type)) {
					blobURL = URL.createObjectURL(file);
					$image.one('built.cropper', function () {

						// Revoke when load complete
						URL.revokeObjectURL(blobURL);
					}).cropper('reset').cropper('replace', blobURL);
					$inputImage.val('');
				} else {
					window.alert('Please choose an image file.');
				}
			}
		});
	} else {
		$inputImage.prop('disabled', true).parent().addClass('disabled');
	}


};

/* CROPPER --- end */

/* INPUT MASK */

function init_InputMask() {

	if (typeof ($.fn.inputmask) === 'undefined') { return; }
	console.log('init_InputMask');

	$(":input").inputmask();

};


/* DATERANGEPICKER */

function init_daterangepicker() {

	if (typeof ($.fn.daterangepicker) === 'undefined') { return; }
	console.log('init_daterangepicker');

	var cb = function (start, end, label) {
		console.log(start.toISOString(), end.toISOString(), label);
		$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	};

	var optionSet1 = {
		startDate: moment().subtract(29, 'days'),
		endDate: moment(),
		minDate: '01/01/2012',
		maxDate: '12/31/2015',
		dateLimit: {
			days: 60
		},
		showDropdowns: true,
		showWeekNumbers: true,
		timePicker: false,
		timePickerIncrement: 1,
		timePicker12Hour: true,
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		},
		opens: 'left',
		buttonClasses: ['btn btn-default'],
		applyClass: 'btn-small btn-primary',
		cancelClass: 'btn-small',
		format: 'MM/DD/YYYY',
		separator: ' to ',
		locale: {
			applyLabel: 'Submit',
			cancelLabel: 'Clear',
			fromLabel: 'From',
			toLabel: 'To',
			customRangeLabel: 'Custom',
			daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
			monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
			firstDay: 1
		}
	};

	$('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
	$('#reportrange').daterangepicker(optionSet1, cb);
	$('#reportrange').on('show.daterangepicker', function () {
		console.log("show event fired");
	});
	$('#reportrange').on('hide.daterangepicker', function () {
		console.log("hide event fired");
	});
	$('#reportrange').on('apply.daterangepicker', function (ev, picker) {
		console.log("apply event fired, start/end dates are " + picker.startDate.format('MMMM D, YYYY') + " to " + picker.endDate.format('MMMM D, YYYY'));
	});
	$('#reportrange').on('cancel.daterangepicker', function (ev, picker) {
		console.log("cancel event fired");
	});
	$('#options1').click(function () {
		$('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
	});
	$('#options2').click(function () {
		$('#reportrange').data('daterangepicker').setOptions(optionSet2, cb);
	});
	$('#destroy').click(function () {
		$('#reportrange').data('daterangepicker').remove();
	});

}

function init_daterangepicker_right() {

	if (typeof ($.fn.daterangepicker) === 'undefined') { return; }
	console.log('init_daterangepicker_right');

	var cb = function (start, end, label) {
		console.log(start.toISOString(), end.toISOString(), label);
		$('#reportrange_right span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	};

	var optionSet1 = {
		startDate: moment().subtract(29, 'days'),
		endDate: moment(),
		minDate: '01/01/2012',
		maxDate: '12/31/2020',
		dateLimit: {
			days: 60
		},
		showDropdowns: true,
		showWeekNumbers: true,
		timePicker: false,
		timePickerIncrement: 1,
		timePicker12Hour: true,
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		},
		opens: 'right',
		buttonClasses: ['btn btn-default'],
		applyClass: 'btn-small btn-primary',
		cancelClass: 'btn-small',
		format: 'MM/DD/YYYY',
		separator: ' to ',
		locale: {
			applyLabel: 'Submit',
			cancelLabel: 'Clear',
			fromLabel: 'From',
			toLabel: 'To',
			customRangeLabel: 'Custom',
			daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
			monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
			firstDay: 1
		}
	};

	$('#reportrange_right span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));

	$('#reportrange_right').daterangepicker(optionSet1, cb);

	$('#reportrange_right').on('show.daterangepicker', function () {
		console.log("show event fired");
	});
	$('#reportrange_right').on('hide.daterangepicker', function () {
		console.log("hide event fired");
	});
	$('#reportrange_right').on('apply.daterangepicker', function (ev, picker) {
		console.log("apply event fired, start/end dates are " + picker.startDate.format('MMMM D, YYYY') + " to " + picker.endDate.format('MMMM D, YYYY'));
	});
	$('#reportrange_right').on('cancel.daterangepicker', function (ev, picker) {
		console.log("cancel event fired");
	});

	$('#options1').click(function () {
		$('#reportrange_right').data('daterangepicker').setOptions(optionSet1, cb);
	});

	$('#options2').click(function () {
		$('#reportrange_right').data('daterangepicker').setOptions(optionSet2, cb);
	});

	$('#destroy').click(function () {
		$('#reportrange_right').data('daterangepicker').remove();
	});

}



function init_daterangepicker_reservation() {

	if (typeof ($.fn.daterangepicker) === 'undefined') { return; }
	console.log('init_daterangepicker_reservation');

	$('#reservation').daterangepicker(null, function (start, end, label) {
		console.log(start.toISOString(), end.toISOString(), label);
	});

	$('#reservation-time').daterangepicker({
		timePicker: true,
		timePickerIncrement: 30,
		locale: {
			format: 'MM/DD/YYYY h:mm A'
		}
	});

}



/* VALIDATOR */

function init_validator() {

	if (typeof (validator) === 'undefined') { return; }
	console.log('init_validator');

	// initialize the validator function
	validator.message.date = 'not a real date';

	// validate a field on "blur" event, a 'select' on 'change' event & a '.reuired' classed multifield on 'keyup':
	$('form')
		.on('blur', 'input[required], input.optional, select.required', validator.checkField)
		.on('change', 'select.required', validator.checkField)
		.on('keypress', 'input[required][pattern]', validator.keypress);

	$('.multi.required').on('keyup blur', 'input', function () {
		validator.checkField.apply($(this).siblings().last()[0]);
	});

	$('form').submit(function (e) {
		e.preventDefault();
		var submit = true;

		// evaluate the form using generic validaing
		if (!validator.checkAll($(this))) {
			submit = false;
		}

		if (submit)
			this.submit();

		return false;
	});

};



/* CUSTOM NOTIFICATION */

function init_CustomNotification() {

	console.log('run_customtabs');

	if (typeof (CustomTabs) === 'undefined') { return; }
	console.log('init_CustomTabs');

	var cnt = 10;

	TabbedNotification = function (options) {
		var message = "<div id='ntf" + cnt + "' class='text alert-" + options.type + "' style='display:none'><h2><i class='fas fa-bell'></i> " + options.title +
			"</h2><div class='close'><a href='javascript:;' class='notification_close'><i class='fas fa-times'></i></a></div><p>" + options.text + "</p></div>";

		if (!document.getElementById('custom_notifications')) {
			alert('doesnt exists');
		} else {
			$('#custom_notifications ul.notifications').append("<li><a id='ntlink" + cnt + "' class='alert-" + options.type + "' href='#ntf" + cnt + "'><i class='fas fa-bell animated shake'></i></a></li>");
			$('#custom_notifications #notif-group').append(message);
			cnt++;
			CustomTabs(options);
		}
	};

	CustomTabs = function (options) {
		$('.tabbed_notifications > div').hide();
		$('.tabbed_notifications > div:first-of-type').show();
		$('#custom_notifications').removeClass('dsp_none');
		$('.notifications a').click(function (e) {
			e.preventDefault();
			var $this = $(this),
				tabbed_notifications = '#' + $this.parents('.notifications').data('tabbed_notifications'),
				others = $this.closest('li').siblings().children('a'),
				target = $this.attr('href');
			others.removeClass('active');
			$this.addClass('active');
			$(tabbed_notifications).children('div').hide();
			$(target).show();
		});
	};

	CustomTabs();

	var tabid = idname = '';

	$(document).on('click', '.notification_close', function (e) {
		idname = $(this).parent().parent().attr("id");
		tabid = idname.substr(-2);
		$('#ntf' + tabid).remove();
		$('#ntlink' + tabid).parent().remove();
		$('.notifications a').first().addClass('active');
		$('#notif-group div').first().css('display', 'block');
	});

};


/* COMPOSE */

function init_compose() {

	if (typeof ($.fn.slideToggle) === 'undefined') { return; }
	console.log('init_compose');

	$('#compose, .compose-close').click(function () {
		$('.compose').slideToggle();
	});

};

/* CALENDAR */

function init_calendar() {

	if (typeof ($.fn.fullCalendar) === 'undefined') { return; }
	console.log('init_calendar');

	var date = new Date(),
		d = date.getDate(),
		m = date.getMonth(),
		y = date.getFullYear(),
		started,
		categoryClass;

	var calendar = $('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listMonth'
		},
		selectable: true,
		selectHelper: true,
		select: function (start, end, allDay) {
			$('#fc_create').click();

			started = start;
			ended = end;

			$(".antosubmit").on("click", function () {
				var title = $("#title").val();
				if (end) {
					ended = end;
				}

				categoryClass = $("#event_type").val();

				if (title) {
					calendar.fullCalendar('renderEvent', {
						title: title,
						start: started,
						end: end,
						allDay: allDay
					},
						true // make the event "stick"
					);
				}

				$('#title').val('');

				calendar.fullCalendar('unselect');

				$('.antoclose').click();

				return false;
			});
		},
		eventClick: function (calEvent, jsEvent, view) {
			$('#fc_edit').click();
			$('#title2').val(calEvent.title);

			categoryClass = $("#event_type").val();

			$(".antosubmit2").on("click", function () {
				calEvent.title = $("#title2").val();

				calendar.fullCalendar('updateEvent', calEvent);
				$('.antoclose2').click();
			});

			calendar.fullCalendar('unselect');
		},
		editable: true,
		events: [{
			title: 'All Day Event',
			start: new Date(y, m, 1)
		}, {
			title: 'Long Event',
			start: new Date(y, m, d - 5),
			end: new Date(y, m, d - 2)
		}, {
			title: 'Meeting',
			start: new Date(y, m, d, 10, 30),
			allDay: false
		}, {
			title: 'Lunch',
			start: new Date(y, m, d + 14, 12, 0),
			end: new Date(y, m, d, 14, 0),
			allDay: false
		}, {
			title: 'Birthday Party',
			start: new Date(y, m, d + 1, 19, 0),
			end: new Date(y, m, d + 1, 22, 30),
			allDay: false
		}, {
			title: 'Click for Google',
			start: new Date(y, m, 28),
			end: new Date(y, m, 29),
			url: 'http://google.com/'
		}]
	});

};

/* DATA TABLES */

function init_DataTables() {

	console.log('run_datatables');

	if (typeof ($.fn.DataTable) === 'undefined') { return; }
	console.log('init_DataTables');

	var handleDataTableButtons = function () {
		if ($("#datatable-buttons").length) {
			$("#datatable-buttons").DataTable({
				dom: "Blfrtip",
				buttons: [
					{
						extend: "copy",
						className: "btn-sm"
					},
					{
						extend: "csv",
						className: "btn-sm"
					},
					{
						extend: "excel",
						className: "btn-sm"
					},
					{
						extend: "pdfHtml5",
						className: "btn-sm"
					},
					{
						extend: "print",
						className: "btn-sm"
					},
				],
				responsive: true
			});
		}
	};

	TableManageButtons = function () {
		"use strict";
		return {
			init: function () {
				handleDataTableButtons();
			}
		};
	}();

	$('#datatable').dataTable();

	$('#datatable-keytable').DataTable({
		keys: true
	});

	$('#datatable-responsive').DataTable();

	$('#datatable-scroller').DataTable({
		ajax: "js/datatables/json/scroller-demo.json",
		deferRender: true,
		scrollY: 380,
		scrollCollapse: true,
		scroller: true
	});

	$('#datatable-fixed-header').DataTable({
		fixedHeader: true
	});

	var $datatable = $('#datatable-checkbox');

	$datatable.dataTable({
		'order': [[1, 'asc']],
		'columnDefs': [
			{ orderable: false, targets: [0] }
		]
	});
	$datatable.on('draw.dt', function () {
		$('checkbox input').iCheck({
			checkboxClass: 'icheckbox_flat-green'
		});
	});

	TableManageButtons.init();

};


$(document).ready(function () {

	init_wysiwyg();
	init_InputMask();
	init_cropper();
	init_daterangepicker();
	init_daterangepicker_right();
	init_daterangepicker_reservation();
	init_validator();
	init_DataTables();
	init_calendar();
	init_compose();
	init_CustomNotification();

});


