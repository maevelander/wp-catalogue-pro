<?php
function catalogue($atts,$content = null) {
	ob_start();
	extract( shortcode_atts( array(
					'wpcat' => '',
					'featured' => '',
				), $atts, 'wp-catalogue' ) );
	
	global $post;
	$post_data = get_post($post->ID, ARRAY_A);
	if(get_queried_object()->taxonomy){
		$slug	=	get_queried_object()->taxonomy.'/'.get_queried_object()->slug;
	}else{
		$slug = $post_data['post_name'];
	}
	$crrurl	=	get_site_url().'/'.$slug;
	if(get_query_var('paged')){
		$paged	=	get_query_var('paged');
	}
	elseif ( get_query_var('page') ) {

    	$paged = get_query_var('page');

		} 
	else{
		 $paged	=	1;	
	}
	
	$args = array(
			'orderby' => 'term_order',
			'order' => 'ASC',
			'hide_empty' => false,
);
$termsCatSort	=	get_terms('wpccategories', $args);
	$count	=	count($termsCatSort);
	$post_content	=	get_queried_object()->post_content;
	
		if(strpos($post_content,'[wp-catalogue]')!==false){
		
		
		 $siteurl	=	get_site_url();
		 global $post;
		 $pid	= $post->ID;
		 $guid	=	 $siteurl.'/?page_id='.$pid;
		 if(get_option('catalogue_page_url')){
			update_option( 'catalogue_page_url', $guid );	 
		}else{
			add_option( 'catalogue_page_url', $guid );	
		}
	}
	
	$term_slug	=	get_queried_object()->slug;
	$slug_url = get_site_url().'/?wpccategories=/'.$term_slug;
	$parentname	=	get_queried_object()->name;
	$term_id = get_queried_object()->term_id;
        
	$child_term_id = $term_id;
	$taxonomy_name = 'wpccategories';
	$termchildren = get_term_children( $child_term_id, $taxonomy_name );
	
	$parent_c_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
	$parent_c = get_term($parent_c_term->parent, get_query_var('taxonomy') );
	$parent_name = $parent_c->name;
	$parent_slug = get_site_url().'/?wpccategories=/'.$parent_c->slug;
	
	global $post;
	$terms1 = get_the_terms($post->ID, 'wpccategories');
		
	if($terms1){
		foreach( $terms1 as $term1 ){
			$slug	= $term1->slug;
			$tname	=	$term1->name;
			$cat_url	=	get_site_url().'/?wpccategories=/'.$slug;
		};
	}
	
	if(!$term_slug){
		$class	=	"active-wpc-cat";	
	}
	
	$catalogue_page_url	=	get_option('catalogue_page_url');
	 $terms	=	get_terms('wpccategories');
	 
//list terms in a given taxonomy using wp_list_categories  (also useful as a widget)
$orderby = 'term_order';
$show_count = 0; // 1 for yes, 0 for no
$pad_counts = 0; // 1 for yes, 0 for no
$hierarchical = 1; // 1 for yes, 0 for no

$taxonomy = 'wpccategories';
$title = '';

$val = array(
  'orderby' => $orderby,
  'show_count' => $show_count,
  'pad_counts' => $pad_counts,
  'hierarchical' => $hierarchical,
  'taxonomy' => $taxonomy,
  'title_li' => $title,
);
	
$all_product_label = get_option('wpc_all_product_label');

$all_product_label = ((!empty($all_product_label)) ? $all_product_label : "All Products");
	
?>

<?php
		if(is_single()){
			$pname	=	'>> '.get_the_title();	
		}
		
		//===============================  NEW try  =================================
		if($featured == true){  //(iii)
						 
						$argsf = array(
						'post_type'=> 'wpcproduct',
						'order'     => 'ASC',
						'orderby'   => 'menu_order',
						'meta_value' =>'is_featured',
						'posts_per_page' => 999,
						);
					}
						
				
			$featuredproducts = new WP_Query($argsf);
		
		if($featuredproducts->have_posts()){
			$tcropping	=	get_option('wpc_tcroping');
			if(get_option('wpc_thumb_height')){
			$theight	=	get_option('wpc_thumb_height');
			}else{
				$theight	=	142;
			}
			if(get_option('wpc_thumb_width')){
				$twidth		=	get_option('wpc_thumb_width');
			}else{
				$twidth		=	205;
			}
			$i = 1;		
			
			//Sidebar with featured products enabled
                        echo "<div style='clear:both'></div>";
		echo '<div id="wpc-catalogue-wrapper">';
                if(!$termchildren){
				if($parent_slug == get_site_url().'/?wpccategories=/'){
					$has_child = "&gt;&gt;";
				}
				else{
					$has_child = '&gt;&gt; <a href="'.$parent_slug.'">'.$parent_name.'</a> &gt;&gt;';
				}
			}
			else{
				$has_child = '';
			}
                        
                        if(get_option('wpc_show_bc')==yes){
			if($termchildren){
				echo '<div class="wp-catalogue-breadcrumb"> <a href="'.$catalogue_page_url.'">'.$all_product_label.'</a> &gt;&gt; <a href="'.$slug_url.'">'.$parentname.'</a>' . $pname . '</div>';
			}
			else{
				echo '<div class="wp-catalogue-breadcrumb"> <a href="'.$catalogue_page_url.'">'.$all_product_label.'</a> '.$has_child.' <a href="'.$slug_url.'">'.$parentname.'</a>' . $pname . '</div>';
			}
		}
                        if(get_option('wpc_sidebar')==yes) {
		echo '<div id="wpc-col-1">';
		echo '<a class="wpc-visible-phone checking" href="#">Categories</a>';
        
		
		// generating sidebar
		if($count>0){
                    echo '<ul class="wpc_all_products">';
			echo '<li class="wpc-category ' . $class . ' wpc_all_product_label"><a href="'. get_option('catalogue_page_url') .'">'.$all_product_label.'</a></li></ul>';	
       		
				
			echo  '<ul class="wpc-categories">'.wp_list_categories($val).'</ul>'; 	
			
		}else{
			echo  '<ul><li class="wpc-category"><a href="#">No category</a></li></ul>';	
		}
        echo ' </div>';
		}
			echo '  <!--col-2-->
			
						<div id="wpc-col-2">
						<div id="wpc-products">';
				while($featuredproducts->have_posts()): $featuredproducts->the_post();
					$product_images = get_post_meta($post->ID, 'product_images', true);
				
				$title		=	get_the_title(); 
				$permalink	=	get_permalink(); 
				$price		=	get_post_meta(get_the_id(),'product_price',true);
				
				
				 echo '<!--wpc product-->';
				
				 echo '<div class="wpc-product">';
				 echo '<div class="wpc-img" style="width:' . $twidth . 'px; height:' . $theight . 'px; overflow:hidden"><a href="'. $permalink .'" class="wpc-product-link">';
				 foreach($product_images as $field ){
				 echo '<img src="'.$field['product_img'].'" alt="" height="' . $theight . '" ';
				 }
				 if($tcropping == 'thumb_scale_fit'){
					  echo  '" width="' .$twidth. '"'; }
				 echo '" />';
		echo '</a></div>';
				 echo '<p class="wpc-title"><a href="'. $permalink .'">' . $title . '</a></p>';
				 echo '</div>';
				 echo '<!--/wpc-product-->';
				endwhile; 
				echo '</div><div class="clear" ></div></div> </div>';	
		
}
		
		else {
                    
			if(!$termchildren){
				if($parent_slug == get_site_url().'/?wpccategories=/'){
					$has_child = "&gt;&gt;";
				}
				else{
					$has_child = '&gt;&gt; <a href="'.$parent_slug.'">'.$parent_name.'</a> &gt;&gt;';
				}
			}
			else{
				$has_child = '';
			}
		
		// =================================================================
		echo "<div style='clear:both'></div>";
		echo '<div id="wpc-catalogue-wrapper">';
		/* ============================== PRo features on/off breadcrumbs and sidebar ====================================*/
		if(get_option('wpc_show_bc')==yes){
			if($termchildren){
				echo '<div class="wp-catalogue-breadcrumb"> <a href="'.$catalogue_page_url.'">'.$all_product_label.'</a> &gt;&gt; <a href="'.$slug_url.'">'.$parentname.'</a>' . $pname . '</div>';
			}
			else{
				echo '<div class="wp-catalogue-breadcrumb"> <a href="'.$catalogue_page_url.'">'.$all_product_label.'</a> '.$has_child.' <a href="'.$slug_url.'">'.$parentname.'</a>' . $pname . '</div>';
			}
		}
		if(get_option('wpc_sidebar')==yes) {
		echo '<div id="wpc-col-1">';
		echo '<a class="wpc-visible-phone checking" href="#">Categories</a>';
        echo '<ul class="wpc-categories">';
		
		// generating sidebar
		if($count>0){
			echo '<li class="wpc-category ' . $class . ' wpc_all_product_label"><a href="'. get_option('catalogue_page_url') .'">'.$all_product_label.'</a></li>';	
       		
				
			echo  '<ul class="wpc-categories">'.wp_list_categories($val).'</ul>'; 	
			
		}else{
			echo  '<li class="wpc-category"><a href="#">No category</a></li>';	
		}
		
		echo '</ul>';
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
        echo ' </div>';
		}
		
		// ending sidebar
		/* ============================== PRo features on/off breadcrumbs and sidebar CLOSING ==================================== */
		// products area
		$per_page	=	get_option('wpc_pagination');
		if($per_page==0){
			$per_page	=	"-1";
		}
		
		// 
		$term_slug	=	get_queried_object()->slug;
		if($term_slug){
	
				$args = array(
					'post_type'=> 'wpcproduct',
					'order'     => 'ASC',
					'orderby'   => 'menu_order',
					'posts_per_page'	=> $per_page,
					'paged'	=> $paged,
					//'meta_value' =>'not_featured',
					'tax_query' => array(
						array(
							'taxonomy' => 'wpccategories',
							'field' => 'slug',
							'terms' => get_queried_object()->slug
						)
				));  // for taxonomy slug
		
			}else{
							extract( shortcode_atts( array(
					'wpcat' => '',
					'featured' => '',
				), $atts, 'wp-catalogue' ) );
				
				if($wpcat != '' && $featured !=true){ //(i)
							$args = array(
							'post_type'=> 'wpcproduct',
							'order'     => 'ASC',
							'orderby'   => 'menu_order',
							//'meta_value' =>'not_featured',
							'posts_per_page'	=> $per_page,
							'paged'	=> $paged,
				
							'tax_query' => array(
								array(
									'taxonomy' => 'wpccategories', // for shortcode attribute adition
									'field'=>'slug',
									'terms'=> $wpcat,
									)
									)
							);
					
				}
				else if($wpcat == '' && $featured != true) //(ii)
					{
						$args = array(
						'post_type'=> 'wpcproduct',
						'order'     => 'ASC',
						'orderby'   => 'menu_order',
						//'meta_value' =>'not_featured',
						'posts_per_page'	=> $per_page,
						'paged'	=> $paged,
						);
					}
					
			
				
				}  // end of all products main condition
				
		// products listing
		
		$products	=	new WP_Query($args);
		
		// =============================================
		
		if($products->have_posts()){
			$tcropping	=	get_option('wpc_tcroping');
			if(get_option('wpc_thumb_height')){
			$theight	=	get_option('wpc_thumb_height');
			}else{
				$theight	=	142;
			}
			if(get_option('wpc_thumb_width')){
				$twidth		=	get_option('wpc_thumb_width');
			}else{
				$twidth		=	205;
			}
			$i = 1;		
			
			if(get_option('wpc_sidebar')==yes) {
			echo '  <!--col-2-->
			
						<div id="wpc-col-2">
						<div id="wpc-products">';
		} else {
			echo '  <!--col-2-->
			
						<div id="wpc-catalogue-wrapper">
						<div id="wpc-products">';
			}
                        while($products->have_posts()): $products->the_post();
                        $product_images = get_post_meta($post->ID, 'product_images', true);
                        /*echo "<pre>";
                            print_r($product_images);
                        echo "</pre>";*/

                        $title		=	get_the_title(); 
                        $permalink	=	get_permalink(); 
                        $price		=	get_post_meta(get_the_id(),'product_price',true); 

                         echo '<!--wpc product-->';
                         echo '<div class="wpc-product">';
                         echo '<div class="wpc-img" style="width:' . $twidth . 'px; height:' . $theight . 'px; overflow:hidden"><a href="'. $permalink .'" class="wpc-product-link">';
                         foreach($product_images as $field ){
                         echo '<img src="'.$field['product_img'].'" alt="" height="' . $theight . '" ';
                         
                         if($tcropping == 'thumb_scale_fit'){
                                  echo  '" width="' .$twidth. '"'; }
                         echo '" />';
                         }
                         echo '</a></div>';
                         echo '<p class="wpc-title"><a href="'. $permalink .'">' . $title . '</a></p>';
                         echo '</div>';
                         echo '<!--/wpc-product-->';

                        if($i == get_option('wpc_grid_rows'))
                {
                        echo '<br clear="all" />';
                        $i = 0; // reset counter
                }
                        $i++;
                        endwhile; wp_reset_postdata();
                        echo '</div>';
                    if(get_option('wpc_pagination')!=0){
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
                    
                    if($wpc_last_page > 1){
                    echo '<div class="wpc-paginations">';
                        if ($wpc_paged > 1) {
                            if(!empty($wpc_permalink)) {
                                echo "<a href='?page=$wpc_previous_page' class='wpc_page_link_previous'>previous</a>";
                            } elseif(strpos($wpc_path, "wpccategories")) {
                                echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_previous_page' class='wpc_page_link_previous'>previous</a>";
                            } else {
                                echo "<a href='?page_id=$wpc_page_id&page=$wpc_previous_page' class='wpc_page_link_previous'>previous</a>";
                            }
                        }
                
                        if ($wpc_last_page < 7 + ($wpc_adjacents * 2)) {	//not enough pages to bother breaking it up
                            for ($wpc_prod_counter = 1; $wpc_prod_counter <= $wpc_last_page; $wpc_prod_counter++) {
				if ($wpc_prod_counter == $wpc_paged) {
                                    echo "<span class='wpc_page_link_disabled'>$wpc_prod_counter</span>";
                                } else {
                                    if(!empty($wpc_permalink)) {
                                        echo "<a href='?page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } elseif(strpos($wpc_path, "wpccategories")) {
                                        echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
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
                                        } elseif(strpos($wpc_path, "wpccategories")) {
                                            echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                        } else {
                                            echo "<a href='?page_id=$wpc_page_id&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                        }
                                    }
                                }
                                echo "<span class='wpc_page_last_dot'>...</span>";
                                if(!empty($wpc_permalink)) {
                                    echo "<a href='?page=$wpc_second_last'>$wpc_second_last</a>";
                                    echo "<a href='?page=$wpc_last_page'>$wpc_last_page</a>";
                                } elseif(strpos($wpc_path, "wpccategories")) {
                                    echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_second_last'>$wpc_second_last</a>";
                                    echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_last_page'>$wpc_last_page</a>";
                                } else {
                                    echo "<a href='?page_id=$wpc_page_id&page=$wpc_second_last'>$wpc_second_last</a>";
                                    echo "<a href='?page_id=$wpc_page_id&page=$wpc_last_page'>$wpc_last_page</a>";
                                }
                            } elseif($wpc_last_page - ($wpc_adjacents * 2) > $wpc_paged && $wpc_paged > ($wpc_adjacents * 2)) {
                                //in middle; hide some front and some back
                                if(!empty($wpc_permalink)) {
                                    echo "<a href='?page=1'>1</a>";
                                    echo "<a href='?page=2'>2</a>";
                                } elseif(strpos($wpc_path, "wpccategories")) {
                                    echo "<a href='?wpccategories=$wpc_term_slug&page=1'>1</a>";
                                    echo "<a href='?wpccategories=$wpc_term_slug&page=2'>2</a>";
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
                                        } elseif(strpos($wpc_path, "wpccategories")) {
                                            echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                        } else {
                                            echo "<a href='?page_id=$wpc_page_id&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                        }
                                    }
				}
				echo "<span class='wpc_page_last_dot'>...</span>";
                                if(!empty($wpc_permalink)) {
                                    echo "<a href='?page=$wpc_second_last'>$wpc_second_last</a>";
                                    echo "<a href='?page=$wpc_last_page'>$wpc_last_page</a>";
                                } elseif(strpos($wpc_path, "wpccategories")) {
                                    echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_second_last'>$wpc_second_last</a>";
                                    echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_last_page'>$wpc_last_page</a>";
                                } else {
                                    echo "<a href='?page_id=$wpc_page_id&page=$wpc_second_last'>$wpc_second_last</a>";
                                    echo "<a href='?page_id=$wpc_page_id&page=$wpc_last_page'>$wpc_last_page</a>";
                                }
                            } else {
                                //close to end; only hide early pages
                                if(!empty($wpc_permalink)) {
                                    echo "<a href='?page=1'>1</a>";
                                    echo "<a href='?page=2'>2</a>";
                                } elseif(strpos($wpc_path, "wpccategories")) {
                                    echo "<a href='?wpccategories=$wpc_term_slug&page=1'>1</a>";
                                    echo "<a href='?wpccategories=$wpc_term_slug&page=2'>2</a>";
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
                                        } elseif(strpos($wpc_path, "wpccategories")) {
                                            echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
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
                            } elseif(strpos($wpc_path, "wpccategories")) {
                                echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_next_page' class='wpc_page_link_next'>next</a>";
                            } else {
                                echo "<a href='?page_id=$wpc_page_id&page=$wpc_next_page' class='wpc_page_link_next'>next</a>";
                            }
                        }
                        echo '</div>';
                    }
		}else{
                    echo 'No Products';
		}
		
		echo '</div><div class="clear"></div></div>';}
		
		return ob_get_clean();
		
		//return $return_string;
	
}
add_shortcode('wp-catalogue','catalogue');