<?php
/**
 * WP_Framework_Core Configs Map
 *
 * @version 0.0.51
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'common' => [
		'define'     => '\WP_Framework_Common\Classes\Models\Define',
		'config'     => '\WP_Framework_Common\Classes\Models\Config',
		'setting'    => '\WP_Framework_Common\Classes\Models\Setting',
		'filter'     => '\WP_Framework_Common\Classes\Models\Filter',
		'uninstall'  => '\WP_Framework_Common\Classes\Models\Uninstall',
		'utility'    => '\WP_Framework_Common\Classes\Models\Utility',
		'option'     => '\WP_Framework_Common\Classes\Models\Option',
		'user'       => '\WP_Framework_Common\Classes\Models\User',
		'input'      => '\WP_Framework_Common\Classes\Models\Input',
		'array'      => '\WP_Framework_Common\Classes\Models\Array_Utility',
		'string'     => '\WP_Framework_Common\Classes\Models\String_Utility',
		'file'       => '\WP_Framework_Common\Classes\Models\File_Utility',
		'deprecated' => '\WP_Framework_Common\Classes\Models\Deprecated',
		'system'     => '\WP_Framework_Common\Classes\Models\System',
	],
	'cache'  => [
		'cache' => '\WP_Framework_Cache\Classes\Models\Cache',
	],

];