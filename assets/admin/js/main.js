var frame;
;(function ($) {
    $(document).ready(function () {

        $(".omb_datepicker").datepicker();

        $("#upload_image").on("click", function () {
            if (frame) {
                frame.open();
                return false;
            }

            frame = wp.media({
                title: "Select Image",
                button: {
                    text: "Insert image"
                },
                multiple: false
            });

            frame.on("select", function () {
                let attachment = frame.state().get("selection").first().toJSON();
                console.log(attachment);
                $("#omb_image_id").val(attachment.id);
                $("#omb_image_url").val(attachment.sizes.thumbnail.url);
                $("#image-container").html(`<img class="media-image" src="${attachment.url}" />`);

            });

            frame.open();
            return false;
        });

    });
})(jQuery);