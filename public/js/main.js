// Votre code JavaScript ici

jQuery(function ($) {

    var monAlert = $('#monAlert');
    console.log(monAlert);
    if (monAlert.length > 0) {
        monAlert.hide().slideDown(500).delay(3000).slideUp(500);
    }
});