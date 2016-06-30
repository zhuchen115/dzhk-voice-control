$(document).ready(function () {
    $('#btn-chpasswd').click(function (e) {
        if ($("#form-opassword").val() == '') {
            $("#fg-opassword").addClass("has-error");
            $("#form-opassword").focus();
        } else {
            $("#fg-opassword").removeClass("has-error");

            if ($("#form-password").val() != '') {
                
                if ($("#form-password").val() == $("#form-rpassword").val()) {
                    $("#form-password").popover('destroy');
                    $('#form-opassword').popover('destroy');
                    $("#fg-npassword").removeClass("has-error");
                    $("#fg-rpassword").removeClass("has-error");
                    $.getJSON('fontend.php', {class: 'auth', action: 'passwd', oldp: $("#form-opassword").val(), newp: $("#form-password").val()}, function (data) {
                        switch (data.errno) {
                            case 4003:
                                $("#fg-opassword").addClass("has-error");
                                $('#form-opassword').popover({title: 'Error', content: data.errmsg,container: 'body'});
                                $('#form-opassword').popover('show');
                                $("#form-opassword").focus();
                                break;
                            case 4005:
                                $("#fg-npassword").addClass("has-error");
                                $('#form-password').popover({title: 'Error', content: data.errmsg,container: 'body'});
                                $('#form-password').popover('show');
                                break;
                            case 1001:
                                $('#form-passwd').append('<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+data.errmsg+'</div>');
                                break;
                        }
                    });
                } else {
                    $("#fg-npassword").addClass("has-error");
                    $("#fg-rpassword").addClass("has-error");
                    //i18n Required
                    $("#form-password").popover({title: "Error", content: 'Password Mismatch', placement: 'right',container: 'body'});
                    $("#form-password").popover('show');
                }
            }else{
                $.getJSON('fontend.php', {class: 'auth', action:'usermod',type:'email',data:$('#form-email').val()},function(data){
                     switch (data.errno){
                        case 1001:
                             $('#form-passwd').prepend('<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+data.errmsg+'</div>');
                             break;
                     }
                });
            }
        }
    });
    $('#btn-userinfo').click(function(e){
        var types = [];
        var datas = [];
        $('#form-uinfo').find('input[type=text], textarea, input[type=radio][checked] input[type=checkbox][checked]').each(function(i,e){
            types = $.merge([$(e).data('utype')],types);
            datas = $.merge([$(e).val()],datas);
        });
        if(types.length ===1){
            types = types[0];
            datas = datas[0];
        }
        $.getJSON('fontend.php',{class:'auth',action:'usermod',type:types,data:datas},function(data){
            switch(data.errno){
                case 1001:
                    $('#form-uinfo').prepend('<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+data.errmsg+'</div>');
            }
        });
    });
});