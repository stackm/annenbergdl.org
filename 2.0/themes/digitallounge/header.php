<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Digital Lounge
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<style type="text/css">

</style>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">


<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'digitallounge' ); ?></a>

	<header id="masthead" class="site-header" role="banner">

		<div class="site-branding">
			<h1 class="site-title">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			</h1> 
			
		</div><!-- .site-branding -->
		<div id="navbuttons">
			<ul>
			<li><a href="#"><span class="chatwithus">chat with us</span></a></li>
			<li><div class="binoculars"><a href="#"><img src="<?php bloginfo('template_directory'); ?>/img/binoc.svg" width="50" height="50"></a>
			</div></li>
			<li><div class="usclogo">
				<a href="#"><img src="<?php bloginfo('template_directory'); ?>/img/usclogo.svg" width="50" height="50"></a>
			</div></li>
			</ul>
		</div>
	</header><!-- #masthead -->

	<div id="content" class="site-content">
