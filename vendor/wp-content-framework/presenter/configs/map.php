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

	'drawer' => '\WP_Framework_Presenter\Classes\Models\Drawer',
	'minify' => '\WP_Framework_Presenter\Classes\Models\Minify',

];