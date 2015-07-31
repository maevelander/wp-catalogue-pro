<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2><?php _e('WP Catalogue Pro Image Resizing', 'wpc') ?></h2>
    
    <?php
        $wpc_plugin_path = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    ?>
    
    <div style="float: left; width: 100%; margin: 12px 0;">
    <?php
        global $wpdb;

        $wpc_count_qry = $wpdb->get_results("Select ID
                                             From ".$wpdb->posts."
                                             Where post_type = 'wpcproduct'
                                             And post_status = 'publish'");
        $wpc_count_products = count($wpc_count_qry);

        echo "<div class='updated'>
                <p>
                    <em><strong>".__('Notice:','wpc')."</strong></em>
                    ".__('This is an upgrade from an older version of WPC Pro and we have detected','wpc')."
                    { <strong>".$wpc_count_products."</strong> }
                    ".__('products. As of version 1.4.1 we have added a new resizing functionality 
                    and it would require to resize all of your images please proceed to start resizing your images by RESIZE IMAGES button. 
                    Please be aware that this will take some time depending upon the volume of your product images.','wpc')."
                </p>
              </div>";
    ?>
    </div>

    <div class="wpc_image_wrap">
    <?php
        $wpc_resize = $_GET['action'];
        if (isset($wpc_resize)) {
            $wpc_per_limit = 15;

            $wpc_first_sql = $wpdb->get_results("SELECT ID, post_title
                                                 FROM wp_posts
                                                 WHERE post_type = 'wpcproduct'
                                                 And post_status = 'publish'");
            $wpc_prod_total = count($wpc_first_sql);

            $wpc_prod_total = ceil($wpc_prod_total / $wpc_per_limit);

            if (isset($_GET["products"])) {
                    $products = $_GET["products"];
            } else {
                    $products = 1;
            }

            $start_from = ($products - 1) * $wpc_per_limit;

            $wpc_resize_image_sql = $wpdb->get_results("SELECT img_posts.ID, img_posts.post_title, img_meta.*
                                                        FROM wp_posts as img_posts
                                                        Inner Join wp_postmeta As img_meta
                                                        On img_posts.ID = img_meta.post_id
                                                        WHERE img_posts.post_type = 'wpcproduct'
                                                        And img_posts.post_status = 'publish'
                                                        Group By img_posts.ID
                                                        Limit $start_from, $wpc_per_limit");
            foreach ($wpc_resize_image_sql as $wpc_img_product) {
    ?>
                <div class="wpc_image_body">
                    <h3><?php echo $wpc_img_product->post_title; ?></h3>

                    <div class="wpc_images">
                    <?php
                        $wpc_product_images = get_post_meta($wpc_img_product->ID, 'product_images', true);
                        $count_imgs = count($wpc_product_images);
                        //echo $wpc_img_product->ID.' - Count - '.$count_imgs.'<br><br>';
                        foreach ($wpc_product_images as $wpc_img) {
                    ?>
                            <img src="<?php echo $wpc_img['product_img']; ?>" />
                    <?php
                        }

                        $upload_dir = wp_upload_dir();
                        $wpc_image_width = get_option('wpc_image_width');
                        $wpc_image_height = get_option('wpc_image_height');
                        $wpc_thumb_width = get_option('wpc_thumb_width');
                        $wpc_thumb_height = get_option('wpc_thumb_height');

                        $wpc_product_images = get_post_meta($wpc_img_product->ID, 'product_images', true);

                        $big_img_name = array();
                        $thumb_img_name = array();
                        foreach ($wpc_product_images as $wpc_prod_img) {
                            /// For Big
                            $big_resize_img = wp_get_image_editor($wpc_prod_img['product_img']);
                            if (!is_wp_error($big_resize_img)) {
                                $product_big_img = $wpc_prod_img['product_img'];

                                $product_img_explode = explode('/', $product_big_img);
                                $product_img_name = end($product_img_explode);
                                $product_img_name_explode = explode('.', $product_img_name);

                                $product_img_name = $product_img_name_explode[0];
                                $product_img_ext = $product_img_name_explode[1];

                                $big_crop = array('center', 'center');
                                $big_resize_img->resize($wpc_image_width, $wpc_image_height, $big_crop);
                                $big_filename = $big_resize_img->generate_filename('big-' . $wpc_image_width . 'x' . $wpc_image_height, $upload_dir['path'], NULL);
                                $big_resize_img->save($big_filename);

                                $big_img_name[]['wpc_big_img'] = $upload_dir['url'] . '/' . $product_img_name . '-big-' . $wpc_image_width . 'x' . $wpc_image_height . '.' . $product_img_ext;
                            }

                            /// For Thumbs
                            $thumb_resize_img = wp_get_image_editor($wpc_prod_img['product_img']);
                            if (!is_wp_error($thumb_resize_img)) {
                                $product_thumb_img = $wpc_prod_img['product_img'];

                                $product_thumb_img_explode = explode('/', $product_thumb_img);
                                $product_thumb_img_name = end($product_thumb_img_explode);
                                $product_thumb_img_name_explode = explode('.', $product_thumb_img_name);

                                $product_thumb_img_name = $product_thumb_img_name_explode[0];
                                $product_thumb_img_ext = $product_thumb_img_name_explode[1];

                                $thumb_crop = array('center', 'center');
                                $thumb_resize_img->resize($wpc_thumb_width, $wpc_thumb_height, $thumb_crop);

                                $thumb_filename = $thumb_resize_img->generate_filename('thumb-' . $wpc_thumb_width . 'x' . $wpc_thumb_height, $upload_dir['path'], NULL);
                                $thumb_resize_img->save($thumb_filename);

                                $thumb_img_name[]['wpc_thumb_img'] = $upload_dir['url'] . '/' . $product_thumb_img_name . '-thumb-' . $wpc_thumb_width . 'x' . $wpc_thumb_height . '.' . $product_thumb_img_ext;
                            }
                        }
                        update_post_meta($wpc_img_product->ID, 'wpc_big_images', $big_img_name);
                        update_post_meta($wpc_img_product->ID, 'wpc_thumb_images', $thumb_img_name);
                    ?>
                    </div>
                </div>
	<?php
            }
			
            $wpc_next_prod = $products + 1;

            if($products > $wpc_prod_total) {
                echo "<div style='float: left; color: #82d302; font-size: 18px; font-weight: bold; margin: 12px;'>
                        ".__('Congratulations, Your all Product Images Resized Successfully.','wpc')."
                      </div>";
                $wpc_button_style = "style='display: none;'";
        ?>
                <style>
                    .updated {
                        display: none !important;
                    }
                </style>
        <?php
            }
    	?>
            <div class="wpc_img_button" <?php echo $wpc_button_style; ?>>
                <a href="edit.php?post_type=wpcproduct&page=image_resize&action=wpc_resize&products=<?php echo $wpc_next_prod; ?>">
                    <?php _e('Next Batch','wpc'); ?>
                </a>
            </div>
    <?php
        } else {
    ?>
            <div class="wpc_img_button" <?php echo $wpc_button_style; ?>>
                <a href="edit.php?post_type=wpcproduct&page=image_resize&action=wpc_resize&products=1">
                    <?php _e('Resize Images','wpc'); ?>
                </a>
            </div>
    <?php
        }
    ?>
    </div>
</div>