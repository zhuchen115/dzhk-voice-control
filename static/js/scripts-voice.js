$(document).ready(function () {
    $('#vc-input-go').click(function () {
        if ($('#vc-input-cmd').val() == '') {
            return;
        } else {
            $.getJSON('fontend.php', {class: 'voice', action: 'msg', msg: $('#vc-input-cmd').val()}, function (data) {
                if (data.errno == 1001) {
                    $('#panel-vcinput').prepend('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
                } else {
                    $('#panel-vcinput').prepend('<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.errmsg + '</div>');
                }
            });
        }
    });
    var recognition = null;
    var final_transcript = '';
    if (!('webkitSpeechRecognition' in window)) {
        $('vc-input-btn').attr('disabled', true);
        $('va-input-btn').attr('title','Not Support');
    } else {
        recognition = new webkitSpeechRecognition();
        recognition.lang = 'en-US';
        recognition.onresult = function (event) {
            var interim_transcript = '';

            for (var i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    final_transcript += event.results[i][0].transcript;
                } else {
                    interim_transcript += event.results[i][0].transcript;
                }
            }
            //final_transcript = capitalize(final_transcript);
            $('#vc-input-cmd').val(final_transcript);
        };
        recognition.onend = function(){
            $('#vc-input-go').click();
        };
    }
    $('#vc-input-btn').click(function(){
        final_transcript = '';
        recognition.start();
    });
});

