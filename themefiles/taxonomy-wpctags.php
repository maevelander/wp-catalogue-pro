<?php
    get_header();
    
    if(get_query_var('paged')){
	$paged = get_query_var('paged');
    } elseif ( get_query_var('page') ) {
    	$paged = get_query_var('page');
    } else {
	$paged = 1;	
    }
    
    $args = array(
                'orderby' => 'term_order',
                'order' => 'ASC',
                'hide_empty' => false,
            );
    $termsCatSort = get_terms('wpccategories', $args);
    $count = count($termsCatSort);
    
    $catalogue_page_url	= get_option('catalogue_page_url');
    
    $all_product_label = get_option('wpc_all_product_label');
    $all_product_label = ((!empty($all_product_label)) ? $all_product_label : "All Products");
    
    $val = array(
        'orderby' => 'term_order',
        'show_count' => 0,
        'pad_counts' => 0,
        'hierarchical' => 1,
        'taxonomy' => 'wpccategories',
        'title_li' => '',
    );
?>
    <!--Content-->
    <?php
        echo get_option('wpc_inn_temp_head');
    ?>
    <div style='clear:both'></div>
    <div id="wpc-catalogue-wrapper">
    <?php    
        if(get_option('wpc_show_bc')==yes){
    ?>
            <div class="wp-catalogue-breadcrumb">
                <a href="<?php echo $catalogue_page_url; ?>">
                    <?php echo $all_product_label; ?>
                </a>
                &gt;&gt;
                <?php echo get_queried_object()->slug; ?>
            </div>
    <?php
	}
        
        if(get_option('wpc_sidebar')==yes) {
    ?>
            <div id="wpc-col-1">
		<a class="wpc-visible-phone checking" href="#">Categories</a>
                
                <?php
                    // generating sidebar
                    if($count > 0) {
                ?>
                        <ul class="wpc_all_products">
                            <li class="wpc-category <?php echo $class; ?> wpc_all_product_label">
                                <a href="<?php echo get_option('catalogue_page_url'); ?>">
                                    <?php echo $all_product_label; ?>
                                </a>
                            </li>
                        </ul>
                        <ul class="wpc-categories">
                            <?php echo wp_list_categories($val); ?>
                        </ul>
                <?php
                    } else {
                ?>
                        <ul><li class="wpc-category"><a href="#">No category</a></li></ul>
                <?php
                    }
                    if(get_option('wpc_show_tags') == 'on') {
                ?>
                
                        <div class="wpc_sidebar_tags">
                            <h2>Catalogue Tags</h2>
                        <?php
                            $wpc_tags_args = array(
                                                'smallest'                  => 	12,
                                                'largest'                   => 	30,
                                                'unit'                      => 	'px',
                                                'number'                    => 	18,
                                                'format'                    => 	'flat',
                                                'separator'                 => 	"\n",
                                                'orderby'                   => 	'name',
                                                'order'                     => 	'ASC',
                                                'exclude'                   => 	null,
                                                'include'                   => 	null,
                                                'topic_count_text_callback' => 	default_topic_count_text,
                                                'link'                      => 	'view',
                                                'taxonomy'                  => 	'wpctags',
                                                'echo'                      => 	true
                                            );

                            wp_tag_cloud($wpc_tags_args);

                            wp_reset_query();
                        ?>
                        </div>
                <?php
                    }
                ?>
            </div>
    <?php
	}
        
        $per_page   =   get_option('wpc_pagination');
        if($per_page==0) {
            $per_page = "-1";
        }
         // 
	$term_slug = get_queried_object()->slug;
	if($term_slug) {
            $product_args = array(
                    'post_type'=> 'wpcproduct',
                    'order'     => 'ASC',
                    'orderby'   => 'menu_order',
                    'posts_per_page'	=> $per_page,
                    'paged'	=> $paged,
                    'tax_query' => array(
                            array(
                                'taxonomy' => 'wpctags',
                                'field' => 'slug',
                                'terms' => get_queried_object()->slug
                            )
            ));  // for taxonomy slug

        } // end of all products main condition
	
	$products = new WP_Query($product_args);
	// =============================================
		
	if($products->have_posts()){
            $tcropping = get_option('wpc_tcroping');
            if(get_option('wpc_thumb_height')) {
                $theight = get_option('wpc_thumb_height');
            } else {
		$theight = 142;
            }
            
            if(get_option('wpc_thumb_width')) {
		$twidth = get_option('wpc_thumb_width');
            } else {
		$twidth = 205;
            }
	
            $i = 1;		
			
            if(get_option('wpc_sidebar')==yes) {
                $wpc_prod_container = ' id="wpc-col-2"';
            } else {
                $wpc_prod_container = ' id="wpc-catalogue-wrapper"';
            }
    ?>
            <div<?php echo $wpc_prod_container; ?>>
                <div id="wpc-products">
                <?php
                    while($products->have_posts()):
                        $products->the_post();
                        $product_images = get_post_meta($post->ID, 'product_images', true);

                        $title = get_the_title();
                        $price = get_post_meta(get_the_id(),'product_price',true); 
                ?>
                        <!--wpc product-->
                        <div class="wpc-product">
                            <div class="wpc-img" style="width:<?php echo $twidth; ?>px; height:<?php echo $theight; ?>px; overflow:hidden">
                                <a href="<?php the_permalink(); ?>" class="wpc-product-link">
                <?php
                            foreach($product_images as $field ) {
                ?>
                                <img src="<?php echo $field['product_img']; ?>" alt="" height="<?php echo $theight; ?>"
                            <?php
                                if($tcropping == 'thumb_scale_fit') {
                            ?>
                                     width="<?php echo $twidth; ?>"
                        <?php
                                }
                            }
                        ?>
                                />
                                </a>
                            </div>
                            <p class="wpc-title">
                                <a href="<?php echo the_permalink(); ?>">
                                    <?php echo $title; ?>
                                </a>
                            </p>
                        </div>

                        <!--/wpc-product-->
                <?php
                    if($i == get_option('wpc_grid_rows')) {
                ?>
                        <br clear="all" />
                <?php
                        $i = 0; // reset counter
                    }
                    $i++;
                    endwhile;
                    wp_reset_postdata();
                ?>
                </div>
        <?php
            if(get_option('wpc_pagination') != 0) {
                $wpc_last_page = ceil($products->found_posts/get_option('wpc_pagination'));	
            }
            $wpc_second_last = $wpc_last_page - 1;
                    
            if (get_query_var('page')) {
                $wpc_paged = get_query_var('page');
            } else {
                $wpc_paged = 1;
            }
            $wpc_path = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $wpc_permalink = get_option('permalink_structure');
            $wpc_page_id = get_queried_object_id();
            $wpc_term_slug = get_queried_object()->slug;
                    
            $wpc_adjacents = 2;
            $wpc_previous_page = $wpc_paged - 1;
            $wpc_next_page = $wpc_paged + 1;
                    
            if($wpc_last_page > 1) {
        ?>
                <div class="wpc-paginations">
                <?php
                    if ($wpc_paged > 1) {
                        if(!empty($wpc_permalink)) {
                ?>
                            <a href='?page=<?php echo $wpc_previous_page; ?>' class='wpc_page_link_previous'>previous</a>
                <?php
                        } elseif(strpos($wpc_path, "wpctags")) {
                ?>
                            <a href='?wpctags=<?php echo $wpc_term_slug; ?>&page=<?php echo $wpc_previous_page; ?>' class='wpc_page_link_previous'>previous</a>
                <?php
                        } else {
                ?>
                        <a href='?page_id=<?php echo $wpc_page_id; ?>&page=<?php echo $wpc_previous_page; ?>' class='wpc_page_link_previous'>previous</a>
                <?php
                        }
                    }

                    if ($wpc_last_page < 7 + ($wpc_adjacents * 2)) {	//not enough pages to bother breaking it up
                        for ($wpc_prod_counter = 1; $wpc_prod_counter <= $wpc_last_page; $wpc_prod_counter++) {
                            if ($wpc_prod_counter == $wpc_paged) {
                                echo "<span class='wpc_page_link_disabled'>$wpc_prod_counter</span>";
                            } else {
                                if(!empty($wpc_permalink)) {
                                    echo "<a href='?page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                } elseif(strpos($wpc_path, "wpctags")) {
                                    echo "<a href='?wpctags=$wpc_term_slug&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                } else {
                                    echo "<a href='?page_id=$wpc_page_id&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                }
                            }
                        }
                    } elseif($wpc_last_page > 5 + ($wpc_adjacents * 2)) {	//enough pages to hide some
                        //close to beginning; only hide later pages
                        if($wpc_paged < 1 + ($wpc_adjacents * 2)) {
                            for ($wpc_prod_counter = 1; $wpc_prod_counter < 3 + ($wpc_adjacents * 2); $wpc_prod_counter++) {
                                if ($wpc_prod_counter == $wpc_paged) {
                                    echo "<span class='wpc_page_link_disabled'>$wpc_prod_counter</span>";
                                } else {
                                    if(!empty($wpc_permalink)) {
                                        echo "<a href='?page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } elseif(strpos($wpc_path, "wpctags")) {
                                        echo "<a href='?wpctags=$wpc_term_slug&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } else {
                                        echo "<a href='?page_id=$wpc_page_id&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    }
                                }
                            }
                            echo "<span class='wpc_page_last_dot'>...</span>";
                            
                            if(!empty($wpc_permalink)) {
                                echo "<a href='?page=$wpc_second_last'>$wpc_second_last</a>";
                                echo "<a href='?page=$wpc_last_page'>$wpc_last_page</a>";
                            } elseif(strpos($wpc_path, "wpctags")) {
                                echo "<a href='?wpctags=$wpc_term_slug&page=$wpc_second_last'>$wpc_second_last</a>";
                                echo "<a href='?wpctags=$wpc_term_slug&page=$wpc_last_page'>$wpc_last_page</a>";
                            } else {
                                echo "<a href='?page_id=$wpc_page_id&page=$wpc_second_last'>$wpc_second_last</a>";
                                echo "<a href='?page_id=$wpc_page_id&page=$wpc_last_page'>$wpc_last_page</a>";
                            }
                        } elseif($wpc_last_page - ($wpc_adjacents * 2) > $wpc_paged && $wpc_paged > ($wpc_adjacents * 2)) {
                            //in middle; hide some front and some back
                            if(!empty($wpc_permalink)) {
                                echo "<a href='?page=1'>1</a>";
                                echo "<a href='?page=2'>2</a>";
                            } elseif(strpos($wpc_path, "wpctags")) {
                                echo "<a href='?wpctags=$wpc_term_slug&page=1'>1</a>";
                                echo "<a href='?wpctags=$wpc_term_slug&page=2'>2</a>";
                            } else {
                                echo "<a href='?page_id=$wpc_page_id&page=1'>1</a>";
                                echo "<a href='?page_id=$wpc_page_id&page=2'>2</a>";
                            }
                            echo "<span class='wpc_page_last_dot'>...</span>";
                            for ($wpc_prod_counter = $wpc_paged - $wpc_adjacents; $wpc_prod_counter <= $wpc_paged + $wpc_adjacents; $wpc_prod_counter++) {
                                if ($wpc_prod_counter == $wpc_paged) {
                                    echo "<span class='wpc_page_link_disabled'>$wpc_prod_counter</span>";
                                } else {
                                    if(!empty($wpc_permalink)) {
                                        echo "<a href='?page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } elseif(strpos($wpc_path, "wpctags")) {
                                        echo "<a href='?wpctags=$wpc_term_slug&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } else {
                                        echo "<a href='?page_id=$wpc_page_id&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    }
                                }
                            }
                            echo "<span class='wpc_page_last_dot'>...</span>";
                            
                            if(!empty($wpc_permalink)) {
                                echo "<a href='?page=$wpc_second_last'>$wpc_second_last</a>";
                                echo "<a href='?page=$wpc_last_page'>$wpc_last_page</a>";
                            } elseif(strpos($wpc_path, "wpctags")) {
                                echo "<a href='?wpctags=$wpc_term_slug&page=$wpc_second_last'>$wpc_second_last</a>";
                                echo "<a href='?wpctags=$wpc_term_slug&page=$wpc_last_page'>$wpc_last_page</a>";
                            } else {
                                echo "<a href='?page_id=$wpc_page_id&page=$wpc_second_last'>$wpc_second_last</a>";
                                echo "<a href='?page_id=$wpc_page_id&page=$wpc_last_page'>$wpc_last_page</a>";
                            }
                        } else {
                            //close to end; only hide early pages
                            if(!empty($wpc_permalink)) {
                                echo "<a href='?page=1'>1</a>";
                                echo "<a href='?page=2'>2</a>";
                            } elseif(strpos($wpc_path, "wpctags")) {
                                echo "<a href='?wpctags=$wpc_term_slug&page=1'>1</a>";
                                echo "<a href='?wpctags=$wpc_term_slug&page=2'>2</a>";
                            } else {
                                echo "<a href='?page_id=$wpc_page_id&page=1'>1</a>";
                                echo "<a href='?page_id=$wpc_page_id&page=2'>2</a>";
                            }
                            echo "<span class='wpc_page_last_dot'>...</span>";
                            
                            for ($wpc_prod_counter = $wpc_last_page - (2 + ($wpc_adjacents * 2)); $wpc_prod_counter <= $wpc_last_page; $wpc_prod_counter++) {
                                if ($wpc_prod_counter == $wpc_paged) {
                                    echo "<span class='wpc_page_link_disabled'>$wpc_prod_counter</span>";
                                } else {
                                    if(!empty($wpc_permalink)) {
                                        echo "<a href='?page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } elseif(strpos($wpc_path, "wpctags")) {
                                        echo "<a href='?wpctags=$wpc_term_slug&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } else {
                                        echo "<a href='?page_id=$wpc_page_id&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    }
                                }
                            }
                        }
                    }

                    if ($wpc_paged < $wpc_prod_counter - 1) {
                        if(!empty($wpc_permalink)) {
                            echo "<a href='?page=$wpc_next_page' class='wpc_page_link_next'>next</a>";
                        } elseif(strpos($wpc_path, "wpctags")) {
                            echo "<a href='?wpctags=$wpc_term_slug&page=$wpc_next_page' class='wpc_page_link_next'>next</a>";
                        } else {
                            echo "<a href='?page_id=$wpc_page_id&page=$wpc_next_page' class='wpc_page_link_next'>next</a>";
                        }
                    }
                ?>
                </div>
    <?php
            }
        } else {
            echo 'No Products';
        }
    ?>
        </div>
        <div class="clear"></div>
    </div>
    <?php
        echo get_option('wpc_inn_temp_foot');
    ?>

    <!--/Content-->

<?php
    get_footer();
?>