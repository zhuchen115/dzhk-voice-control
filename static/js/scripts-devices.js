$(document).ready(function () {
    var url = 'data/language/' + $('html').attr('lang').replace('-', '_') + '/js/devices.json';
    var lang;
    $.getJSON(url, function (data) {
        lang = data;
        $('.auth-btn, .shift-btn').each(function () {
            if ($(this).text() == '') {
                $(this).text(lang.viewdetails);
            }
        });
    });
    $('#btn-add').click(function () {
        $('#modal-adddev').modal('show');
    });


    $('#newdevsave').click(function () {
        $.getJSON('fontend.php', {class: 'device', action: 'bind', devid: $('#in-dev-id').val(), devname: $('#in-dev-name').val(), devshift: $('#in-dev-shift').val()}, function (data) {
            if (data.errno == 1001) {
                $('#modal-adddev').modal('hide');
                $('#col-devtable').prepend('<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
                $('#tbody-device').append('<tr><td>' + data.id + '</td><td>' + data.hid + '</td><td>' + data.name + '</td><td><button type="button" class="btn btn-primary btn-sm auth-btn" data-devid="' + data.id + '">' + lang.viewdetails + '</button></td><td><button type="button" class="btn btn-warning btn-sm shift-btn" data-devid="' + data.id + '">' + lang.viewdetails + '</button></td></tr>');
            } else {
                $('#mb-newdev').prepend('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $('#cname-save').click(function () {
        $.getJSON('fontend.php', {class: 'device', action: 'chname', id: $('#cname-id').text(), name: $('#cname-name').val()}, function (data) {
            switch (data.errno) {
                case 1001:
                    $('#modal-cname').modal('hide');
                    $('#col-devtable').prepend('<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
                    $(".name-btn [data-devid=" + $('#cname-id').text() + "]").html($('#cname-name').val() + '<i class="fa fa-pencil"></i>');
                    break;
                default:
                    $('#mb-chname').prepend('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $('.auth-btn').click(function (e) {
        var devid = $(this).data('devid');
        $.getJSON('fontend.php', {class: 'device', action: 'auth', id: devid}, function (data) {
            if (data.errno > 4000) {
                $('#col-devtable').prepend('<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            } else {
                if (data.global) {
                    $('#access-global').text('Yes');
                } else {
                    $('#access-global').text('No');
                }
                var loc = '<ul>';
                $.each(data.location, function (i, item) {
                    loc += '<li>' + item + '</li>';
                });
                loc += '</ul>';
                $('#access-location').html(loc);
                var obje = '<ul>';
                $.each(data.object, function (i, item) {
                    obje += '<li>' + item + '</li>';
                });
                obje += '</ul>';
                $('#access-object').html(obje);
                $('#modal-devauth').modal('show');
            }
        });
    });
    $('.name-btn').click(function (e) {
        var id = $(this).data('devid');
        var hid = $(this).parent().prev('td').text();
        var name = $(this).text();
        $('#cname-id').text(id);
        $('#cname-hid').text(hid);
        $('#cname-name').val(name);
        $('#modal-cname').modal('show');
    });
    $('.shift-btn').click(function (e) {
        var devid = $(this).data('devid');
        $.getJSON('fontend.php', {class: 'device', action: 'tfstatus', id: devid}, function (data) {
            if (data.errno == 1001) {
                $('#transfer-status').html('<p>' + lang.tfready + '<br>' + lang.shift + ':' + data.shift + '</p>');
                $('#tf-action').text(lang.ctransfer);
                $('#tfaction-help').text(lang.ctfhelp);
                $('#tf-action').data('devid', devid);
                $('#tf-action').data('action', 'notransfer');
                $('#modal-tfdev').modal('show');
            } else if (data.errno == 1002) {
                $('#transfer-status').html('<p>' + lang.notf + '</p>');
                $('#tf-action').text(lang.transfer);
                $('#tfaction-help').text(lang.tfhelp);
                $('#tf-action').data('devid', devid);
                $('#tf-action').data('action', 'transfer');
                $('#modal-tfdev').modal('show');
            } else {
                $('#col-devtable').prepend('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $('#tf-action').click(function (e) {
        var acti = $(this).data('action');
        var devid = $(this).data('devid');
        $.getJSON('fontend.php', {class: 'device', action: acti, id: devid}, function (data) {
            if (data.errno != 1001) {
                $('#col-devtable').prepend('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
                return;
            } else {
                if (acti == 'transfer') {
                    $('#transfer-status').html('<p>' + lang.tfready + '<br>' + lang.shift + ':' + data.shift + '</p>');
                    $('#tf-action').text(lang.ctransfer);
                    $('#tfaction-help').text(lang.ctfhelp);
                    $('#tf-action').data('devid', devid);
                    $('#tf-action').data('action', 'notransfer');
                } else if (acti == 'notransfer') {
                    $('#transfer-status').html('<p>' + lang.notf + '</p>');
                    $('#tf-action').text(lang.transfer);
                    $('#tfaction-help').text(lang.tfhelp);
                    $('#tf-action').data('devid', devid);
                    $('#tf-action').data('action', 'transfer');
                }
            }
        });
    });
});