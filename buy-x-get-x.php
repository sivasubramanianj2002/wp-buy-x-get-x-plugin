<?php
/**
 * Plugin Name: Buy x Get x
 * Description: This plugin is made for to automate store owners to configure products that to be bought as BUY X and GET X
 * Author: Siva Subramanian
 * Text Domain: buy-x-get-x
 * Version: 1.0
 */
defined('ABSPATH') or die('No Access');
defined('BXGX_PATH') or define('BXGX_PATH',plugin_dir_path(__FILE__));
defined('BXGX_URL') or define('BXGX_URL',plugin_dir_url(__FILE__));
if(!file_exists(BXGX_PATH.'/vendor/autoload.php')) {
    return;
}
require_once BXGX_PATH.'/vendor/autoload.php';
if(!class_exists('\BXGX\App\Router')){
    return;
}
$router= new \BXGX\App\Router();
$router->init();


