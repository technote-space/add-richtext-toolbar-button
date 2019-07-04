<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Richtext_Toolbar_Button\Classes\Models\Custom_Post;

use Richtext_Toolbar_Button\Classes\Models\Assets;
use Richtext_Toolbar_Button\Classes\Models\Style;
use Richtext_Toolbar_Button\Classes\Models\Validation;
use Richtext_Toolbar_Button\Traits\Models\Custom_Post;
use WP_Framework_Db\Classes\Models\Query\Builder;
use WP_Post;

// @codeCoverageIgnoreStart
if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	exit;
}
// @codeCoverageIgnoreEnd

/**
 * Class Setting
 * @package Richtext_Toolbar_Button\Classes\Models\Custom_Post
 */
class Setting implements \Richtext_Toolbar_Button\Interfaces\Models\Custom_Post {

	use Custom_Post;

	/**
	 * @var array $cache_setting
	 */
	private $cache_setting = [];

	/**
	 * @var array $cache_settings
	 */
	private $cache_settings = [];

	/**
	 * insert presets
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function insert_presets() {
		if ( $this->app->get_option( 'has_inserted_presets' ) ) {
			return;
		}
		$this->app->option->set( 'has_inserted_presets', true );

		foreach ( $this->apply_filters( 'get_setting_presets', $this->app->get_config( 'preset' ) ) as $item ) {
			$item['post_title'] = $this->translate( $this->app->array->search( $item, 'name', 'class_name', 'tag_name', '' ) );
			unset( $item['name'] );
			if ( ! empty( $item['group_name'] ) ) {
				$item['group_name'] = $this->translate( $item['group_name'] );
			}
			$this->insert( $item );
		}
	}

	/**
	 * setup assets
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function setup_assets() {
		global $typenow;
		if ( empty( $typenow ) || $typenow !== $this->get_post_type() ) {
			return;
		}

		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );
		$assets->enqueue_plugin_assets();
		$this->add_script_view( 'admin/script/custom_post/preview', [
			'css_handle'         => $assets->get_css_handle(),
			'fontawesome_handle' => $this->apply_filters( 'is_valid_fontawesome' ) ? $this->app->get_config( 'config', 'fontawesome_handle' ) : null,
		] );
		$this->add_style_view( 'admin/style/custom_post/preview', [
			'post_type' => $this->get_post_type(),
		] );
	}

	/**
	 * setup page
	 */
	public function setup_page() {
		if ( $this->apply_filters( 'is_valid_fontawesome' ) ) {
			$this->setup_fontawesome();
		}
	}

	/**
	 * @param array $params
	 * @param WP_Post $post
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function filter_edit_form_params(
		/** @noinspection PhpUnusedParameterInspection */
		array $params, $post
	) {
		$setting_details = $this->get_setting_details( 'setting' );
		$columns         = [];
		foreach ( $setting_details as $key => $args ) {
			$column         = $this->app->array->get( $params['columns'], $key, function () use ( $args ) {
				return $this->app->array->get( $args, 'detail', [] );
			} );
			$column['args'] = $args;
			unset( $column['args']['name'], $column['args']['value'], $column['args']['selected'], $column['args']['attributes']['checked'] );
			$column['args']['attributes']['data-default'] = $column['args']['attributes']['data-value'];
			$column['default']                            = $column['args']['attributes']['data-default'];
			if ( empty( $column['args']['attributes']['data-option_name'] ) ) {
				$column['args']['attributes']['data-option_name'] = $key;
			}
			$column['is_user_defined'] = true;
			if ( empty( $column['type'] ) ) {
				$column['type'] = 'VARCHAR';
			}
			$column['form_type'] = $this->get_form_type( $column );
			$columns[ $key ]     = $column;
		}
		$params['columns']            = $columns;
		$params                       = $this->app->array->set( $params, 'columns.class_name.args.attributes.data-default', $this->get_default_class_name( $post->ID ) );
		$params                       = $this->app->array->set( $params, 'columns.class_name.default', $this->app->array->get( $params, 'columns.class_name.args.attributes.data-default' ) );
		$params['name_prefix']        = $this->get_post_field_name_prefix();
		$params['groups']             = $this->get_groups();
		$params['fontawesome_handle'] = $this->apply_filters( 'is_valid_fontawesome' ) ? $this->app->get_config( 'config', 'fontawesome_handle' ) : null;

		return $params;
	}

	/**
	 * @param array $column
	 *
	 * @return string
	 */
	private function get_form_type( array $column ) {
		return ! empty( $column['args']['form_type'] ) ? $column['args']['form_type'] : ( empty( $column['form_type'] ) ? $this->get_form_by_type( $column['type'] ) : $column['form_type'] );
	}


	/**
	 * @param WP_Post $post
	 * @param array $params
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function before_output_edit_form(
		/** @noinspection PhpUnusedParameterInspection */
		WP_Post $post, array $params
	) {
		$this->setup_dashicon_picker();
		$this->setup_media_uploader();
	}

	/**
	 * @return array
	 */
	private function get_setting_list() {
		return [
			'tag_name'                => [],
			'class_name'              => [],
			'group_name'              => [
				'default' => $this->apply_filters( 'default_group' ),
			],
			'icon'                    => [
				'args' => [
					'form_type' => 'icon',
				],
			],
			'style'                   => [
				'args' => [
					'target'               => [
						'setting',
						'front',
					],
					'form_type'            => 'style',
					'preset'               => $this->get_preset(),
					'is_valid_fontawesome' => $this->apply_filters( 'is_valid_fontawesome' ),
				],
			],
			'styles'                  => [
				'args' => [
					'target' => [
						'front',
					],
				],
			],
			'test'                    => [
				'args' => [
					'target'    => [
						'setting',
					],
					'form_type' => 'test',
				],
			],
			'is_valid_toolbar_button' => [
				'args' => [
					'target' => [
						'setting',
						'front',
						'editor',
					],
				],
			],
			'priority'                => [
				'args' => [
					'target' => [
						'setting',
						'front',
					],
				],
			],
		];
	}

	/**
	 * @param string $target
	 *
	 * @return array
	 */
	private function get_setting_details( $target ) {
		$args = [];
		foreach ( $this->get_setting_list() as $key => $setting ) {
			if ( is_array( $setting ) && ! empty( $setting['args']['target'] ) && ! in_array( $target, $setting['args']['target'], true ) ) {
				continue;
			}
			$args[ $key ] = $this->get_setting( $key, $setting );
		}

		return $args;
	}

	/**
	 * @param string $name
	 * @param string|array $setting
	 *
	 * @return array
	 */
	private function get_setting( $name, $setting ) {
		if ( ! isset( $this->cache_setting[ $name ] ) ) {
			$columns = $this->app->db->get_columns( $this->get_related_table_name() );
			$detail  = $this->app->array->get( is_array( $setting ) ? $setting : [], 'detail', function () use ( $columns, $name ) {
				return $this->app->array->get( $columns, $name, [] );
			} );
			$value   = $this->app->array->get( is_array( $setting ) ? $setting : [], 'default', function () use ( $detail ) {
				return $this->app->array->get( $detail, 'default' );
			} );
			$ret     = [
				'class'      => 'add-richtext-toolbar-option',
				'name'       => $this->get_post_field_name_prefix() . $name,
				'value'      => $value,
				'label'      => $this->translate( $this->app->array->get( $detail, 'comment', $name ) ),
				'attributes' => [
					'data-value'   => $value,
					'data-default' => $value,
				],
				'detail'     => $detail,
				'type'       => $this->app->utility->parse_db_type( $this->app->array->get( $detail, 'type' ) ),
			];
			if ( is_array( $setting ) ) {
				$ret = array_replace_recursive( $ret, isset( $setting['args'] ) && is_array( $setting['args'] ) ? $setting['args'] : [] );
			}
			if ( 'bool' === $ret['type'] ) {
				$ret['value'] = 1;
				if ( ! empty( $value ) ) {
					$ret['attributes']['checked'] = 'checked';
				}
				$ret['label'] = $this->translate( 'Valid' );
			}
			$this->cache_setting[ $name ] = $ret;
		}

		return $this->cache_setting[ $name ];
	}

	/**
	 * @return null|string
	 */
	protected function get_post_column_title() {
		return $this->translate( 'Setting name' );
	}

	/**
	 * @return array
	 */
	protected function get_manage_posts_columns() {
		return [
			'preview'                 => $this->get_manage_column_preview(),
			'display'                 => $this->get_manage_column_display(),
			'is_valid_toolbar_button' => [
				'name'                  => $this->translate( 'Validity of toolbar button' ),
				'callback'              => function ( $value ) {
					return ! empty( $value ) ? $this->translate( 'Valid' ) : $this->translate( 'Invalid' );
				},
				'sortable'              => true,
				'default_sort'          => true,
				'default_sort_priority' => 1,
				'desc'                  => true,
			],
			'priority'                => [
				'name'                  => $this->translate( 'priority' ),
				'value'                 => '',
				'sortable'              => true,
				'default_sort'          => true,
				'default_sort_priority' => 5,
			],
		];
	}

	/**
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	private function get_manage_column_preview() {
		return [
			'name'     => $this->translate( 'preview' ),
			'callback' => function (
				/** @noinspection PhpUnusedParameterInspection */
				$value, $data, $post
			) {
				$setting_details    = $this->get_setting_details( 'list' );
				$data['class_name'] = $this->get_setting_class_name( $data, $post );
				foreach ( [ 'class_name', 'tag_name' ] as $key ) {
					$setting    = $this->app->array->get( $setting_details, $key );
					$is_default = $this->is_default( $data[ $key ] );
					if ( $is_default ) {
						$data[ $key ] = $setting['value'];
					}
					$details[ $setting['label'] ] = $data[ $key ];
				}

				return $this->get_view( 'admin/custom_post/setting/preview', [
					'class_name' => $data['class_name'],
					'tag_name'   => $data['tag_name'],
				] );
			},
			'unescape' => true,
		];
	}

	/**
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	private function get_manage_column_display() {
		return [
			'name'     => $this->translate( 'display' ),
			'callback' => function (
				/** @noinspection PhpUnusedParameterInspection */
				$value, $data, $post
			) {
				$setting_details    = $this->get_setting_details( 'list' );
				$details            = [];
				$data['class_name'] = $this->get_setting_class_name( $data, $post );
				foreach ( array_keys( $this->get_setting_list() ) as $key ) {
					$setting = $this->app->array->get( $setting_details, $key );
					if ( empty( $setting ) ) {
						continue;
					}
					$is_default = $this->is_default( $data[ $key ] );
					if ( $is_default ) {
						$data[ $key ] = $setting['value'];
					}
					$details[ $setting['label'] ] = 'icon' === $key ? [
						'value'     => $data[ $key ],
						'form_type' => 'icon',
					] : $data[ $key ];
				}

				return $this->get_view( 'admin/custom_post/setting/detail', [
					'details' => $details,
				] );
			},
			'unescape' => true,
		];
	}

	/**
	 * @param string $key
	 *
	 * @return null|string
	 */
	protected function get_table_column_name( $key ) {
		if ( 'post_title' === $key ) {
			return $this->get_post_column_title();
		}

		return null;
	}

	/**
	 * @param int $post_id
	 * @param WP_Post $post
	 * @param array $old
	 * @param array $new
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function data_updated(
		/** @noinspection PhpUnusedParameterInspection */
		$post_id, WP_Post $post, array $old, array $new
	) {
		$this->clear_cache_file();
	}

	/**
	 * @param int $post_id
	 * @param WP_Post $post
	 * @param array $data
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function data_inserted(
		/** @noinspection PhpUnusedParameterInspection */
		$post_id, WP_Post $post, array $data
	) {
		$this->clear_cache_file();
	}

	/**
	 * @param int $post_id
	 * @param WP_Post $post
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function untrash_post(
		/** @noinspection PhpUnusedParameterInspection */
		$post_id, WP_Post $post
	) {
		$this->clear_cache_file();
	}

	/**
	 * @param int $post_id
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function trash_post(
		/** @noinspection PhpUnusedParameterInspection */ $post_id
	) {
		$this->clear_cache_file();
	}

	/**
	 * @param int $post_id
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function delete_misc(
		/** @noinspection PhpUnusedParameterInspection */
		$post_id
	) {
		$this->clear_cache_file();
	}

	/**
	 * clear options cache
	 */
	private function clear_cache_file() {
		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );
		$assets->clear_cache_file();
	}

	/**
	 * @param string $target
	 *
	 * @return array
	 */
	public function get_settings( $target ) {
		if ( ! isset( $this->cache_settings[ $target ] ) ) {
			$setting_details                 = $this->get_setting_details( $target );
			$priority_direction              = 'front' === $target ? 'DESC' : 'ASC';
			$group_name_direction            = 'front' === $target ? 'DESC' : 'ASC';
			$updated_at_direction            = 'front' === $target ? 'ASC' : 'DESC';
			$list_data                       = $this->get_list_data( function ( $query ) use ( $priority_direction, $updated_at_direction, $group_name_direction ) {
				/** @var Builder $query */
				$query->order_by( 'priority', $priority_direction )
					->order_by( 'updated_at', $updated_at_direction )
					->order_by( 'group_name', $group_name_direction );
			} );
			$this->cache_settings[ $target ] = $this->app->array->map( $this->app->array->get( $list_data, 'data' ), function ( $data ) use ( $setting_details, $target ) {
				return $this->data_to_setting( $data, $setting_details, $target );
			} );
		}

		return $this->cache_settings[ $target ];
	}

	/**
	 * @param array $data
	 * @param array $setting_details
	 * @param string $target
	 *
	 * @return array
	 */
	private function data_to_setting( $data, $setting_details, $target ) {
		$setting = [];
		foreach ( array_keys( $this->get_setting_list() ) as $key ) {
			$detail = $this->app->array->get( $setting_details, $key );
			if ( empty( $detail ) ) {
				continue;
			}

			$is_default                         = $this->is_default( $data[ $key ] );
			$detail['attributes']['data-value'] = $is_default ? $detail['value'] : $data[ $key ];
			list( $name, $value ) = $this->parse_setting( $detail, $key ); // phpcs:ignore Generic.Formatting.MultipleStatementAlignment.NotSameWarning
			$setting[ $name ] = $value; // phpcs:ignore Generic.Formatting.MultipleStatementAlignment.NotSameWarning
		}
		/** @var WP_Post $post */
		$post                  = $data['post'];
		$setting['class_name'] = $this->get_setting_class_name( $setting, $post );
		$setting['group_name'] = $this->get_setting_group_name( $setting, $post );
		$setting['title']      = $post->post_title;
		$setting['name']       = "setting-{$post->ID}";
		$setting['selector']   = $this->get_selector( $setting );
		$setting['is_valid']   = $setting['is_valid_toolbar_button'];
		if ( 'editor' === $target ) {
			foreach ( $setting as $key => $item ) {
				$new_key = $this->app->string->camel( $key );
				if ( $key !== $new_key ) {
					$setting[ $new_key ] = $item;
					unset( $setting[ $key ] );
				}
			}
		}

		return $setting;
	}

	/**
	 * @param array $setting
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	private function get_setting_class_name( array $setting, $post ) {
		return $this->apply_filters( 'class_name', empty( $setting['class_name'] ) ? $this->get_default_class_name( $post->ID ) : $setting['class_name'], $setting, $post );
	}

	/**
	 * @param int|string $post_id
	 *
	 * @return string
	 */
	public function get_default_class_name( $post_id ) {
		return $this->apply_filters( 'default_class_name', $this->get_default_class_name_prefix() . $post_id, $post_id );
	}

	/**
	 * @return string
	 */
	public function get_default_class_name_prefix() {
		return 'artb-';
	}

	/**
	 * @param array $setting
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	private function get_setting_group_name( array $setting, $post ) {
		if ( ! empty( $setting['group_name'] ) ) {
			return $this->apply_filters( 'group_name', $setting['group_name'], $setting, $post );
		}

		$group_name = $this->apply_filters( 'default_group' );
		if ( '' === (string) $group_name ) {
			$group_name = $this->get_default_group_name( $post );
		}

		return $this->apply_filters( 'group_name', $group_name, $setting, $post );
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	private function get_default_group_name( $post ) {
		return $this->apply_filters( 'default_group_name', $post->post_title . '-' . $post->ID, $post );
	}

	/**
	 * @param array $setting
	 *
	 * @return string
	 */
	private function get_selector( array $setting ) {
		$class_names = $this->app->string->explode( $setting['class_name'], ' ' );
		$class_names = '.' . implode( '.', $class_names );

		return $this->apply_filters( 'get_selector', $setting['tag_name'] . $class_names, $setting['tag_name'], $setting['class_name'], $setting );
	}

	/**
	 * @param array $setting
	 * @param string $key
	 *
	 * @return array
	 */
	private function parse_setting( array $setting, $key ) {
		$value = $setting['attributes']['data-value'];
		$name  = empty( $setting['attributes']['data-option_name'] ) ? $key : $setting['attributes']['data-option_name'];

		return [ $name, $value ];
	}

	/**
	 * @param array $errors
	 * @param array $post_array
	 *
	 * @return array
	 *
	 */
	protected function filter_validate_input(
		/** @noinspection PhpUnusedParameterInspection */
		array $errors, array $post_array
	) {
		/** @var Validation $validation */
		$validation = Validation::get_instance( $this->app );

		$class_name = trim( $this->get_post_field( 'class_name' ) );
		$class_name = preg_replace( '/\s{2,}/', ' ', $class_name );
		if ( '' !== $class_name ) {
			$priority = (int) $this->get_post_field( 'priority', 10 );
			$errors   = $validation->validate_class_name( $class_name, $priority, $post_array, $errors );
		}

		$tag_name = trim( $this->get_post_field( 'tag_name' ) );
		if ( '' !== $tag_name ) {
			$errors = $validation->validate_tag_name( $tag_name, $errors );
		}

		return $errors;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $default
	 * @param array|null $post_array
	 *
	 * @return mixed
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function filter_post_field(
		/** @noinspection PhpUnusedParameterInspection */
		$key, $value, $default, $post_array
	) {
		if ( is_string( $value ) ) {
			$value = trim( $value );
		}
		if ( 'style' === $key ) {
			/** @var Style $style */
			$style = Style::get_instance( $this->app );

			return $style->encode_style( $value );
		} elseif ( 'group_name' === $key ) {
			return preg_replace( '/[\x00-\x1F\x7F]/', '', $value );
		} elseif ( 'class_name' === $key ) {
			return preg_replace( '/\s{2,}/', ' ', $value );
		}

		return $value;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	protected function filter_item( array $data ) {
		/** @var Style $style */
		$style          = Style::get_instance( $this->app );
		$data['styles'] = $style->decode_style( $data['style'] );
		$data['style']  = $style->decode_style( $data['style'], true );

		return $data;
	}

	/**
	 * @return array
	 */
	private function get_groups() {
		$groups  = array_filter( $this->app->array->pluck_unique( $this->app->array->get( $this->get_list_data( null, false ), 'data', [] ), 'group_name' ), function ( $data ) {
			return isset( $data ) && '' !== $data;
		} );
		$default = $this->apply_filters( 'default_group' );
		if ( $default && ! in_array( $default, $groups, true ) ) {
			$groups[] = $default;
		}

		return $this->apply_filters( 'get_groups', $this->app->array->combine( $groups, null ) );
	}

	/**
	 * @return array
	 */
	private function get_preset() {
		$font_family = $this->apply_filters( 'is_valid_fontawesome' ) ? $this->app->get_config( 'config', 'fontawesome_font_family' ) : null;

		return $this->apply_filters( 'get_preset', array_filter( $this->app->array->map( $this->app->get_config( 'design_preset' ), function ( $preset ) use ( $font_family ) {
			if ( $this->is_closure( $preset ) ) {
				if ( ! $font_family ) {
					return null;
				}

				return $preset( $font_family );
			}

			return $preset;
		} ) ) );
	}

	/**
	 * @return array
	 */
	public function get_valid_post_types() {
		return $this->app->array->combine( array_intersect( get_post_types_by_support( 'editor' ), get_post_types( [
			'show_in_rest' => true,
		] ) ), null );
	}
}
