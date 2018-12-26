<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Doctor Directory
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php 
	if ( function_exists('docdirect_get_favicon') ) { docdirect_get_favicon(); }
	wp_head();
?>
</head>
<body <?php body_class()?>>
<?php do_action('kt_docdirect_init_headers');?>