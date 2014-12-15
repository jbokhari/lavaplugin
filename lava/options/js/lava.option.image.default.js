jQuery(document).ready(function($) {
    console.log(wp);
    var _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;

    $('.media-upload-clear').click( function(e) {
        var $this = $(this);
        var id = $this.data("image-id");
        var container = $this.parent();
        console.log( container );
        console.log( $( ".image-source", container ) );
        console.log( $( ".image-preview", container ) );
        $( ".image-source", container ).val("");
        $( ".image-preview", container ).fadeOut( function() {
            $(this).attr("src", "");
        });
    });
    $('.media-upload').click(function(e) {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var $this = $(this);
        var container = $this.parent();
        var id = container.data("image-id");
        // var id = button.data('image-id');
        console.log(id);


        _custom_media = true;
        wp.media.editor.send.attachment = function(props, attachment) {
            if (_custom_media) {
                $( ".image-source", container ).val(attachment.url);
                $( ".image-preview", container ).attr("src", attachment.url).hide().fadeIn();
            } else {
                return _orig_send_attachment.apply(this, [props, attachment]);
            };
        }

        wp.media.editor.open($this);
        return false;
    });

    $('.add_media').on('click', function() {
        _custom_media = false;
    });
    $('.invalid').on("hover", function(){
        $(this).removeClass("invalid");
    })
});