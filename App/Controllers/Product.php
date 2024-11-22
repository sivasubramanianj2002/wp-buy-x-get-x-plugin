<?php
namespace BXGX\App\Controllers;
use BXGX\App\Model\ProductDb;

defined('ABSPATH')or die('No access');
class Product{
    /**
     * Add admin menu in the admin dasboard
     * @return void
     */

    public static function addAdminMenu(){
        add_menu_page(
            __("Buy X Get X",'buy-x-get-x'),
            __("Buy X Get X",'buy-x-get-x'),
            "manage_options",
            "buy-x-get-x",
            [self::class, "bxgxView"],
            "dashicons-cart",
                6,
        );
    }
    /** 
     * Loads the view page for adding products as a Buy X Get X discount
     * @return void 
     */

    public static function bxgxView(){
        $selected_products = ProductDb::getSelectedProducts();
        $args=[
            'selected_products' => $selected_products,
        ];
        $template_path='BxgxView.php';
        if(!file_exists(BXGX_PATH.'App/Views/'.$template_path)){
            return;
        }
        wc_get_template($template_path,$args,'',BXGX_PATH.'App/Views/');
    }
    /**
     * Enqueues jquery and css for admin page
     * @return void
     */

    public static function enqueueScripts(){
        if(!isset($_GET['page']) && $_GET['page']!='buy-x-get-x'){
            return;
        }
        wp_enqueue_script('bxgx-script', BXGX_URL.'App/assets/js/script.js',['jquery'],'1.0.4',true);
        wp_enqueue_style('bxgx-style', BXGX_URL.'App/assets/css/style.css',[]);
        wp_localize_script('bxgx-script','bxgxScript',[
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bxgx_nonce'),
        ]);
        }

    /**
     * Handles ajax requests when searching for products in input 
     * @return void
     */
    public static function searchProducts() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'bxgx_nonce')) {
            wp_send_json_error(['message' => 'Nonce verification failed.']);
        }
        if(!isset($_POST['query']) && empty($_POST['query'])){
            return;
        }
        $search_query = sanitize_text_field(wp_unslash($_POST['query']));
        $response = [];
        if (strlen($search_query) >= 2) {
            $products = ProductDb::getProducts();
            foreach ($products as $product_id) {
                $response[] = [
                    'id'   => $product_id,
                    'name' => get_the_title($product_id)
                ];
            }
        }
        wp_send_json($response);
    }
    /**
     * Updates the database with meta key
     * @return void
     */

public static function saveSelectedProducts() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'bxgx_nonce')) {
        wp_send_json_error(['message' => 'Nonce verification failed.']);
    }
    if(!isset($_POST['data'])){
        wp_send_json_error(['message' => 'Missing Product Names']);
        return;
    }
    
    $products_selected = isset($_POST['data']) ? array_map('intval', array_map('sanitize_text_field', $_POST['data'])) : [];
    if (!empty($products_selected)) {
        foreach ($products_selected as $product_id) {
            update_post_meta($product_id, '_bxgx_products', 'yes');
        }
        wp_send_json_success(['message' => 'Products updated successfully']);
    }
        wp_send_json_error(['message' => 'No products selected.']);
    }


    /**
     * Removes meta key from the database
     * @return void
     */

    public static function removeProducts(){
        if(!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'bxgx_nonce')){
            wp_send_json_error(['message' => 'failed to verify  nonce']);
        }

        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        if ($product_id > 0) {
            delete_post_meta($product_id, '_bxgx_products');
            wp_send_json_success(['message' => 'Product meta removed successfully']);
        }
        wp_send_json_error(['message'=> 'failed to delete product']);
    }
    /**
     * Adds the item to the cart
     * @return void
     */

    public static function addToCart($cart_item_key, $product_id, $quantity){
        if(!$product_id||!$quantity || !is_numeric($quantity) || $quantity<=0){
            return;
        }

        $cart = WC()->cart;

        if (get_post_meta($product_id, '_bxgx_products', true) === 'yes') {
            foreach ($cart->get_cart() as $key => $values) {
                if ($values['product_id'] == $product_id && isset($values['is_free'])) {
                    $cart->set_quantity($key, $quantity); 
                    return; 
                }
            }
            $cart->add_to_cart($product_id, $quantity, 0, [], [
                'is_free' => true,
                'main_product_key' => $cart_item_key
            ]);
        }
    }
    
    /**
     * Returns the span class free to free items
     * @return string
     */
    public static function freeCartWidget($price, $cart_item, $cart_item_key) {
        if (isset($cart_item['is_free']) && $cart_item['is_free']) {
            $price = '<span class="free-price">Free</span>';
        }
        return $price;
    }

    /**
     * Sets the Product price for the product
     * @return void
     */

    public static function setProductPrice($cart){
        foreach($cart->get_cart() as $cart_item){
            if(isset($cart_item['is_free'])){
                $cart_item['data']->set_price(0);
            }
        }
    }

    /**
     * Make updates on the quantity of a product
     * @return void
     */
    public static function updateProductQuantity($cart_item_key, $quantity, $old_quantity, $cart) {
        foreach ($cart->get_cart() as $key => $cart_item) {
            if (isset($cart_item['is_free']) && $cart_item['main_product_key'] == $cart_item_key) {
                if ($quantity !== $old_quantity) {
                    WC()->cart->set_quantity($key, $quantity);
                }
            }
        }
    }
    
    /**
     * Removes product which all are free 
     * @return void
     */

    public static function removeFreeProduct($cart_item_key, $cart) {
        $removed_item = WC()->cart->get_cart_item($cart_item_key);
        if (!isset($removed_item['is_free']) || $removed_item['is_free'] === false) {
            $main_product_key = $cart_item_key;  
            foreach ($cart->get_cart() as $key => $cart_item) {     
                if (isset($cart_item['is_free']) && $cart_item['is_free'] === true && $cart_item['main_product_key'] === $main_product_key) {
                    WC()->cart->remove_cart_item($key);
                }
            }
        }
    }
    /**
     * disables the remove icon from the free product
     * @return string
     */

    public static function disableRemoveIcon($remove_link, $cart_item_key) {
        $cart_item = WC()->cart->get_cart_item($cart_item_key);
        if (isset($cart_item['is_free']) && $cart_item['is_free'] == true) {
            return '<span class="disabled-remove-icon"><i class="dashicons dashicons-trash" style="display: none"></i></span>';
        }
        return $remove_link;
    }

    /**
     * Disables the quantity from the free product
     * @return string
     */

    public static function disableQuantity($quantity, $cart_item_key, $cart_item) {
        if (isset($cart_item['is_free']) && $cart_item['is_free']) {
            return '<span class="disabled-remove-icon"><i class="dashicons dashicons-trash" style="display: none"></i></span>';
        }
        return $quantity;
    }
}