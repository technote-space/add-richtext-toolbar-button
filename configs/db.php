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

	'setting' => [
		'columns' => [
			'post_id'                 => [
				'type'     => 'BIGINT(20)',
				'unsigned' => true,
				'null'     => false,
				'comment'  => 'post id',
			],
			'tag_name'                => [
				'type'    => 'VARCHAR(32)',
				'default' => 'span',
				'comment' => 'tag name',
			],
			'class_name'              => [
				'type'    => 'VARCHAR(64)',
				'comment' => 'class name',
			],
			'group_name'              => [
				'type'    => 'VARCHAR(64)',
				'comment' => 'group name',
			],
			'icon'                    => [
				'type'    => 'TEXT',
				'comment' => 'icon',
			],
			'style'                   => [
				'type'    => 'TEXT',
				'comment' => 'style',
			],
			'priority'                => [
				'type'     => 'INT(11)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 10,
				'comment'  => 'priority',
			],
			'is_valid_toolbar_button' => [
				'type'     => 'BIT(1)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 1,
				'comment'  => 'Validity of toolbar button',
			],
		],
		'index'   => [
			'key'    => [
				'priority' => [ 'priority' ],
			],
			'unique' => [
				'uk_post_id' => [ 'post_id' ],
			],
		],
		'comment' => 'settings',
	],

];
