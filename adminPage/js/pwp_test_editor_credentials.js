(function ($) {
    'use strict';

    const _domainField = $('#pie_domain');
    const _customerIdField = $('#pie_customer_id');
    const _apiKeyField = $('#pie_api_key');
    const _testButton = $('#pie_api_test');

    if (_testButton) {
        _testButton.on('click', function () {
            test_api_connection();
        });
    }

    function test_api_connection() {
        var formData = new FormData();

        formData.append('c', _customerIdField.val());
        formData.append('a', _apiKeyField.val());
        let statusCode = -1;
        // console.log(_domainField.val() + "/editor/api/checkcredentials.php");
        // console.log(_apiKeyField.val());
        // console.log(_customerIdField.val());

        $.ajax({
            url: _domainField.val() + "/editor/api/checkcredentials.php",
            method: 'POST',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                _testButton.prop('disabled', true);
            },
            complete: function (response) {
                _testButton.prop('disabled', false);
            },
            success: function (response) {
                console.log(response.result);
                alert(response.result);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus.statusCode);
                console.log(errorThrown);
                alert('CustomerID or API key does not seem to be correct.');
            }

        });
    }

})(jQuery);