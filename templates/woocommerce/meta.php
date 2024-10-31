<?php

use PWP\includes\editor\Product_Meta_Data;

if (!defined('ABSPATH')) {
    exit;
}

$isB2cSite = get_option('ppi-is-b2b', false);

global $product;
$meta = new Product_Meta_Data($product);
// $isSimpleProduct = $product->is_type('simple');

// if ($isSimpleProduct) {
$showPricesWithVat = get_option('woocommerce_prices_include_tax') === 'yes' ? true : false;
$priceSuffix = $product->get_price_suffix();

$individualPrice = $showPricesWithVat ? wc_get_price_including_tax($product) : wc_get_price_excluding_tax($product);
$individualPriceWithCurrencySymbol = get_woocommerce_currency_symbol() . number_format($individualPrice, 2);

$bundlePrice = $meta->get_unit_price();
$bundleUnits = $meta->get_unit_amount();
$isBundleProduct = $bundleUnits > 1;

$bundleLabel = '';
if ($isBundleProduct) {
    $bundlePriceWithCurrencySymbol =  get_woocommerce_currency_symbol() . number_format($bundlePrice, 2);
    $bundleLabel = sprintf(
        " (%d %s)",
        $bundleUnits,
        __('pieces', 'Peleman-Webshop-Package')
    );
}
// }
?>

<div class="product_meta">
    <?php do_action('woocommerce_product_meta_start'); ?>

    <?php if (!empty($individualPrice)) : ?>
        <span class="sku_wrapper">
            <span class="individual-price <?php echo esc_html(!$isBundleProduct ? 'pwp-hidden' : ''); ?>">
                <span class="label">
                    <?php _e('Individual price', 'Peleman-Webshop-Package') . ': '; ?>
                </span>
                <span class="individual-price-amount woocommerce-Price-amount amount">
                    <?php echo esc_html($isBundleProduct ? $individualPriceWithCurrencySymbol : ''); ?>
                </span>
                <span class="woocommerce-price-suffix">
                    <?php echo $priceSuffix; ?>
                </span>
            </span>
            <span class="add-to-cart-price">
                <span class="label">
                    <?php _e('Price', 'Peleman-Webshop-Package') . ': '; ?>
                </span>
                <span class="bundle-price-amount woocommerce-Price-amount amount">
                    <?php echo esc_html($isBundleProduct ? $bundlePriceWithCurrencySymbol : $individualPriceWithCurrencySymbol); ?>
                </span>
                <span class="woocommerce-price-suffix">
                    <?php echo $priceSuffix; ?>
                    <span class="bundle-suffix <?php echo $isBundleProduct ? 'pwp-hidden' : ''; ?>"> <?php echo $bundleLabel; ?>
                    </span>
                </span>
            </span>
        <?php endif; ?>

        <?php if (get_option("pwp_enable_f2d")) : ?>
            <?php $articleCode = $meta->get_f2d_article_code(); ?>
            <span class="sku_wrapper article-code-container <?php echo empty($articleCode) ? 'pwp-hidden' : ''; ?>">
                <span class="label article-code-label">
                    <?php esc_html_e('Article code:', 'Peleman-Webshop-Package'); ?>
                </span>
                <span class="article-code">
                    <?php echo empty($articleCode) ? '' : esc_html($articleCode); ?>
                </span>
            </span>
        <?php endif; ?>

        <!-- Display SKU if user is admin -->
        <?php if (current_user_can('manage_options')) : ?>
            <?php if (wc_product_sku_enabled() && ($product->get_sku() || $product->is_type('variable'))) : ?>
                <span class="sku_wrapper">
                    <span class="label">
                        <?php esc_html_e('SKU:', 'woocommerce'); ?>
                    </span>
                    <span class="sku">
                        <?php echo esc_html($product->get_sku() ?: __('N/A', 'woocommerce')); ?>
                    </span>
                </span>
            <?php endif; ?>
        <?php endif; ?>
        </span>

        <?php
        echo wc_get_product_category_list(
            $product->get_id(),
            ', ',
            '<span class="posted_in"><span class="label">' . _n('Category:', 'Categories:', count($product->get_category_ids()), 'woocommerce') . '</span> ',
            '</span>'
        );
        ?>

        <?php
        echo wc_get_product_tag_list(
            $product->get_id(),
            ', ',
            '<span class="tagged_as"><span class="label">' . _n('Tag:', 'Tags:', count($product->get_tag_ids()), 'woocommerce') . '</span> ',
            '</span>'
        ); ?>

        <?php do_action('woocommerce_product_meta_end'); ?>

</div>