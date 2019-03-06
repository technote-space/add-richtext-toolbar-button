<?php
/**
 * WP_Framework_Presenter Configs Filter
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'minify' => [
		'admin_print_footer_scripts' => [
			'output_js' => 999,
		],
		'admin_head'                 => [
			'output_css' => 999,
		],
		'admin_footer'               => [
			'output_css' => 999,
			'end_footer' => 999,
		],

		'wp_print_footer_scripts' => [
			'output_js'  => 999,
			'output_css' => 998,
			'end_footer' => 999,
		],
		'wp_print_styles'         => [
			'output_css' => 999,
		],
	],

];