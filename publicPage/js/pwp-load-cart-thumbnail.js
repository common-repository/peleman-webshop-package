(function ($) {
    ('use strict');

    console.log("async thumbnail loader initializing...");
    var _images = $(".pwp-fetch-thumb");

    !_images.each(function (img) {
        console.log($(this).attr('projid'));
        var projectId = $(this).attr('projid');

        var data = {
            id: projectId,
            action: 'Ajax_Load_Cart_Thumbnail',
        }
        $.ajax({
            url: Ajax_Load_Cart_Thumbnail_object.ajax_url,
            method: 'GET',
            data: data,
            cache: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    console.log(response.data);
                    $('img[projid="' + projectId + '"').attr("src", response.data.src).attr("srcset", '');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log({ jqXHR });
                console.error(
                    'Something went wrong:\n' +
                    jqXHR.status +
                    ': ' +
                    jqXHR.statusText +
                    '\nTextstatus: ' +
                    textStatus +
                    '\nError thrown: ' +
                    errorThrown
                );
            },
        });
    });

})(jQuery);