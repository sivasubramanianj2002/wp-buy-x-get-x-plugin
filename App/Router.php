<?php
namespace BXGX\App;
use BXGX\App\Controllers\Product;
defined('ABSPATH')or die('No access');
class Router{
    /**
     * Registers hooks for wp and wc 
     */
    public function init(){
          if(is_admin()){
            add_action('admin_menu',[Product::class, 'addAdminMenu']);
          add_action('admin_enqueue_scripts', [Product::class, 'enqueueScripts']); 
          }
          add_action('woocommerce_add_to_cart', [Product::class, 'addToCart'],10,6);
          add_action('woocommerce_before_calculate_totals', [Product::class, 'setProductPrice'], 10,1);
          add_filter('woocommerce_cart_item_price', [Product::class,'freeCartWidget'], 10,3);
          add_action('woocommerce_after_cart_item_quantity_update', [Product::class, 'updateProductQuantity'], 10, 4);
          add_action('woocommerce_cart_item_removed', [Product::class, 'removeFreeProduct'], 10,2);
          add_filter('woocommerce_cart_item_remove_link', [Product::class, 'disableRemoveIcon'], 10, 5);
          add_filter('woocommerce_cart_item_quantity', [Product::class, 'disableQuantity'],10,3);
          if(wp_doing_ajax()){
            add_action('wp_ajax_bxgx_search_products', [Product::class, 'searchProducts']);
            add_action('wp_ajax_bxgx_save_selected_products', [Product::class, 'saveSelectedProducts']);
            add_action('wp_ajax_bxgx_remove_products', [Product::class, 'removeProducts']);
          }
    }

}