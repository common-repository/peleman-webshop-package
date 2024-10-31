/**
 * This script is responsible for redirecting customers to the Imaxel editor,
 * if the product has a template attached, in the admin backend.
 * It fires on the click event, and passes the variantId and,
 * if present, the content file Id.  It call   the PHP function
 * "pwp_add_to_cart" in PublicPage/pwpProductPage.php,
 * which makes a request to Imaxel to create a project.
 * It then persists this as a customer project and redirects the user to the editor.
 *
 * In the event of an error, it's displayed.
 * Errors can be caused by:
 * -an outage of the Imaxel servers (rare)
 * -a non-existing template defined in the backend
 */

(function ($) {
	("use strict");
	console.log("updated add to cart js initializing...");
	const { __, _x, _n, _nx } = wp.i18n;
	$(document).on("click", ".single_add_to_cart_button", function (e) {
		e.preventDefault();

		// Check min value
		let minQuantity = parseInt($("input[name='quantity']").attr('min'));
// 		console.log("Min Quantity: ", minQuantity); // Print Min Quantity value to console

		let inputQuantity = parseInt($("input[name='quantity']").val());

		// If min is different from 1
		if (minQuantity !== 1 && inputQuantity < minQuantity) {
			alert('The minimum quantity for this product must be at least ' + minQuantity + '.'); 
			return;
		}

		let $thisButton = $(this);
		let $form = $("form.cart");

		let id = $thisButton.val();
		let file = $("#pwp-file-upload")[0].files[0];
		let product_id = $form.find("input[name=product_id]").val() || id;
		let quantity = $form.find("input[name=quantity]").val() || 1;
		let variation_id = $form.find("input[name=variation_id]").val() || 0;
		let loadSpinner = $(this).find(".pwp-loader");
		let buttonText = $thisButton.find(".btn-text").text();
		let projectName = $form.find("input[id=project_reference]").val() || "";
		let formData = new FormData();

		formData.append("action", "Ajax_Add_To_Cart");
		formData.append("product_id", product_id);
		formData.append("product_sku", "");
		formData.append("quantity", quantity);
		formData.append("variation_id", variation_id);
		formData.append("pdf-upload", file);
		formData.append("project_reference", projectName);
		formData.set("nonce", Ajax_Add_To_Cart_object.nonce);

		//console.log(JSON.stringify(formData));

		// $(document.body).trigger('adding_to_cart', [$thisButton, formData]);

		$.ajax({
			url: Ajax_Add_To_Cart_object.ajax_url,
			method: "POST",
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			// async: true,
			beforeSend: function () {
				$thisButton.removeClass("pwp-added");
				$thisButton.addClass("pwp-loading");
				if (file) {
					$thisButton.find(".btn-text").text("Uploading file, please wait");
				}
				loadSpinner.show();
				$thisButton.attr("disabled", true);
				showElement($("#pwp-loading"));
			},
			complete: function (response) {
				$thisButton.addClass("pwp-added");
				$thisButton.removeClass("pwp-loading");
				$thisButton.attr("disabled", false);
				$thisButton.find(".btn-text").text(buttonText);
				loadSpinner.hide();
				hideElement($("#pwp-loading"));
				//console.log(response);
			},
			success: function (response) {
				//console.log(response);
				data = response.data;
				response.success ? onSuccess(data, $thisButton) : onFailure(data);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				logAjaxError(jqXHR, textStatus, errorThrown);
			},
		});
	});
	return false;

	function onFailure(data) {
		//console.log(data);
		alert(data.message);
		$("#redirection-info").html(data.message);

		$("#redirection-info").addClass("ppi-response-error");
	}

	function onSuccess(data, button) {
		if (data.destination_url) {
			window.location.href = data.destination_url;
			return;
		}

		$(document.body).trigger("added_to_cart", [
			data.fragments,
			data.cart_hash,
			button,
		]);
	}

	function logAjaxError(jqXHR, textStatus, errorThrown) {
		//console.log(jqXHR.responseText);
		let response = JSON.parse(jqXHR.responseText);
		alert(response.data.message);
		//console.log(
		// 	"Something went wrong:\n" +
		// 		jqXHR.status +
		// 		": " +
		// 		jqXHR.statusText +
		// 		"\nTextstatus: " +
		// 		textStatus +
		// 		"\nError thrown: " +
		// 		errorThrown
		// );
	}

	function hideElement(element) {
		element.addClass("pwp-hidden");
	}

	function showElement(element) {
		element.removeClass("pwp-hidden");
	}
})(jQuery);