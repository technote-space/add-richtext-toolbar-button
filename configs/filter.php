<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	exit;
}

return [

	'\Richtext_Toolbar_Button\Classes\Models\Assets' => [
		'${prefix}post_load_admin_page' => [
			'remove_setting',
		],
		'wp_head'                       => [
			'setup_assets',
		],
		'${prefix}changed_option'       => [
			'changed_option',
		],
		'${prefix}app_activated'        => [
			'clear_cache_file',
		],
		'upgrader_process_complete'     => [
			'clear_cache_file',
		],
	],

	'\Richtext_Toolbar_Button\Classes\Models\Editor' => [
		'enqueue_block_editor_assets' => [
			'enqueue_block_editor_assets',
		],
	],

	'\Richtext_Toolbar_Button\Classes\Models\Custom_Post\Setting' => [
		'${prefix}app_activated' => [
			'insert_presets',
		],
		'load-edit.php'          => [
			'setup_assets',
		],
	],
];
