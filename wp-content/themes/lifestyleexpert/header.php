<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
  <link href='http://fonts.googleapis.com/css?family=Lato:700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/style.css" />
  <link href="<?php bloginfo('template_directory'); ?>/css/custom-theme/jquery-ui-1.10.3.custom.css" rel="stylesheet">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.9.1.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery-ui-1.10.3.custom.js"></script>
	<script>
	$(function() {	
		$( "#tabs" ).tabs({ active: 1 });
	});
  </script>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div id="page" class="hfeed site">
		<div id="masthead" class="site-header" role="banner">
      <div id="wrap-headder">
        <div class="wrap-logo">
          <a class="home-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
            <img alt="logo" class="logo" src="<?php bloginfo('template_directory'); ?>/images/logopage.png" />
          </a>
        </div>
        <div class="main-nav">
					<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
				</div>
        <div class="clear"></div>
      </div>
			

		</div><!-- #masthead -->

		<div id="main" class="site-main">
