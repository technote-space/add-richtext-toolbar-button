<?php
/**
 * WP_Framework_Mail Configs Filter
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'mail' => [
		'wp_mail_failed'    => [
			'wp_mail_failed',
		],
		'wp_mail_from'      => [
			'wp_mail_from',
		],
		'wp_mail_from_name' => [
			'wp_mail_from_name',
		],
	],

];