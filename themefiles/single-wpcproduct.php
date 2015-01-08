<?php get_header(); ?>

<!--Content-->

<?php echo get_option('wpc_inn_temp_head'); ?>



<?php

$catalogue_page_url = get_option('catalogue_page_url');
$terms = get_terms('wpccategories');

global $post;
$terms1 = get_the_terms($post->post_parent, 'wpccategories');

if ($terms1) {
    foreach ($terms1 as $term1) {
        $slug = $term1->slug;
        $tname = $term1->name;
        $pId = $term1->parent;
        $cat_url = get_bloginfo('siteurl') . '/?wpccategories=/' . $slug;
    };
}

$pterm = get_term_by('id', $pId, 'wpccategories');

$prname = $pterm->name;
$prslug = get_bloginfo('siteurl') . '/?wpccategories=/' . $pterm->slug;


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

if (is_single()) {
    $pname = '&gt;&gt;&nbsp;' . get_the_title();
    $show_title = get_the_title();
}
/* ========================= on/off breadcrumbs ======================== */
if (get_option('wpc_show_bc') == yes) {
    if ($prslug == get_bloginfo('siteurl') . '/?wpccategories=/') {
        $has_parent = "&gt;&gt;";
    } else {
        $has_parent = '&gt;&gt; <a href="' . $prslug . '">' . $prname . '</a> &gt;&gt;';
    }

    echo '<div class="wp-catalogue-breadcrumb"> <a href="' . $catalogue_page_url . '">' . $all_product_label . '</a> ' . $has_parent . ' <a href="' . $cat_url . '">' . $tname . '</a> ' . $pname . '</div>';
}
/* ========================= on/off breadcrumbs CLOSING ======================== */
?>

<div id="wpc-catalogue-wrapper">
    <?php
    global $post;
    $terms1 = get_the_terms($post->id, 'wpccategories');

    if ($terms1 != null || $term1 != null) {
        foreach ($terms1 as $term1) {
            $slug = $term1->slug;
            $term_id = $term1->term_id;
        };
    }
    global $wpdb;

    $args = array(
        'orderby' => 'term_order',
        'order' => 'ASC',
        'hide_empty' => true,
    );
    $terms = get_terms('wpccategories', $args);
    $count = count($terms);

    /* ======================= sidebar on/off ====================== */
    if (get_option('wpc_sidebar') == yes) {
        echo '<div id="wpc-col-1">';
            echo '<a class="visible-phone checking" href="#">Categories</a>';
            echo '<ul class="wpc-categories" id="accordion">';

                // generating sidebar
                if ($count > 0) {
                echo '<li class="wpc-category ' . $class . '"><a href="' . get_option('catalogue_page_url') . '">' . $all_product_label . '</a></li>';
            echo '<ul class="wpc-categories" id="accordion">' . wp_list_categories($val) . '</ul>';
        } else {
                echo '<li class="wpc-category"><a href="#">No category</a></li>';
        }

            echo '</ul>';
        echo ' </div>';
    }
    /* ======================= sidebar on/off CLOSING ====================== */
    ?>
    <!--/Left-menu-->
    <!--col-2-->
    <?php
        $i = 1;
        if (get_option('wpc_sidebar') == yes) {
    ?>
        <div id="wpc-col-2">
    <?php
        } else {
    ?>
            <div id="wpc-catalogue-wrapper">
    <?php
        }
			
            if (have_posts()) :
                while (have_posts()) : the_post();
                $product_images = get_post_meta($post->ID, 'product_images', true);
    ?>
                <div id="wpc-product-gallery">
                <?php
                    if (get_option('wpc_image_height')) {
                        $img_height = get_option('wpc_image_height');
                    } else {
                        $img_height = 348;
                    }
                    if (get_option('wpc_image_width')) {
                        $img_width = get_option('wpc_image_width');
                    } else {
                        $img_width = 490;
                    }
                    $iwpc_croping = get_option('wpc_croping');
                ?>
                    <div class="product-img-view" style="width:<?php echo $img_width; ?>px; height:<?php echo $img_height; ?>px;">
                        <div id="slideshow-1">
                            <div id="cycle-1" class="cycle-slideshow"
                                data-cycle-timeout="0"
                                data-cycle-prev="#slideshow-1 .cycle-prev"
                                data-cycle-next="#slideshow-1 .cycle-next"
                                data-cycle-fx="fade"
                            >
                    <?php
                        foreach ($product_images as $field) {
                    ?>
                            <img src="<?php echo $field['product_img']; ?>" alt="" id="img<?php echo $i++; ?>" height="<?php echo $img_height; ?>" <?php if ($iwpc_croping == 'image_scale_fit') {
                                echo 'width="' . $img_width . '"';
                            } ?> />
                    <?php
                        }
                    ?>
                            </div>
                        </div>
                    </div>
                    <?php
                        $c = 1;
                    ?>
                    <div id="slideshow-2" >
                        <div class="wpc-product-img">
                            <div id="cycle-2" class="cycle-slideshow vertical"
                                data-cycle-slides="> span"
                                data-cycle-timeout="0"
                                data-cycle-prev="#slideshow-2 .cycle-prev"
                                data-cycle-next="#slideshow-2 .cycle-next"
                                data-cycle-fx="carousel"
                            <?php if(count($product_images) >= 3):?>
                                data-cycle-carousel-visible="3"
                            <?php else: ?>
                                data-cycle-carousel-visible="2"
                            <?php endif; ?>
                                data-cycle-carousel-vertical=true
                                data-allow-wrap="false"
                            >

                        <?php
                            $count = 0;

                            foreach ($product_images as $field) {
                                $count++;
                                if ($field['product_img']):
                                    if(count($product_images) > 1):
                        ?>
                                        <span class="wpc-thumb-reel">
                                            <img src="<?php echo $field['product_img']; ?>" alt="" id="img<?php echo $c++; ?>" />
                                        </span>
                        <?php
                                    endif;
                                endif;
                            }
                        ?>
                            </div>
                        <?php
                            if ($count > 3) {
                        ?>
                                <a href="#" class="cycle-prev">prev</a>  
                                <a href="#" class="cycle-next">next</a>
                        <?php
                            }
                        ?>
                        </div>
                    </div>

                    <div class="clear"></div>
                </div>
            <?php
                $wpc_product_price = get_post_meta($post->ID, 'wpc_product_price', true);
                if (get_option('wpc_show_title') == yes) {
            ?>
                    <h4>
                    <?php
                        echo $show_title;
                } else {
            ?>
                    <h4>
                        Product Details
            <?php
                }
                if ($wpc_product_price):
            ?>
                    <span class="product-price">Price: <span><?php echo $wpc_product_price; ?></span></span>
            <?php
                endif;
            ?>
                    </h4>
                    <article class="post">
                        <div class="entry-content"> 
                        <?php
                            the_content();
                            wpc_the_meta();
                        
                            if (get_option('wpc_next_prev') == 1) {
                                echo '<p class="wpc-next-prev">';
                                previous_post_link( '%link', 'Previous', TRUE, ' ', 'wpccategories' );
                                next_post_link( '%link', 'Next', TRUE, ' ', 'wpccategories' );
                                echo '</p>';
                            }
                        ?>

                        </div>
                    </article>
    <?php
            endwhile;
        endif;
    ?>
            </div>
            <!--/col-2-->
            <div class="clear"></div>    
        </div>
    <?php
        echo get_option('wpc_inn_temp_foot');
    ?>
    <!--/Content-->
    <script type="text/javascript">
        var screenWidth = window.screen.width;

        switch (screenWidth) {
            case 768:
                $('#cycle-2').attr('data-cycle-carousel-visible', 2);
            break;
            case 480:
                $('#cycle-2').attr('data-cycle-carousel-visible', 1);
            break;
            case 360:
                $('#cycle-2').attr('data-cycle-carousel-visible', 1);
            break;
            case 640:
                $('#cycle-2').attr('data-cycle-carousel-visible', 1);
            break;
            case 320:
                $('#cycle-2').attr('data-cycle-carousel-visible', 1);
            break;
        }
    </script>
<?php
get_footer();