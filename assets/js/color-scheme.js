'use strict'
$(document).ready(function () {

    var html = $('html');
    var body = $('body');

    /* layout modes dark-light */
    if (getCookie("goMobileuxlayoutmode") === 'dark-mode') {
        $('#btn-layout-modes-dark').prop('checked', true);
        html.addClass('dark-mode');
    } else {
        $('#btn-layout-modes-dark').prop('checked', false);
        html.removeClass('dark-mode');
    }

    $('#btn-layout-modes-dark').on('change', function () { // Changed from 'click' to 'change'
        if ($(this).is(':checked')) {
            setCookie('goMobileuxlayoutmode', 'dark-mode', 1);
            html.addClass('dark-mode').removeClass('light-mode');
        } else {
            setCookie('goMobileuxlayoutmode', 'light-mode', 1);
            html.addClass('light-mode').removeClass('dark-mode');
        }
    });


    /* Right to left to right directions  */
    if (getCookie('goMobileuxdirectionmode') === 'rtl') {
        $('#btn-layout-RTL').prop('checked', true);
        html.addClass('rtl');
        html.attr('dir', 'ltr');

    } else {
        $('#btn-layout-RTL').prop('checked', false);
        html.attr('dir', '');
        html.removeClass('rtl');
    }

    $('#btn-layout-RTL').on('click', function () {
        if ($(this).is(':checked')) {
            setCookie('goMobileuxdirectionmode', 'rtl', 1);
            html.attr('dir', 'rtl');
            html.addClass('rtl');

        } else {
            setCookie('goMobileuxdirectionmode', 'ltr', 1);
            html.attr('dir', '');
            html.removeClass('rtl');
        }
    });



    /* color style  */
    var curentstyle = body.attr('data-theme');
    if ($.type(getCookie("goMobileuxtheme")) != 'undefined' && getCookie("goMobileuxtheme") != '') {
        body.attr('data-theme', getCookie("goMobileuxtheme")).removeClass(curentstyle);
        body.addClass(getCookie("goMobileuxtheme"));
        curentstyle = getCookie("goMobileuxtheme");

        $('#theme-select .select-box').each(function () {
            if ($(this).attr('data-title') === getCookie("goMobileuxtheme")) {
                $(this).addClass("active");
            }
        });
        $('.personalise-color-list li').each(function () {
            if ($(this).attr('data-title') === getCookie("goMobileuxtheme")) {
                $(this).addClass("active");
            }
        });
    }

    $('.personalise-color-list li').on('click', function () {
        body.removeClass(body.attr('data-theme'));

        $('.personalise-color-list li').removeClass('active');
        var setstyle = $(this).attr('data-title');

        if ($(this).hasClass('active') != true && setstyle != '') {
            $(this).addClass('active');
            body.addClass(setstyle).attr('data-theme', setstyle);
            setCookie('goMobileuxtheme', setstyle, 1);
            curentstyle = setstyle;
        }
    });


});