jQuery(document).ready(function($) {
    // console.log(wp);
    var _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;

    $('.media-upload-clear').click(function(e) {
        var button = $(this);
        var id = button.attr('id').replace('_clear', '');
        $("#" + id).val("");
        $("#" + id + "_preview").fadeOut(function() {
            $(this).attr("src", "");
        });
    });
    $('.media-upload').click(function(e) {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = $(this);
        var id = button.data('id');


        _custom_media = true;
        wp.media.editor.send.attachment = function(props, attachment) {
            if (_custom_media) {
                $("#" + id).val(attachment.url);
                $("#" + id + "_preview").attr("src", attachment.url).hide().fadeIn();
                console.log(attachment);
            } else {
                return _orig_send_attachment.apply(this, [props, attachment]);
            };
        }

        wp.media.editor.open(button);
        return false;
    });

    $('.add_media').on('click', function() {
        _custom_media = false;
    });
    $('.invalid').on("hover", function(){
        $(this).removeClass("invalid");
    })
});