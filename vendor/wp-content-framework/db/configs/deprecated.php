<?php
/**
 * WP_Framework_Db Configs Deprecated
 *
 * @version 0.0.14
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'\WP_Framework_Db\Classes\Models\Db' => '\WP_Framework_Db\Deprecated\Classes\Models\Db',

];