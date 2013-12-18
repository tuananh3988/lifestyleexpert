<?php
/**
 * Template Name: Contact page
 *
 * @package WordPress
 * @subpackage lifestyleexpert
 * @since lifestyleexpert
 */

get_header(); ?>

<div class="wrap-contactpage">
  <div class="contactpage-content">
    <?php
			$imageLeft = get_post_custom_values('image_left'); 
      $imageLeft = isset($imageLeft[0]) ? $imageLeft[0] : '';
      
      $phone = get_post_custom_values('phone'); 
      $phone = isset($phone[0]) ? $phone[0] : '';
      
      $liveHelp = get_post_custom_values('live_help'); 
      $liveHelp = isset($liveHelp[0]) ? $liveHelp[0] : '';
      
      $other = get_post_custom_values('other'); 
      $other = isset($other[0]) ? $other[0] : '';
		?>
    <div class="image-left">
      <img src="<?php echo $imageLeft; ?>" style="width: 100%;" />
    </div>
    <div class="tab-content">
      <div class="wrap-tab-content">
        <h2>Contact Us</h2>
        <div id="tabs">
          <ul>
            <li><a href="#tabs-1">Phone</a></li>
            <li><a href="#tabs-2">Live Help</a></li>
            <li><a href="#tabs-3">Email</a></li>
            <li><a href="#tabs-4">Other</a></li>
          </ul>
          <div id="tabs-1">
            <p><?php echo $phone; ?></p>
          </div>
          <div id="tabs-2">
            <p><?php echo $liveHelp; ?></p>
          </div>
          <div id="tabs-3">
            <?php echo do_shortcode('[contact-form-7 id="23" title="Contact form 1"]'); ?>
          </div>
          <div id="tabs-4">
            <p><?php echo $other; ?></p>
          </div>
        </div>
      </div>
      
    </div>
    <div class="clear"  ></div>
  </div>
</div>

<?php get_footer(); ?>