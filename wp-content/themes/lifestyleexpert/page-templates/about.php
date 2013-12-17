<?php
/**
 * Template Name: About page
 *
 * @package WordPress
 * @subpackage lifestyleexpert
 * @since lifestyleexpert
 */

get_header(); ?>

<div class="wrap-aboutpage">
  <div class="aboutpage-content">
    <?php
			$topImage = get_post_custom_values('image_top_content'); 
			if(isset($topImage[0])) {
				echo '<img src="' . $topImage[0] . '" style="width:100%;" />';
			}
		?>
		<div id="content" class="site-content" role="main">
		<?php if ( have_posts() ) : ?>

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>

		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>

		</div><!-- #content -->
  </div>
</div>

<?php get_footer(); ?>