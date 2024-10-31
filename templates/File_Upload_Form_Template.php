<?php
if (!defined('ABSPATH')) {
    exit;
}

// Get the WooCommerce dimension unit first
$dimension_unit = get_option('woocommerce_dimension_unit');
?>

<div class="pwp-upload-form" <?php echo ($enabled ? '' : 'style="display: none"');/* @phpstan-ignore-line */ ?>>
    <div class='pwp-upload-parameters' style="display: inline-block">
        <p>
            <?php echo esc_html__('Your full price will be calculated in the cart according to the number of pages of your content PDF.', 'Peleman-Webshop-Package'); ?>
            <?php echo esc_html__('The price per page for this product equals', 'Peleman-Webshop-Package'); ?> <span class="price-per-page"><?php echo wc_price($price_per_page ?: '');/* @phpstan-ignore-line */ ?></span>
        </p>
        <table class="pwp-pdf-table">
            <tbody>
                <tr>
                    <td><?php echo esc_html__('Maximum file size', 'Peleman-Webshop-Package'); ?></td>
                    <td><?php echo esc_html($max_file_size);/* @phpstan-ignore-line */ ?></td>
                </tr>
				<tr>
                    <td><?php echo esc_html__('Size', 'Peleman-Webshop-Package'); ?></td>
                    <td class='param-value' id='content-paper-size'></td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('PDF page width', 'Peleman-Webshop-Package') . ' (' . esc_html($dimension_unit) . ')'; ?></td>
                    <td class="param-value" id="content-width"><?php echo esc_attr($pdf_width ?: '');/* @phpstan-ignore-line */ ?> <span> mm</span></td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('PDF page height', 'Peleman-Webshop-Package') . ' (' . esc_html($dimension_unit) . ')'; ?></td>
                    <td class="param-value" id="content-height"><?php echo esc_attr($pdf_height ?: ''); /* @phpstan-ignore-line */ ?> <span> mm</span></td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('Minimum page count', 'Peleman-Webshop-Package'); ?></td>
                    <td class="param-value" id="content-min-pages"><?php echo esc_attr($pdf_min_pages ?: '');/* @phpstan-ignore-line */ ?></td>
                </tr>
                <tr>
                    <td><?php echo esc_html__("Maximum page count", "Peleman-Webshop-Package"); ?></td>
                    <td class="param-value" id="content-max-pages"><?php echo esc_attr($pdf_max_pages ?: '');/* @phpstan-ignore-line */ ?></td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('Price per page', 'Peleman-Webshop-Package'); ?></td>
                    <td class="param-value price-per-page" id="content-price-per-page" value="<?php echo $price_per_page ?: 0.00; ?>"><?php echo wc_price($price_per_page ?: '', ['decimals' => 3]); ?></td>
                </tr>
            </tbody>
            <span id="pwp-product-price" class="pwp_hidden" value="<?php echo $individual_product_price; /* @phpstan-ignore-line */ ?>"></span>
            <span id="pwp-currency-code" class="pwp_hidden" value="<?php echo $currency_code; /* @phpstan-ignore-line */ ?>"></span>
            <span id="pwp-price-format" class="pwp_hidden" value="<?php echo $currency_pos; /* @phpstan-ignore-line */ ?>"></span>
        </table>
        <div id='pwp-upload-info'>
            <label class='pwp-upload-label' for='pwp-file-upload'>
                <i class=" icon-doc"></i><?php echo esc_html__('Drag or upload your PDF file here', 'Peleman-Webshop-Package'); ?>
                <input class='pwp-upload-field' id='pwp-file-upload' type='file' accept='application/pdf' name='pdf-upload' size='<?php echo esc_html($size); /* @phpstan-ignore-line */ ?>' required />
                <br /><span id="pwp-upload-filename" style="color: green; margin-top: 20px; font-weight: 500; font-size: 16px;"></span>
            </label>
            <div class='pwp-thumbnail-container'>
                <canvas id='pwp-pdf-canvas' width="250" style="display:none"></canvas>
            </div>
            <button id="pwp-file-clear" type="button" style="display:none;"><?php echo esc_html__('Remove PDF', 'Peleman-Webshop-Package'); ?></button>
        </div>
        <div>
            <table id="pwp-pdf-pages-pricing" style="display: none">
                <tbody>
                    <tr>
                        <td><?php echo esc_html__('PDF pages: ', 'Peleman-Webshop-Package'); ?></td>
                        <td id="pwp-pdf-pages"></td>
                    </tr>
                    <tr>
                        <td><?php echo esc_html__('Added cost (excl. VAT): ', 'Peleman-Webshop-Package'); ?></td>
                        <td>
                            <strong id="pwp-pdf-price" class="param-value"><?php echo wc_price(0); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo esc_html__('Estimated total cost: ', 'Peleman-Webshop-Package'); ?></td>
                        <td>
                            <strong id="pwp-pdf-total" class="param-value"><?php echo wc_price(0); ?></strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
