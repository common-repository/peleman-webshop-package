/**
 * This script is only responsible to the content file upload.
 * If a product requires a content file, 'variable-product.js' will load an upload form.
 * This script fires on the change event of that form, and performs an AJAX call to
 * the PHP function "upload_content_file" in PublicPage/pwpProductPage.php,
 * where the file is validated and uploaded to the server on success.
 * A response is then return (success or error) after which the "add to cart" button is
 * enabled, or an error message is displayed.
 */

(function ($) {
    'use strict';
    // console.log('initializing pdf upload code...');
    disableAddToCartButton();
    // Event: when the file input changes, ie: when a new file is selected
    $('#file-upload').on('change', e => {
        const productId = $("[name='add-to-cart']").val();
        const variationId = $("[name='variation_id']").val();
        //re-enable this line to automatically disable the upload button
        $('#upload-info').html(''); // clear html content in upload-info
        $('#upload-info').removeClass(); // removes all classes from upload info
        $('#pwp-loading').removeClass('pwp-hidden'); // display loading animation
        $('.thumbnail-container').css('background-image', ''); // remove thumbnail
        $('.thumbnail-container').removeClass('pwp-min-height');
        $('.thumbnail-container').prop('alt', '');

        const formData = constructFormData(productId, variationId);

        // automatically submit form on change event
        $('#file-upload').submit();
        e.preventDefault();

        $.ajax({
            //ajax setup
            url: Upload_PDF_object.ajax_url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            enctype: 'multipart/form-data',
            cache: false,
            dataType: 'json',
            beforeSend: function () {
                console.log('uploading pdf...');
            },
            success: function (response, textStatus, jqXHR) {
                onUploadSuccess(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                onUploadError(jqXHR, textStatus, errorThrown);
            },
            complete: function (jqXHR, textStatus) { },

        });
        $('#file-upload').val('');
    });

    function getDomain() {
        const url = window.location.href;
        return url.substring(
            url.indexOf('//') + 2,
            url.indexOf('.com') + 4
        );
    }

    /**
     * Updates the price for the user after uploading a content file
     *
     * @param {number} price
     */
    function updatePrice(price) {
        $('.woocommerce-Price-amount bdi').text('penguins') = price;

        const pricetext = $(
            'div.woocommerce-variation-price span.woocommerce-Price-amount'
        ).text();

        const currencySymbol = pricetext.replace(/[0-9]./g, '');
        const newPriceText = currencySymbol + price.toFixed(2);

        $(
            'div.woocommerce-variation-price span.woocommerce-Price-amount'
        ).text(newPriceText);
    }

    function onUploadSuccess(response) {
        console.log(response);
        enableAddToCartButton();
        $('#upload-info').html(response.data.message);
        if (response.status === 'success') {
            updatePrice(response.data.file.price_vat_incl);

            // update thumbnail container   
            $('.thumbnail-container').addClass('pwp-min-height');
            $('.thumbnail-container').css('background-image', 'url("' + response.data.file.thumbnail + '")');
            $('.thumbnail-container').prop('alt', response.data.file.name);

            // add content file id to hidden input
            // $("[name='variation_id']").after(
            //     '<input type="hidden" name="content_file_id" class="content_file_id" value="' + response.data.file.content_file_id + '"></input>'
            // );
            $('#pwp-loading').addClass('pwp-hidden');
        } else {
            $('#upload-info').html(response.data.description);
            $('#upload-info').addClass('pwp-response-error');
            $('#pwp-loading').addClass('pwp-hidden');
        }
    }

    function onUploadError(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        $('#upload-info').html('Something went wrong.  Please try again with a different file.');
        $('#upload-info').addClass('response-error');
        $('#pwp-loading').addClass('pwp-hidden');
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

    }

    function constructFormData(productID, variationId) {
        const formData = new FormData();

        formData.append('action', 'Upload_PDF');
        formData.append('file', document.getElementById('file-upload').files[0]);
        formData.append('product_id', productID || 0)
        formData.append('variant_id', variationId || 0);
        formData.append('nonce', Upload_PDF_object.nonce);
        return formData;
    }

    function disableAddToCartButton() {
        $('.single_add_to_cart_button').addClass('pwp-disabled');
        $('.single_add_to_cart_button').prop("disabled", true);
    }

    function enableAddToCartButton() {
        $('.single_add_to_cart_button').removeClass('pwp-disabled');
        $('.single_add_to_cart_button').prop("disabled", false);
    }
})(jQuery);
