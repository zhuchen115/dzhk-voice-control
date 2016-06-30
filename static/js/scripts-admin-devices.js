/* 
 * Copyright (C) 2016 zhc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
$(document).ready(function(){
    var url = 'data/language/' + $('html').attr('lang').replace('-', '_') + '/js/admin.json';
    var lang;
    $.getJSON(url, function (data) {
        lang = data;
    });
    $(document).on('click','.edname-btn',function(){
        var content = $(this).parent().text();
        var devid = $(this).data('devid');
        $(this).parent().html('<input type="text" class="form-control edname-input" value="' + content + '" data-gid="'+devid+'">');
    });
    $(document).on('blur', 'input.edname-input', function (e) {
        var gid = $(this).data('devid');
        var nname =$(this).val();
        var obj = $(this).parent();
        $.getJSON('admin.php',{class:'devadmin',action:'dinfo',devid:gid},function(data){
            if(data.errno==1001){
                obj.html(nname+'<button class="btn  btn-primary btn-xs pull-right edname-btn" type="button" data-gid="'+gid+'"><i class="fa fa-pencil"></i></button>');
            }else{
                $('#devtable').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" ata-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $(document).on('click','.dremove-btn',function(){
         var gid0 = $(this).data('devid');
        var obj = $(this).parent().parent();
        $.getJSON('admin.php', {class: 'devadmin', action: 'devdel', devid: gid0}, function (data) {
            if (data.errno == 1001) {
                obj.remove();
                $('#devtable').prepend('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" ata-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            } else {
                $('#devtable').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" ata-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
    $('#btn-adddev').click(function(){
        $('#modal-adddev').modal('show');
    });
    $('#newdevsave').click(function () {
        $.getJSON('admin.php', {class: 'devadmin', action: 'new', devid: $('#in-dev-id').val(), devname: $('#in-dev-name').val()}, function (data) {
            if (data.errno == 1001) {
                $('#modal-adddev').modal('hide');
                $('#col-devtable').prepend('<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
                $('#tbody-device').append('<tr><td>' + data.id + '</td><td>' + data.hid + '</td><td>' + data.name + '</td><td>'+data.owner+'</td><td><button type="button" class="btn btn-primary btn-sm auth-btn" data-devid="' + data.id + '">' + lang.viewdetails + '</button></td><td><button type="button" class="btn btn-warning btn-sm shift-btn" data-devid="' + data.id + '">' + lang.viewdetails + '</button></td></tr>');
            } else {
                $('#mb-newdev').prepend('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
            }
        });
    });
});

