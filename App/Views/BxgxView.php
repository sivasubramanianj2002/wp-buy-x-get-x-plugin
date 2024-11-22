<?php
defined('ABSPATH')or die();
?>

<h1><?php esc_html_e('Buy One Get One Offer', 'buy-x-get-x'); ?></h1>

<form id="bxgx_product_form" method="POST" action="">
    <div id="bxgx_product_data" class="panel woocommerce_options_panel">
        <div class="options_group">

            <!-- Search bar with label -->
            <label for="bxgx_product_search"><?php esc_html_e('Select the products for Buy One Get One offer', 'buy-x-get-x'); ?></label>
            <div class="form-group">    
                <!-- Container for search input and selected products -->
                <div class="bxgx-product-container">
                    <div id="selected_products" class="selected-products">
                        <?php if (!empty($selected_products)) { ?>
                            <?php foreach ($selected_products as $selected_product_id) { ?>
                                <span class="bxgx-product" data-product-id="<?php echo esc_attr($selected_product_id); ?>">
                                    <?php echo esc_html(get_the_title($selected_product_id)); ?>
                                    <a href="#" class="bxgx-remove-product">x</a>
                                </span>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <input type="text" name = "data" id="bxgx_product_search" class="form-control search"
                           placeholder="<?php esc_attr_e('Search products...', 'buy-x-get-x'); ?>">
                </div>
            </div>

            <!-- Dropdown list for product search -->
            <ul id="bxgx_product_dropdown" class="list-group list">
                <li class="list-group-item"><?php esc_html_e('Start typing to search for products...', 'buy-x-get-x'); ?></li>
            </ul>

            <!-- Submit Button -->
            <button type="submit" class="button button-primary"><?php esc_html_e('Submit', 'buy-x-get-x'); ?></button>
            <div id="toast-container"></div>


        </div>
    </div>
</form>
