<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Richtext_Toolbar_Button\Classes\Models;

use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;

// @codeCoverageIgnoreStart
if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	exit;
}
// @codeCoverageIgnoreEnd

/**
 * Class Validation
 * @package Richtext_Toolbar_Button\Classes\Models
 */
class Validation implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook {

	use Singleton, Hook, Package;

	/**
	 * @param string $class_name
	 * @param int $priority
	 * @param array $post_array
	 * @param array $errors
	 *
	 * @return array
	 */
	public function validate_class_name( $class_name, $priority, array $post_array, array $errors ) {
		/** @var Custom_Post\Setting $setting */
		$setting = Custom_Post\Setting::get_instance( $this->app );
		if ( preg_match( '/\A' . preg_quote( $setting->get_default_class_name_prefix(), '/' ) . '/', $class_name ) ) {
			$errors['class_name'][] = $this->translate( 'The value is unusable.' );

			return $errors;
		}
		if ( ! preg_match( '/\A([_a-zA-Z]+[a-zA-Z0-9-]*)(\s+[_a-zA-Z]+[_a-zA-Z0-9-]*)*\z/', $class_name ) ) {
			$errors['class_name'][] = $this->translate( 'Invalid format.' );
			$errors['class_name'][] = $this->translate( 'A class name must begin with a letter, followed by any number of hyphens, letters, or numbers.' );

			return $errors;
		}

		if ( ! isset( $post_array['ID'] ) ) {
			$post_array['ID'] = -1;
		}
		if ( $this->app->db->builder()
			->from( $setting->get_related_table_name() )
			->where( 'post_id', '<>', $post_array['ID'] )
			->where( 'class_name', $class_name )
			->exists() ) {
			$errors['class_name'][] = $this->translate( 'The value has already been used.' );
		} else {
			// この時点で $class_name は 英数及びアンダーバー、ハイフン、スーペースのみ
			$replace = " {$class_name} ";
			if ( $this->app->db->builder()
				->from( $setting->get_related_table_name() )
				->where( 'post_id', '<>', $post_array['ID'] )
				->where( 'priority', '<=', $priority )
				->where_raw( "LENGTH(%s) <> LENGTH(REPLACE(%s, CONCAT(' ', class_name, ' '), ''))", [ $replace, $replace ] )
				->exists() ) {
				$errors['class_name'][] = $this->translate( 'The value is included in the class name of other settings.' );
			}
		}

		return $errors;
	}

	/**
	 * @param string $tag_name
	 * @param array $errors
	 *
	 * @return array
	 */
	public function validate_tag_name( $tag_name, array $errors ) {
		if ( 'div' === strtolower( $tag_name ) ) {
			$errors['tag_name'][] = $this->translate( 'This tag name is unusable.' );
		} elseif ( ! preg_match( '/\A[a-zA-Z]+\z/', $tag_name ) ) {
			$errors['tag_name'][] = $this->translate( 'Invalid format.' );
		}

		return $errors;
	}
}
