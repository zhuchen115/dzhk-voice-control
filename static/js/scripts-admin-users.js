$(document).ready(function (e) {
    var url = 'data/language/' + $('html').attr('lang').replace('-', '_') + '/js/admin.json';
    var lang;
    $.getJSON(url, function (data) {
        lang = data;
    });
    // Begin Group Function
    $('#btn-user-search').click(function (ea) {
        if ($('#form-usearch').val() == '') {
            $('#form-usearch').focus();
        } else {
            $.getJSON('admin.php', {class: 'useradmin', action: 'usearch', keyword: $('#form-usearch').val()}, function (data) {
                var tb = '';
                $.each(data, function (idx, it) {
                    tb += '<tr><td class="td-uid">' + it.id + '</td><td class="td-username">' + it.username + '</td><td><button type="button" class="btn btn-warning btn-xs chpasswd-btn" data-uid="' + it.id + '">' + lang.chpasswd + '</button></td><td>' + it.email + '<button class="btn  btn-primary btn-xs pull-right eemail-btn" type="button" data-uid="' + it.id + '"><i class="fa fa-pencil"></i></button></td><td>' + it.groups + '<button class="btn  btn-primary btn-xs pull-right egroups-btn" type="button" data-uid="' + it.id + '"><i class="fa fa-pencil"></i></button></td><td>' + it.name + '<button class="btn  btn-primary btn-xs pull-right ename-btn" type="button" data-uid="' + it.id + '"><i class="fa fa-pencil"></i></button></td><td><button type="button" class="btn btn-success btn-xs vauth-btn" data-uid="' + it.id + '">' + lang.details + '</button></td><td><button type="button" class="btn btn-danger btn-xs uremove-btn" data-uid="' + it.id + '"><i class="fa fa-remove"></i></button></td></tr>';
                });
                if (tb == '') {
                    tb = '<tr><td colspan="8">' + lang.notfound + '</td></tr>';
                }
                $('#tbody-users').html(tb);
            });
        }
    });
    $('#form-usearch').keydown(function (e) {
        if (e.keyCode == 13) {
            $('#btn-user-search').click();
            return false;
        }
    });
    $(document).on('click', '.chpasswd-btn', function () {
        var uid = $(this).data('uid');
        var username = $(this).parent().siblings('.td-username').text();
        $('#chpasswd-uid').text(uid);
        $('#chpasswd-username').text(username);
        $('#modal-upasswd').modal('show');
    });
    $('#chpasswd-save').click(function () {
        if ($('#chpasswd-passwd').val() == $('#chpasswd-rpasswd').val()) {
            $('#chpasswd-rpasswd').popover('destroy');
            $.getJSON('admin.php', {class: 'useradmin', action: 'passwd', uid: $('#chpasswd-uid').text(), newpass: $('#chpasswd-passwd').val()}, function (data) {
                if (data.errno == 1001) {
                    $('#utable').prepend('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + lang.passwdchanged + '</div>');
                    $('#modal-upasswd').modal('hide');
                }
            });
        } else {
            $('#chpasswd-rpasswd').popover({title: lang.error, content: lang.pwdmism, container: 'body'});
            $('#chpasswd-rpasswd').popover('show');
        }
    });
    $(document).on('click', '.eemail-btn', function () {
        var email = $(this).parent().text();
        var uid = $(this).data('uid');
        var username = $(this).parent().siblings('.td-username').text();
        $('#chemail-uid').text(uid);
        $('#chemail-username').text(username);
        $('#chemail-email').val(email);
        $('#modal-uemail').modal('show');
    });

    $(document).on('click', '.egroups-btn', function () {
        var uid = $(this).data('uid');
        var username = $(this).parent().siblings('.td-username').text();
        $('#chgroups-uid').text(uid);
        $('#chgroups-username').text(username);
        $.getJSON('admin.php', {class: 'useradmin', action: 'lgroups', uid: $(this).data('uid')}, function (data) {
            $.each(data, function (idx, it) {
                $('#chgroups-select').find('option[value="' + it + '"]').attr("selected", true);
            });
        });
        $('#modal-ugroups').modal('show');
    });

    $(document).on('click', '.ename-btn', function () {
        var name = $(this).parent().text();
        var uid = $(this).data('uid');
        var username = $(this).parent().siblings('.td-username').text();
        $('#chname-uid').text(uid);
        $('#chname-username').text(username);
        $('#input-dispname').val(name);
        $('#modal-ucname').modal('show');
    });

    $(document).on('click', '.uremove-btn', function () {
        var uid = $(this).data('uid');
        var username = $(this).parent().siblings('.td-username').text();
        $('#uremove-uid').text(uid);
        $('#uremove-username').text(username);
        $('#modal-uremove').modal('show');
    });
    $(document).on('click', '.vauth-btn', function () {
        var uid0 = $(this).data('uid');
        var username = $(this).parent().siblings('.td-username').text();
        $('#uauth-uid').text(uid0);
        $('#uauth-username').text(username);
        $('#uauth-selectl').find('option[disabled]').attr('disabled', false);
        $('#uauth-selectl').find('option:selected').attr('selected', false);
        $.getJSON('admin.php', {class: 'useradmin', action: 'uauth', uid: uid0}, function (data) {
            if (data.global) {
                $('#uauth-selectl').find('option').attr('disabled', true);
                $('#uauth-selecto').find('option').attr('disabled', true);
                $('#uauth-global-y').attr('checked', true);
                $('#uauth-global-n').attr('checked', false);
            } else {
                $('#uauth-global-n').attr('checked', true);
                $('#uauth-global-y').attr('checked', false);
                $.each(data.location, function (idx, it) {
                    $('#uauth-selectl').find('option[value="' + it + '"]').attr('selected', true);
                    $('#uauth-selecto [data-location="' + it + '"], #uauth-selecto [data-location2="' + it + '"]').attr('disabled', true);
                });
                $.each(data.object, function (idx, it) {
                    $('#uauth-selecto').find('option[value="' + it + '"]').attr('selected', true);
                });
            }
            $('#modal-uauth').modal('show');
        });
    });
    $('#chemail-save').click(function () {
        var uid0 = $('#chemail-uid').text();
        $.getJSON('admin.php', {class: 'useradmin', action: 'chemail', uid: uid0, email: $('#chemail-email').val()}, function (data) {
            if (data.errno == 1001) {
                $('#utable').prepend('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + lang.emailchanged + '</div>');
                $('.eemail-btn[data-uid="' + uid0 + '"]').parent().html($('#chemail-email').val() + '<button class="btn  btn-primary btn-xs pull-right eemail-btn" type="button" data-uid="' + uid0 + '"><i class="fa fa-pencil"></i></button>');
                $('#modal-uemail').modal('hide');
            } else {
                $('#mb-chemail').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $('#chname-save').click(function () {
        $.getJSON('admin.php', {class: 'useradmin', action: 'chname', uid: $('#chname-uid').text(), name: $('#input-dispname').val()}, function (data) {
            if (data.errno == 1001) {
                $('#utable').prepend('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + lang.namechanged + '</div>');
                $('.ename-btn[data-uid="' + $('#chname-uid').text() + '"]').parent().html($('#input-dispname').val() + '<button class="btn  btn-primary btn-xs pull-right eemail-btn" type="button" data-uid="' + $('#chname-uid').text() + '"><i class="fa fa-pencil"></i></button>');
                $('#modal-ucname').modal('hide');
            } else {
                $('#mb-chemail').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $('#uremove-confirm').click(function () {
        $.getJSON('admin.php', {class: 'useradmin', action: 'uremove', uid: $('#uremove-uid').text()}, function (data) {
            $('#modal-uremove').modal('hide');
            if (data.errno == 1001) {
                $('#utable').prepend('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
                $('[data-uid="' + $('#uremove-uid').text() + '"]').parent().remove();
            } else {
                $('#utable').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $('#btn-addu').click(function (e) {
        $('#mb-uadd').find('input select').val('');
        $('#modal-uadd').modal('show');
    });
    $('#uadd-save').click(function (e) {
        $('#mb-uadd').find('input[type="text"], input[type="password"]').each(function () {
            if ($(this).val() == '') {
                $(this).parent().addClass('has-error');
                e.preventDefault();
            } else {
                if ($('#uadd-passwd').val() == $('#uadd-rpasswd').val()) {
                    $('#uadd-rpasswd').popover('destroy');
                    $.getJSON('admin.php', {class: 'useradmin', action: 'uadd', username: $('#uadd-username').val(), pass: $('#uadd-passwd').val(), name: $('#uadd-name').val(), email: $('#uadd-email').val(), groups: $('#uadd-selectg').val()}, function (data) {
                        if (data.errno == 1001) {
                            $('#modal-uadd').modal('hide');
                            $('#uauth-selectl').find('option[disabled]').attr('disabled', false);
                            $('#uauth-selectl').find('option:selected').attr('selected', false);
                            $('#uauth-global-y').attr('checked', false);
                            $('#uauth-global-n').attr('checked', false);
                            $('#uauth-uid').text(data.uid);
                            $('#uauth-username').text($('#uadd-username').val());
                            $('#modal-uauth').modal('show');
                        } else {
                            $('#mb-uadd').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
                        }
                    });
                } else {
                    $('#uadd-rpasswd').popover({title: lang.error, content: lang.pwdmism, container: 'body'});
                    $('#uadd-rpasswd').popover('show');
                }
            }
        });

    });
    $('#uauth-global-y').click(function () {
        $('#uauth-selectl').attr('disabled', true);
        $('#uauth-selecto').attr('disabled', true);
        $('#uauth-selectl').addClass('disabled');
        $('#uauth-selecto').addClass('disabled');
    });
    $('#uauth-global-n').click(function () {
        $('#uauth-selectl').attr('disabled', false);
        $('#uauth-selecto').attr('disabled', false);
        $('#uauth-selectl').removeClass('disabled');
        $('#uauth-selecto').removeClass('disabled');
    });
    $('#chgroups-save').click(function () {
        $.getJSON('admin.php', {class: 'useradmin', action: 'ugroups', uid: $('#chgroups-uid').text(), groups: $('#chgroups-select').val()}, function (data) {
            if (data.errno == 1001) {
                $('#utable').prepend('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + lang.groupchanged + '</div>');
                $('.egroups-btn[data-uid="' + $('#chgroups-uid').text() + '"]').parent().html($('#chgroups-select').val().join(',') + '<button class="btn  btn-primary btn-xs pull-right egroups-btn" type="button" data-uid="' + $('#chgroups-uid').text() + '"><i class="fa fa-pencil"></i></button>');
                $('#modal-ugroups').modal('hide');
            } else {
                $('#mb-chgroups').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $('#uauth-save').click(function () {
        var uid0 = $('#uauth-uid').text();
        $.ajax({
            url: 'admin.php',
            method: "POST",
            data: {class: 'useradmin', action: 'euauth', uid: uid0, global: $('input[name="access-global"][checked]').val(), location: $('#uauth-selectl').val(), objects: $('#uauth-selecto').val()},
            dataType: "json"
        }).done(function (data) {
            if (data.errno == 1001) {
                $('#utable').prepend('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
                $('#modal-uauth').modal('hide');
            } else {
                $('#mb-uauth').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $('#uauth-selectl').change(function(){
        $('#uauth-selecto option[disabled]').attr('disabled',false);
        $('#uauth-selectl').find('option:selected').each(function(){
            var loc = $(this).val();
            if($(this).data('type')=='location2'){
                $('#uauth-selecto').find('[data-location2="'+loc+'"]').attr('disabled',true);
            }else{
                var lc = loc.split('/');
                $('#uauth-selecto').find('[data-location1="'+lc[1].trim()+'"]').attr('disabled',true);
            }
        });
    });
    //End Users Function
    //Begin Group Function
    $('#btn-addg').click(function (e) {
        $('#modal-gadd').modal('show');
    });
    $('.egauth-btn').click(function () {
        var gid0 = $(this).data('gid');
        var gname = $(this).parent().prev().text();
        $('#gauth-gid').text(gid0);
        $('#gauth-gname').text(gname);
        $('#gauth-selectl').find('option[disabled]').attr('disabled', false);
        $('#gauth-selectl').find('option:selected').attr('selected', false);
        $.getJSON('admin.php', {class: 'useradmin', action: 'gauth', gid: gid0}, function (data) {
            if (data.global) {
                $('#gauth-selectl').find('option').attr('disabled', true);
                $('#gauth-selecto').find('option').attr('disabled', true);
                $('#gauth-global-y').attr('checked', true);
                $('#gauth-global-n').attr('checked', false);
            } else {
                $('#gauth-global-n').attr('checked', true);
                $('#gauth-global-y').attr('checked', false);
                $.each(data.location, function (idx, it) {
                    $('#gauth-selectl').find('option[value="' + it + '"]').attr('selected', true);
                    $('#gauth-selecto [data-location="' + it + '"], #gauth-selecto [data-location2="' + it + '"]').attr('disabled', true);
                });
                $.each(data.object, function (idx, it) {
                    $('#gauth-selecto').find('option[value="' + it + '"]').attr('selected', true);
                });
            }
            $('#modal-gauth').modal('show');
        });
    });

    $('#gauth-global-y').click(function () {
        $('#gauth-selectl').attr('disabled', true);
        $('#gauth-selecto').attr('disabled', true);
        $('#gauth-selectl').addClass('disabled');
        $('#gauth-selecto').addClass('disabled');
    });

    $('#gauth-global-n').click(function () {
        $('#gauth-selectl').attr('disabled', false);
        $('#gauth-selecto').attr('disabled', false);
        $('#gauth-selectl').removeClass('disabled');
        $('#gauth-selecto').removeClass('disabled');
    });

    $(document).on('click', '.gremove-btn', function () {
        var gid0 = $(this).data('gid');
        var obj = $(this).parent().parent();
        $.getJSON('admin.php', {class: 'useradmin', action: 'gdel', gid: gid0}, function (data) {
            if (data.errno == 1001) {
                obj.remove();
                $('#groupt-wrap').prepend('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            } else {
                $('#groupt-wrap').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $(document).on('click', '.egname-btn', function (e) {
        var content = $(this).parent().text();
        var gid = $(this).data('gid');
        $(this).parent().html('<input type="text" class="form-control egname-input" value="' + content + '" data-gid="'+gid+'">');
    });
    $(document).on('click', '.egdesc-btn', function (e) {
        var content = $(this).parent().text();
        var gid = $(this).data('gid');
        $(this).parent().html('<input type="text" class="form-control egdesc-input" value="' + content + '" data-gid="'+gid+'">');
    });
    $(document).on('blur', 'input.egname-input', function (e) {
        var gid = $(this).data('gid');
        var nname =$(this).val();
        var obj = $(this).parent();
        $.getJSON('admin.php',{class:'useradmin',action:'ginfo',name:nname,desc:'/',gid:gid},function(data){
            if(data.errno==1001){
                obj.html(nname+'<button class="btn  btn-primary btn-xs pull-right egname-btn" type="button" data-gid="'+gid+'"><i class="fa fa-pencil"></i></button>');
            }else{
                $('#groupt-wrap').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $(document).on('blur', 'input.egdesc-input', function (e) {
        var gid = $(this).data('gid');
        var ndesc =$(this).val();
        var obj = $(this).parent();
        $.getJSON('admin.php',{class:'useradmin',action:'ginfo',name:'/',desc:ndesc,gid:gid},function(data){
            if(data.errno==1001){
                obj.html(ndesc+'<button class="btn  btn-success btn-xs pull-right egname-btn" type="button" data-gid="'+gid+'"><i class="fa fa-pencil"></i></button>');
            }else{
                $('#groupt-wrap').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $('#gadd-save').click(function(e){
        $.ajax({
            url: 'admin.php',
            method: "POST",
            data: {class: 'useradmin', action: 'gadd', gid: $('#gadd-gid').val(),name:$('#gadd-name').val(), desc:$('#gadd-desc').val() ,global: $('input[name="gadd-global"][checked]').val(), location: $('#gadd-selectl').val(), objects: $('#gadd-selecto').val()},
            dataType: "json"
        }).done(function(data){
            if(data.errno==1001){
                $('#modal-gadd').modal('hide');
                $('#tbody-groups').append('<tr><td>'+data.gid+'</td><td class="td-gname">'+$('#gadd-name').val()+'<button class="btn  btn-primary btn-xs pull-right egname-btn" type="button" data-gid="'+data.gid+'"><i class="fa fa-pencil"></i></button></td><td><button class="btn  btn-info btn-xs egauth-btn" type="button" data-gid="'+data.gid+'">'+lang.details+'</button></td><td>'+$('#gadd-desc').val()+'<button class="btn  btn-success btn-xs pull-right egdesc-btn" type="button" data-gid="'+data.id+'"><i class="fa fa-pencil"></i></button></td><td><button type="button" class="btn btn-danger btn-xs gremove-btn" data-uid="'+data.id+'"><i class="fa fa-remove"></i></button></td></tr>');
            }else{
                $('#groupt-wrap').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $('#gauth-save').click(function(){
        $.ajax({
            url: 'admin.php',
            method: "POST",
            data: {class: 'useradmin', action: 'egauth', gid: $('#gauth-gid').text() ,global: $('input[name="gauth-global"][checked]').val(), location: $('#gauth-selectl').val(), objects: $('#gauth-selecto').val()},
            dataType: "json"
        }).done(function(data){
            if(data.errno==1001){
                $('#modal-gadd').modal('hide');
                $('#tbody-groups').append('<tr><td>'+data.gid+'</td><td class="td-gname">'+$('#gadd-name').val()+'<button class="btn  btn-primary btn-xs pull-right egname-btn" type="button" data-gid="'+data.gid+'"><i class="fa fa-pencil"></i></button></td><td><button class="btn  btn-info btn-xs egauth-btn" type="button" data-gid="'+data.gid+'">'+lang.details+'</button></td><td>'+$('#gadd-desc').val()+'<button class="btn  btn-success btn-xs pull-right egdesc-btn" type="button" data-gid="'+data.id+'"><i class="fa fa-pencil"></i></button></td><td><button type="button" class="btn btn-danger btn-xs gremove-btn" data-uid="'+data.id+'"><i class="fa fa-remove"></i></button></td></tr>');
            }else{
                $('#groupt-wrap').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $('#gadd-selectl').change(function(){
        $('#gadd-selecto option[disabled]').attr('disabled',false);
        $('#gadd-selectl').find('option:selected').each(function(){
            var loc = $(this).val();
            if($(this).data('type')=='location2'){
                $('#gadd-selecto').find('[data-location2="'+loc+'"]').attr('disabled',true);
            }else{
                var lc = loc.split('/');
                $('#gadd-selecto').find('[data-location1="'+lc[1].trim()+'"]').attr('disabled',true);
            }
        });
    });
    $('#gauth-selectl').change(function(){
        $('#gauth-selecto option[disabled]').attr('disabled',false);
        $('#gauth-selectl').find('option:selected').each(function(){
            var loc = $(this).val();
            if($(this).data('type')=='location2'){
                $('#gauth-selecto').find('[data-location2="'+loc+'"]').attr('disabled',true);
            }else{
                var lc = loc.split('/');
                $('#gauth-selecto').find('[data-location1="'+lc[1].trim()+'"]').attr('disabled',true);
            }
        });
    });
});

