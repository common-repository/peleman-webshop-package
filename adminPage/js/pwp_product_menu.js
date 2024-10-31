(function ($) {
    ('use strict');
    $(function () {
        connectFoldouts();
        connectRequirements();
        $(document).on('woocommerce_variations_loaded', function (event) {
            connectFoldouts();
            connectRequirements();
        });
    });

    function elementVisibility(element, isVisible) {
        isVisible ? showElements(element) : hideElements(element);
    }

    function hideElements(element) {
        element.addClass('pwp-hidden');
    }

    function showElements(element) {
        element.removeClass('pwp-hidden');
    }

    function setelementRequired(element, isRequired) {
        element.prop('required', isRequired);
    }

    function connectFoldouts() {

        var selections = $("select[foldout]");
        selections.each(function () {
            var id = '#' + $(this).attr('foldout');
            var target = $(id);
            $(this).change(function () {
                elementVisibility(target, $(this).val() != '');
            });
        });

        var checks = $("input[foldout]");
        checks.each(function () {
            var target = $('#' + $(this).attr('foldout'));
            $(this).change(function () {
                elementVisibility(target, $(this).prop('checked'));
            });
        })
    };

    function connectRequirements() {

        var selections = $("select[requires]");
        selections.each(function () {
            var targets = $('.' + $(this).attr('requires'));
            $(this).change(function () {
                setelementRequired(targets, $(this).val() != '');
            });
        });

        var checks = $("input[requires]");
        checks.each(function () {
            var targets = $('.' + $(this).attr('requires'));
            $(this).change(function () {
                setelementRequired(targets, $(this).prop('checked'));
            });
        });
    }

})(jQuery);