<?php	
register_nav_menus( array(
  'primary'   => __( 'Top primary menu', 'lifestyleexpert' ),
  'secondary' => __( 'Secondary menu in left sidebar', 'twentyfourteen' ),
) );

add_action("login_head", "my_login_head");
function my_login_head() {
	echo "
	<style>
	body.login #login h1 a {
		background: url('".get_bloginfo('template_url')."/images/logopage.png') no-repeat scroll center top transparent;
		height: 54px;
		width: 219px;
	}
	</style>
	";
}