
jQuery(document).ready(function () {

    /*
     Fullscreen background
     */
    $.backstretch([
        "static/img/backgrounds/2.jpg"
                , "static/img/backgrounds/3.jpg"
                , "static/img/backgrounds/1.jpg"
    ], {duration: 3000, fade: 750});

    /*
     Form validation
     */
    $('.login-form input[type="text"], .login-form input[type="password"], .login-form textarea').on('focus', function () {
        $(this).removeClass('input-error');
    });

    $('#btn-submit').click(function (e) {

        $('.login-form').find('input[type="text"], input[type="password"], textarea').each(function () {
            if ($(this).val() == "") {
                $(this).addClass('input-error');
                e.preventDefault();
            } else {
                $(this).removeClass('input-error');
                $.getJSON("fontend.php", {class: "auth", action: "userlogin", username: $('#form-username').val(), password: $('#form-password').val()}, function (data) {
                    if (data.errno == 1001) {
                        window.location.href = "?action=dashboard";
                    } else {
                        $("#logininfo").css("display", "block");
                    }
                });
            }
        });

    });


});
