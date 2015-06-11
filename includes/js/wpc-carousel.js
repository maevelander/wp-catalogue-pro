/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($) {
    jQuery.fn.b29_carousel = function (defaults) {

        // Default wpc_settings
        var wpc_settings = jQuery.extend({
            layout: 'hort',
            visible_items: 2,
            current_index: 1,
            item_width: this.find('li').width(),
            reel_length: this.find('li').length,
            item_height: this.find('li').height(),
        }, defaults);

        // Adding Prev/Next links 
        if ((wpc_settings.layout == 'vert' && wpc_settings.reel_length > 3) || (wpc_settings.layout == 'hort' && wpc_settings.reel_length > 2)) {
            this.find('.wpc_carousel').after('<div class="wpc_controls"><span class="prev-up">&lt;</span><span class="next-down">&gt;</span></div>');
        }

        // Hero image selection
        var wpc_hero_img = jQuery('.wpc_carousel ul li:first img').attr('src');
        jQuery('.wpc_hero_img img').attr('src', wpc_hero_img);

        jQuery('.wpc_carousel img').click(function () {
            var img_src = jQuery(this).attr('src');
            jQuery('.wpc_hero_img img').attr('src', img_src);
        });

        // Setting window / reel / controls according to layout
        if (wpc_settings.layout === 'hort') {
            this.find('.wpc_carousel').width(wpc_settings.item_width * wpc_settings.visible_items);
            this.find('ul').width(wpc_settings.reel_length * wpc_settings.item_width);
        } else if (wpc_settings.layout === 'vert') {
            this.find('.wpc_carousel').height(wpc_settings.item_height * wpc_settings.visible_items);
            this.find('ul').height(wpc_settings.reel_length * wpc_settings.item_height);
        }

        // Slide control
        jQuery('.wpc_controls span').click(function () {
            if (jQuery(this).hasClass('next-down')) {
                if (wpc_settings.current_index !== (wpc_settings.reel_length - (wpc_settings.visible_items - 1))) {
                    if (wpc_settings.layout === 'hort') {
                        jQuery(this).parents(this).find('ul').animate({left: '-=' + wpc_settings.item_width + 'px'}, 200);
                    } else if (wpc_settings.layout === 'vert') {
                        jQuery(this).parents(this).find('ul').animate({top: '-=' + wpc_settings.item_height + 'px'}, 200);
                    }
                    wpc_settings.current_index++;
                } else {
                    return false;
                }
            } else {
                if (wpc_settings.current_index !== 1) {
                    if (wpc_settings.layout === 'hort') {
                        jQuery(this).parents(this).find('ul').animate({left: '+=' + wpc_settings.item_width + 'px'}, 200);
                    } else if (wpc_settings.layout === 'vert') {
                        jQuery(this).parents(this).find('ul').animate({top: '+=' + wpc_settings.item_height + 'px'}, 200);
                    }
                    wpc_settings.current_index--;
                } else {
                    return false;
                }
            }
        });

    };
})(jQuery);