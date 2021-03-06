<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?>

		</div><!-- #main -->
		<footer id="colophon" class="site-footer" role="contentinfo">
			<div class="footer-nav">
				<?php 
					$menu = wp_get_nav_menu_items( 3 ); 
					$content = '';
					foreach ($menu as $key => $value) {
						$class = (get_permalink() == $value->url) ? 'active' : '';
						$content .= '<a class="' . $class . '" href="' . $value->url . '">' . $value->title . '</a> | ';
					}
					
					echo substr($content, 0, -2);
				?>
				<p>All rights reserved - Master Drafters - 2013</p>
			</div>
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>