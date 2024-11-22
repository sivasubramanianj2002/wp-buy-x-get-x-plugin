<?php
namespace BXGX\App\Model;
class ProductDb{
    public static function getProducts(){
        $search_query=isset($_POST['query']) ? sanitize_text_field($_POST['query']) :'';
        $args=[

            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            's' => $search_query,
            'fields' => 'ids',
        ];

        return get_posts($args);
    }

    public static function getSelectedProducts(){
        $args=[
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_key' => '_bxgx_products',
            'fields' => 'ids',
        ];
        
        return get_posts($args);
    }   
}