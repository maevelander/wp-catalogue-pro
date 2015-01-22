<div class="wrap">
  <div id="icon-options-general" class="icon32"><br>
  </div>
  <h2>Wp Catalogue<?php _e(' Settings','wpc') ?></h2>
  <div class="wpc-left-liquid">
    <?php if ( isset( $_GET['settings-updated'] ) ) {
    echo "<div class='updated' style='margin-top:10px;'><p> WP Catalogue ". __('Settings updated successfully','wpc')."</p></div>";
} ?>
    <div class="wpc-left">
      <div class="wpc-headings">
        <h3><?php _e('Settings','wpc') ?></h3>
      </div>
      <div class="wpc-inner">
        <p class="description"><?php _e('Adjust the basic presentation of your product catalogue here. It is important to set this up before you start uploading products so the plugin knows what size to generate thumbnails and product images','wpc') ?> </p>
        <p class="description"><?php _e('You can further customise the design of your product catalogue in your theme css.','wpc') ?> </p>
        <form method="post" action="options.php">
          <?php settings_fields( 'baw-settings-group' ); ?>
          <table class="form-table" id="catalogue-settings-tabls">
            <tbody>
              <tr>
                <th scope="row"><label for="wpc_grid_rows"><?php _e('Grid Rows','wpc') ?></label></th>
                <td><select id="wpc_grid_rows" name="wpc_grid_rows">
                    <option value="2" <?php if(get_option('wpc_grid_rows')==2){echo 'selected="selected"';} ?> >2</option>
                    <option value="3" <?php if(get_option('wpc_grid_rows')==3){echo 'selected="selected"';} ?>>3</option>
                    <option value="4" <?php if(get_option('wpc_grid_rows')==4){echo 'selected="selected"';} ?>>4</option>
                    <option value="5" <?php if(get_option('wpc_grid_rows')==5){echo 'selected="selected"';} ?>>5</option>
                    <option value="6" <?php if(get_option('wpc_grid_rows')==6){echo 'selected="selected"';} ?>>6</option>
                    <option value="7" <?php if(get_option('wpc_grid_rows')==7){echo 'selected="selected"';} ?>>7</option>
                    <option value="8" <?php if(get_option('wpc_grid_rows')==8){echo 'selected="selected"';} ?>>8</option>
                    <option value="9" <?php if(get_option('wpc_grid_rows')==9){echo 'selected="selected"';} ?>>9</option>
                    <option value="10" <?php if(get_option('wpc_grid_rows')==10){echo 'selected="selected"';} ?>>10</option>
                  </select>
                  <span><?php _e('products per row','wpc') ?></span></td>
              </tr>
              <!--Color choose - New feature-->
              
              <tr valign="top">
                <th scope="row"><label for="colorPicker"><?php _e('Choose Theme Color','wpc') ?></label></th>
                <td><input type="text" value="<?php if(get_option('templateColorforProducts')){ echo get_option('templateColorforProducts'); }else {echo "#000000";} ?>" class="templateColorforProducts" name="templateColorforProducts" id="templateColorforProducts" /></td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="pagination"><?php _e('Pagination','wpc') ?> </label></th>
                <td><input name="wpc_pagination" type="text" id="wpc_pagination" value="<?php if(get_option('wpc_pagination') or get_option('wpc_pagination')==0){ echo get_option('wpc_pagination'); }else {echo 20;} ?>" size="15">
                  <span><?php _e('products per page (use 0 for unlimited)','wpc') ?></span></td>
              </tr>
              <!--WPC pro cersion 1.0's features-->
               <tr valign="top">
                <th scope="row"><label for="breadcrumbs">
                    <?php _e('Breadcrumbs','wpc') ?>
                  </label></th>
                <td><label><?php _e('ON','wpc'); ?>
                    <input type="radio" name="wpc_show_bc" value="yes" <?php if(get_option('wpc_show_bc')==yes){echo 'checked="checked"';} ?>  />
                  </label>
                  <label> <?php _e('OFF','wpc'); ?>
                    <input type="radio" name="wpc_show_bc" value="no" <?php if(get_option('wpc_show_bc')==no){echo 'checked="checked"';} ?> />
                  </label></td>
              </tr>
			  <tr valign="top">
                <th scope="row"><label for="accordion">
                    <?php _e('Accordion Sidebar','wpc') ?>
                  </label></th>
                <td><label><?php _e('ON','wpc'); ?>
                    <input type="radio" name="wpc_accordion_setting" value="yes" <?php if(get_option('wpc_accordion_setting')==yes){echo 'checked="checked"';} ?>  />
                  </label>
                  <label> <?php _e('OFF','wpc'); ?>
                    <input type="radio" name="wpc_accordion_setting" value="no" <?php if(get_option('wpc_accordion_setting')==no){echo 'checked="checked"';} ?> />
                  </label></td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="all products categories label">
                    <?php _e('Main Category Heading','wpc') ?>
                  </label></th>
                <td>
                    <input type="text" name="wpc_all_product_label" value="<?php echo get_option('wpc_all_product_label'); ?>"  style="width:100%"  />
                    <p style="font-size:12px; font-weight:bold;"><?php _e('Note:','wpc'); ?> <span style="font-style:italic;"><?php _e('You can change main category list heading. Default is "All Products"', 'wpc'); ?></span></p>
                  </td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="title">
                <?php _e('Show Product Titles','wpc') ?>
                  </label></th>
                <td><label><?php _e('ON','wpc'); ?>
                    <input type="radio" name="wpc_show_title" value="yes" <?php if(get_option('wpc_show_title')==yes){echo 'checked="checked"';} ?>  />
                  </label>
                  <label> <?php _e('OFF','wpc'); ?>
                    <input type="radio" name="wpc_show_title" value="no" <?php if(get_option('wpc_show_title')!=yes){echo 'checked="checked"';} ?> />
                  </label></td>
              </tr>
              <tr valign="top">
           <th scope="row"><label for="sidebar">
                    <?php _e('Sidebar','wpc') ?>
                  </label></th>
                <td><label><?php _e('ON','wpc'); ?>
                    <input type="radio" name="wpc_sidebar" value="yes" <?php if(get_option('wpc_sidebar')==yes){echo 'checked="checked"';} ?>  />
                  </label>
                  <label> <?php _e('OFF','wpc'); ?>
                    <input type="radio" name="wpc_sidebar" value="no" <?php if(get_option('wpc_sidebar')==no){echo 'checked="checked"';} ?> />
                  </label></td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="sidebar">
                    <?php _e('Custom Fields','wpc') ?>
                  </label></th>
                <td><label><?php _e('ON','wpc'); ?>
                    <input type="radio" name="wpc_custom_fields" value="yes" <?php if(get_option('wpc_custom_fields')==yes){echo 'checked="checked"';} ?>  />
                  </label>
                  <label> <?php _e('OFF','wpc'); ?>
                    <input type="radio" name="wpc_custom_fields" value="no" <?php if(get_option('wpc_custom_fields')==no){echo 'checked="checked"';} ?> />
                  </label></td>
              </tr>
              <!--WPC pro version 1.0's feature closing-->
              <tr>
                <th scope="row"><label><?php _e('Gallery Image','wpc') ?></label></th>
                <td><input name="wpc_image_height" type="text" id="wpc_image_height" value="<?php if(get_option('wpc_image_height')){ echo get_option('wpc_image_height'); }else {echo 358;} ?>" size="10">
                  &nbsp;&nbsp;&nbsp;<span><?php _e('Height','wpc') ?></span>&nbsp;&nbsp;&nbsp;
                  <input name="wpc_image_width" type="text" id="wpc_image_width" value="<?php if(get_option('wpc_image_width')){ echo get_option('wpc_image_width'); }else {echo 500;} ?>" size="10">
                  &nbsp;&nbsp;&nbsp;<span><?php _e('Width','wpc') ?></span><br>
                  <select id="wpc_croping" name="wpc_croping">
                    <option value="wpc_image_scale_crop" <?php if(get_option('wpc_croping')=='wpc_image_scale_crop'){echo 'selected="selected"';} ?>><?php _e('Scale & Crop', 'wpc') ?></option>
                    <option value="image_scale_fit" <?php if(get_option('wpc_croping')=='image_scale_fit'){echo 'selected="selected"';} ?>><?php _e('Scale To Fit', 'wpc') ?></option>
                  </select></td>
              </tr>
              <tr>
                <th scope="row"><label><?php _e('Thumbnail','wpc') ?></label></th>
                <td><input name="wpc_thumb_height" type="text" id="wpc_thumb_height" value="<?php if(get_option('wpc_thumb_height')){ echo get_option('wpc_thumb_height'); }else {echo 151;} ?>" size="10">
                  &nbsp;&nbsp;&nbsp;<span><?php _e('Height','wpc') ?></span>&nbsp;&nbsp;&nbsp;
                  <input name="wpc_thumb_width" type="text" id="wpc_thumb_width" value="<?php if(get_option('wpc_thumb_width')){ echo get_option('wpc_thumb_width'); }else {echo 212;} ?>" size="10">
                  &nbsp;&nbsp;&nbsp;<span><?php _e('Width','wpc') ?></span><br>
                  <select id="wpc_croping" name="wpc_tcroping">
                    <option value="wpc_thumb_scale_crop" <?php if(get_option('wpc_tcroping')=='wpc_thumb_scale_crop'){echo 'selected="selected"';} ?>><?php _e('Scale & Crop','wpc') ?></option>
                    <option value="thumb_scale_fit" <?php if(get_option('wpc_tcroping')=='thumb_scale_fit'){echo 'selected="selected"';} ?>><?php _e('Scale To Fit','wpc') ?></option>
                  </select></td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="pagination"><?php _e('Show Next/Prev Links','wpc') ?> </label></th>
                <td><input type="radio" name="wpc_next_prev" value="1" <?php if(get_option('wpc_next_prev')==1){echo 'checked="checked"';} ?> />
                  <?php _e('Yes','wpc'); ?> &nbsp;&nbsp;&nbsp;
                  <input type="radio" name="wpc_next_prev" value="0" <?php if(get_option('wpc_next_prev')==0){echo 'checked="checked"';} ?>/>
                  <?php _e('No','wpc'); ?><br />
                  <span></span></td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="inn_temp_head"><?php _e('Inner Template Header','wpc') ?></label></th>
                <td><textarea name="wpc_inn_temp_head" cols="90" rows="7" class="widefat inn_temp"><?php echo get_option('wpc_inn_temp_head'); ?></textarea>
                  <small style="position:relative; top:-10px;"><?php _e('Use this area to add your theme layout divs if you are having problems with inner pages of the catalogue.','wpc') ?></small></td>
              </tr>
              <tr valign="top">
                <th scope="row"><label for="inn_temp_foot"><?php _e('Inner Template Footer','') ?></label></th>
                <td><textarea name="wpc_inn_temp_foot" cols="90" rows="7" class="widefat inn_temp"><?php echo get_option('wpc_inn_temp_foot'); ?></textarea>
                  <small style="position:relative; top:-10px;"><?php _e('Use this area to close the open divs after the WP Catalogue.','wpc') ?></small></td>
              </tr>
            </tbody>
          </table>
          <p class="submit">
            <input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes">
          </p>
        </form>
        <br class="clear">
      </div>
      <br class="clear">
    </div>
  </div>
  <div class="wpc-right-liquid">
  <table cellpadding="0" class="widefat" style="margin-bottom:10px;" width="50%">
      <thead>
      <th scope="col"><strong style="color:#008001;"><?php _e('How to use this plugin','wpc') ?></strong></th>
        </thead>
      <tbody>
        <tr>
          <td style="border:0;"><?php _e('You can use 3 shortcodes','wpc') ?></td>
        </tr>
        <tr>
          <td style="border:0;">
         <b>1. [wp-catalogue]  </b><?php _e('to display complete catalogue','wpc') ?>
          </td>
        </tr>
        <tr>
          <td style="border:0;"><b>2. [wp-catalogue featured="true"]</b>  <?php _e('to display featured products anywhere on your blog.','wpc') ?> </td>
        </tr>
        <tr>
          <td style="border:0;"><b>3. [wp-catalogue wpcat="wpc-category-slug"] </b> <?php _e('to display products from specific category.','wpc') ?> </td>
        </tr>
      </tbody>
    </table>
    <table cellpadding="0" class="widefat donation" style="margin-bottom:10px; border:solid 2px #008001;" width="50%">
      <thead>
      <th scope="col"><strong style="color:#008001;"><?php _e('Help Improve This Plugin!','wpc') ?></strong></th>
        </thead>
      <tbody>
        <tr>
          <td style="border:0;"><?php _e('Enjoyed this plugin? All donations are used to improve and further develop this plugin. Thanks for your contribution.','wpc') ?></td>
        </tr>
        <tr>
          <td style="border:0;"><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
              <input type="hidden" name="cmd" value="_s-xclick">
              <input type="hidden" name="hosted_button_id" value="A74K2K689DWTY">
              <input type="image" src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
              <img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
            </form></td>
        </tr>
        <tr>
          <td style="border:0;"><?php _e('you can also help by','wpc') ?> <a href="http://wordpress.org/support/view/plugin-reviews/wp-catalogue" target="_blank"><?php _e('rating this plugin on wordpress.org','wpc') ?></a></td>
        </tr>
      </tbody>
    </table>
    <table cellpadding="0" class="widefat" border="0">
      <thead>
      <th scope="col"><?php _e('Need Support?','wpc') ?></th>
        </thead>
      <tbody>
        <tr>
          <td style="border:0;"><?php _e('Check out the','wpc') ?> <a href="http://enigmaplugins.com/documentation/" target="_blank"><?php _e('FAQs for Documentation','wpc'); ?></a> <?php _e('and','wpc') ?> <a href="http://enigmaplugins.com/contact-support" target="_blank"><?php _e('Support','wpc') ?></a></td>
        </tr>
      </tbody>
    </table>
  </div>
  <br class="clear">
</div>
