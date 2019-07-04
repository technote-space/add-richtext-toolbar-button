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

	[
		'name'                    => 'Source code',
		'tag_name'                => 'code',
		'icon'                    => 'dashicons-editor-code',
		'is_valid_toolbar_button' => 0,
		'priority'                => 100,
	],

	[
		'name'                    => 'Superscript',
		'tag_name'                => 'sup',
		'icon'                    => 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iLTg1IC05NzUgMTAyNCAxMDI0Ij4KPHBhdGggYXJpYS1oaWRkZW49InRydWUiIHJvbGQ9ImltZyIgZm9jdXNhYmxlPSJmYWxzZSIgdHJhbnNmb3JtPSJzY2FsZSgxLCAtMSkiIHRyYW5zbGF0ZT0iKDAsIC05NjApIiBkPSJNNzY4IDc1NHYtNTBoMTI4di02NGgtMTkydjE0NmwxMjggNjB2NTBoLTEyOHY2NGgxOTJ2LTE0NnpNNjc2IDcwNGgtMTM2bC0xODgtMTg4LTE4OCAxODhoLTEzNmwyNTYtMjU2LTI1Ni0yNTZoMTM2bDE4OCAxODggMTg4LTE4OGgxMzZsLTI1NiAyNTZ6Ii8+Cjwvc3ZnPg==',
		'is_valid_toolbar_button' => 0,
		'priority'                => 100,
	],

	[
		'name'                    => 'Subscript',
		'tag_name'                => 'sub',
		'icon'                    => 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iLTg1IC05NzUgMTAyNCAxMDI0Ij4KPHBhdGggYXJpYS1oaWRkZW49InRydWUiIHJvbGQ9ImltZyIgZm9jdXNhYmxlPSJmYWxzZSIgdHJhbnNmb3JtPSJzY2FsZSgxLCAtMSkiIHRyYW5zbGF0ZT0iKDAsIC05NjApIiBkPSJNNzY4IDUwdi01MGgxMjh2LTY0aC0xOTJ2MTQ2bDEyOCA2MHY1MGgtMTI4djY0aDE5MnYtMTQ2ek02NzYgNzA0aC0xMzZsLTE4OC0xODgtMTg4IDE4OGgtMTM2bDI1Ni0yNTYtMjU2LTI1NmgxMzZsMTg4IDE4OCAxODgtMTg4aDEzNmwtMjU2IDI1NnoiLz4KPC9zdmc+',
		'is_valid_toolbar_button' => 0,
		'priority'                => 100,
	],

	[
		'name'                    => 'Highlighter(Red)',
		'group_name'              => 'Pen',
		'style'                   => 'background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0) 60%, #f69 75%); font-weight: bold;',
		'is_valid_toolbar_button' => 1,
		'priority'                => 50,
	],

	[
		'name'                    => 'Highlighter(Green)',
		'group_name'              => 'Pen',
		'style'                   => 'background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0) 60%, #6f6 75%); font-weight: bold;',
		'is_valid_toolbar_button' => 1,
		'priority'                => 50,
	],

	[
		'name'                    => 'Highlighter(Blue)',
		'group_name'              => 'Pen',
		'style'                   => 'background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0) 60%, #6cf 75%); font-weight: bold;',
		'is_valid_toolbar_button' => 1,
		'priority'                => 50,
	],

	[
		'name'                    => 'Highlighter(Yellow)',
		'group_name'              => 'Pen',
		'style'                   => 'background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0) 60%, #ff6 75%); font-weight: bold;',
		'is_valid_toolbar_button' => 1,
		'priority'                => 50,
	],

];
