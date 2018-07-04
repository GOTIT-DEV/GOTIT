function scrollTo(elt_id, time = 1000) {
    $('html, body').animate({
        scrollTop: $(elt_id).offset().top
    }, time);
}

function initSwitchery(selector, size = 'small') {

    var elems = Array.prototype.slice.call(document.querySelectorAll(selector));
    elems.forEach(function(html) {
        var switchery = new Switchery(html, { size: size });
    });

}

function toggleFormSelect(event) {
    switch (event.target.value) {
        case 0:
            $(".methode-select").prop('disabled', true);
            $(".taxa-select").prop('disabled', true);
            break;

        case 1:
            $(".methode-select").prop('disabled', true);
            $(".taxa-select").prop('disabled', false);
            break;

        case 2:
            $(".methode-select").prop('disabled', false);
            $(".methode-select").prop('disabled', true);
            break;
    }
}

function onDateMotuSelected(dateFormModule, submitBtn, mode = "select") { // mode : 'select' or 'checkbox'
    var module = $(dateFormModule);
    var spinners = $(module.data('spinner'));
    var dateMotu = module.find("select[name='date_methode']");
    var methode = module.find("select[name='methode']");
    $(submitBtn).prop("disabled", true);
    $(spinners).removeClass("hidden");
    $.post(
        module.data('url'), { date_methode: dateMotu.val() },
        function(response) {
            if (mode == 'select') {
                methode.html('');
                for (i = 0; i < response.data.length; i++) {
                    methode.append(
                        Mustache.render('<option value={{id}}>{{code}}</option>', response.data[i]));
                }
            } else if (mode == 'checkbox') {
                var container = module.find('#methodes-container');
                var template = module.find("#method-form-checkbox").html();
                container.html('');
                for (i = 0; i < response.data.length; i++) {
                    container.append(Mustache.render(template, response.data[i]));
                }
            }
            $(submitBtn).prop("disabled", false);
            $(spinners).addClass("hidden");
        });
}