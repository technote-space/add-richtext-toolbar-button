<?php
/**
 * WP_Framework_Log Configs Db
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

	'__log' => [
		'columns' => [
			'level'              => [
				'type' => 'VARCHAR(32)',
				'null' => false,
			],
			'message'            => [
				'type' => 'TEXT',
				'null' => false,
			],
			'context'            => [
				'type' => 'LONGTEXT',
				'null' => true,
			],
			'file'               => [
				'type' => 'VARCHAR(255)',
				'null' => true,
			],
			'line'               => [
				'type'     => 'INT(11)',
				'unsigned' => true,
				'null'     => true,
			],
			'framework_version'  => [
				'type' => 'VARCHAR(32)',
				'null' => false,
			],
			'framework_packages' => [
				'type' => 'TEXT',
			],
			'plugin_version'     => [
				'type' => 'VARCHAR(32)',
				'null' => false,
			],
			'php_version'        => [
				'type' => 'VARCHAR(32)',
				'null' => false,
			],
			'wordpress_version'  => [
				'type' => 'VARCHAR(32)',
				'null' => false,
			],
		],
		'index'   => [
			'key' => [
				'created_at' => [ 'created_at' ],
			],
		],
	],

];
