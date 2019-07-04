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

	'bold'             => 'font-weight: bold;',
	'font color'       => 'color: #f00;',
	'font size'        => 'font-size: 1.5em;',
	'line height'      => 'line-height: 1.5;',
	'background color' => 'background-color: #9ff;',
	'border'           => 'border: solid 2px #f9f;',
	'border radius'    => 'border-radius: 5px;',
	'padding'          => 'padding: .5em;',
	'shadow'           => 'box-shadow: 3px 3px 3px #ccc;',
	'highlighter'      => 'background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0) 60%, #6f6 75%);',
	'stripe'           => [
		'background-image: repeating-linear-gradient(-45deg, rgb(64, 255, 0), rgb(64, 255, 0) 2px, transparent 2px, transparent 4px);',
		'background-size: 100% 0.6em',
		'padding-bottom: 0.6em',
		'background-position: 0 center',
		'background-repeat: no-repeat',
	],
	'block'            => 'display: block;',
	'inline block'     => 'display: inline-block;',
	'icon'             => function ( $font_family ) {
		return [
			'display: block;',
			'padding: 1em 1em 1em 4.6em;',
			'position: relative;',
			'line-height: 1.4;',
			'background: #f0f9ff;',
			'border: 1px solid #acf;',
			'[after] content: " ";',
			'[after] display: block;',
			'[after] width: 0;',
			'[after] height: 50%;',
			'[after] position: absolute;',
			'[after] top: 25%;',
			'[after] left: 3.4em;',
			'[after] opacity: 0.6;',
			'[after] border-right: 2px dashed;',
			'[after] border-right-color: #76b3f7;',
			"[before] font-family: {$font_family};",
			'[before] content: "\f06a";',
			'[before] font-weight: 900;',
			'[before] color: #9cf;',
			'[before] display: inline-block;',
			'[before] position: absolute;',
			'[before] top: 50%;',
			'[before] left: 1.2em;',
			'[before] transform: translateY(-50%) scale(1.5);',
		];
	},
	'tab'              => function ( $font_family ) {
		return [
			'display: block;',
			'position: relative;',
			'border: 2px solid #f94;',
			'padding: 1.2em 1em;',
			'margin-top: 1.4em;',
			'[before] position: absolute;',
			"[before] font-family: {$font_family};",
			'[before] content: "\f0f3  tab";',
			'[before] left: -2px;',
			'[before] top: -1.8em',
			'[before] font-size: .8em;',
			'[before] font-weight: 900;',
			'[before] padding: 0 1em 0 .8em;',
			'[before] background-color: #f94;',
			'[before] color: white;',
			'[before] border-radius: 6px 6px 0 0;',
			'[before] line-height: 1.8em;',
		];
	},
	'tag'              => [
		'display: block;',
		'border-left: 6px solid #06c;',
		'padding: 1.2em 1em;',
		'background-color: #def;',
	],
	'label'            => function ( $font_family ) {
		return [
			'display: block;',
			'position: relative;',
			'padding: 1em .5em .7em;',
			'background-color: #d9d9d9;',
			'[before] position: absolute;',
			"[before] font-family: {$font_family};",
			'[before] content: "\f005  label";',
			'[before] right: 0;',
			'[before] top: 0;',
			'[before] font-size: .6em;',
			'[before] font-weight: 900;',
			'[before] padding: 0 .8em;',
			'[before] background-color: #666;',
			'[before] color: white;',
			'[before] line-height: 1.6em;',
			'[before] white-space: pre;',
		];
	},
	'warning'          => function ( $font_family ) {
		return [
			'display: block;',
			'padding: 1em 1em 1em 3em;',
			'position: relative;',
			'line-height: 1.4;',
			'background-color: #fbeaea;',
			'border-width: 0 0 0 5px;',
			'border-style: solid;',
			'border-color: #dc3232;',
			"[before] font-family: {$font_family};",
			'[before] content: "\f057";',
			'[before] font-weight: 900;',
			'[before] color: #dc3232;',
			'[before] display: inline-block;',
			'[before] position: absolute;',
			'[before] top: 50%;',
			'[before] left: 1.2em;',
			'[before] transform: translateY(-50%) scale(1.5);',
		];
	},

];
