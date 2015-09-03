<?php

// enqueue SOUP styles

function enqueue_soup_styles() {
	wp_enqueue_style( 'soup', SOUP_PLUGIN_URL . 'css/soup.css' );
}

add_action( 'wp_enqueue_scripts', 'enqueue_soup_styles' );
