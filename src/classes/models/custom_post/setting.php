<?php
/**
 * @version 1.0.14
 * @author technote-space
 * @since 1.0.0
 * @since 1.0.2 #25
 * @since 1.0.3 #28, #30, #33, #34
 * @since 1.0.5 #52
 * @since 1.0.7 #60
 * @since 1.0.10 trivial change
 * @since 1.0.12 #74
 * @since 1.0.14 #82, #87
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Richtext_Toolbar_Button\Classes\Models\Custom_Post;

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	exit;
}

/**
 * Class Setting
 * @package Richtext_Toolbar_Button\Classes\Models\Custom_Post
 */
class Setting implements \Richtext_Toolbar_Button\Interfaces\Models\Custom_Post {

	use \Richtext_Toolbar_Button\Traits\Models\Custom_Post;

	/**
	 * @var array $_cache_setting
	 */
	private $_cache_setting = [];

	/**
	 * @var array $_cache_settings
	 */
	private $_cache_settings = [];

	/**
	 * insert presets
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function insert_presets() {
		if ( $this->app->get_option( 'has_inserted_presets' ) ) {
			return;
		}
		$this->app->option->set( 'has_inserted_presets', true );

		foreach ( $this->apply_filters( 'get_setting_presets', $this->app->get_config( 'preset' ) ) as $item ) {
			$item['post_title'] = $this->translate( $this->app->utility->array_get( $item, 'name', $this->app->utility->array_get( $item, 'class_name', $this->app->utility->array_get( $item, 'tag_name', '' ) ) ) );
			unset( $item['name'] );
			! empty( $item['group_name'] ) and $item['group_name'] = $this->translate( $item['group_name'] );
			$this->insert( $item );
		}
	}

	/**
	 * setup assets
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_assets() {
		global $typenow;
		if ( empty( $typenow ) || $typenow !== $this->get_post_type() ) {
			return;
		}

		/** @var \Richtext_Toolbar_Button\Classes\Models\Assets $assets */
		$assets = \Richtext_Toolbar_Button\Classes\Models\Assets::get_instance( $this->app );
		$assets->enqueue_plugin_assets( null );
		$this->add_script_view( 'admin/script/custom_post/preview', [
			'css_handle'         => $assets->get_css_handle(),
			'fontawesome_handle' => $this->app->get_config( 'config', 'fontawesome_handle' ),
			'theme_style'        => get_template_directory_uri() . '/style.css',
			'chile_theme_style'  => is_child_theme() ? get_stylesheet_uri() : false,
		] );
		$this->add_style_view( 'admin/style/custom_post/preview', [
			'post_type' => $this->get_post_type(),
		] );
	}

	/**
	 * setup page
	 */
	public function setup_page() {
		$this->setup_fontawesome();
	}

	/**
	 * @param array $params
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	protected function filter_edit_form_params(
		/** @noinspection PhpUnusedParameterInspection */
		array $params, $post
	) {
		$setting_details = $this->get_setting_details( 'setting' );
		$columns         = [];
		foreach ( $setting_details as $key => $args ) {
			$column         = $this->app->utility->array_get( $params['columns'], $key, $this->app->utility->array_get( $args, 'detail', [] ) );
			$column['args'] = $args;
			unset( $column['args']['name'] );
			unset( $column['args']['value'] );
			unset( $column['args']['selected'] );
			unset( $column['args']['attributes']['checked'] );
			$column['args']['attributes']['data-default'] = $column['args']['attributes']['data-value'];
			$column['default']                            = $column['args']['attributes']['data-default'];
			if ( empty( $column['args']['attributes']['data-option_name'] ) ) {
				$column['args']['attributes']['data-option_name'] = $key;
			}
			$column['is_user_defined'] = true;
			empty( $column['type'] ) and $column['type'] = 'VARCHAR';
			! empty( $args['form_type'] ) and $column['form_type'] = $args['form_type'];
			empty( $column['form_type'] ) and $column['form_type'] = $this->get_form_by_type( $column['type'] );
			$columns[ $key ] = $column;
		}
		$params['columns']                                                     = $columns;
		$params['columns']['class_name']['args']['attributes']['data-default'] = $this->get_default_class_name( $post->ID );
		$params['columns']['class_name']['default']                            = $params['columns']['class_name']['args']['attributes']['data-default'];
		$params['columns']['exclude_post_types']['post_types']                 = $this->get_valid_post_types();
		$params['name_prefix']                                                 = $this->get_post_field_name_prefix();
		$params['groups']                                                      = $this->get_groups();

		$params['fontawesome_handle'] = $this->app->get_config( 'config', 'fontawesome_handle' );
		$params['theme_style']        = get_template_directory_uri() . '/style.css';
		$params['chile_theme_style']  = is_child_theme() ? get_stylesheet_uri() : false;

		return $params;
	}

	/**
	 * @param \WP_Post $post
	 * @param array $params
	 */
	protected function before_output_edit_form(
		/** @noinspection PhpUnusedParameterInspection */
		\WP_Post $post, array $params
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
					'target'    => [
						'setting',
						'front',
					],
					'form_type' => 'style',
					'preset'    => $this->get_preset(),
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
			'exclude_post_types'      => [
				'args' => [
					'form_type' => 'exclude_post_types',
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
			if ( is_array( $setting ) && ! empty( $setting['args']['target'] ) && ! in_array( $target, $setting['args']['target'] ) ) {
				continue;
			}
			$args[ $key ] = $this->get_setting( $key, $setting );
		}

		return $args;
	}

	/**
	 * @return string
	 */
	private function get_id_prefix() {
		return $this->app->slug_name . '-';
	}

	/**
	 * @param string $name
	 * @param string|array $setting
	 *
	 * @return array
	 */
	private function get_setting( $name, $setting ) {
		if ( ! isset( $this->_cache_setting[ $name ] ) ) {
			$columns = $this->app->db->get_columns( $this->get_related_table_name() );
			$detail  = $this->app->utility->array_get( is_array( $setting ) ? $setting : [], 'detail', $this->app->utility->array_get( $columns, $name, [] ) );
			$value   = $this->app->utility->array_get( is_array( $setting ) ? $setting : [], 'default', $this->app->utility->array_get( $detail, 'default' ) );
			$ret     = [
				'id'         => $this->get_id_prefix() . $name,
				'class'      => 'add-richtext-toolbar-option',
				'name'       => $this->get_post_field_name_prefix() . $name,
				'value'      => $value,
				'label'      => $this->translate( $this->app->utility->array_get( $detail, 'comment', $name ) ),
				'attributes' => [
					'data-value'   => $value,
					'data-default' => $value,
				],
				'detail'     => $detail,
				'type'       => $this->app->utility->parse_db_type( $this->app->utility->array_get( $detail, 'type' ) ),
			];
			if ( is_array( $setting ) ) {
				$ret = array_replace_recursive( $ret, isset( $setting['args'] ) && is_array( $setting['args'] ) ? $setting['args'] : [] );
			}
			if ( 'bool' === $ret['type'] ) {
				$ret['value'] = 1;
				! empty( $value ) and $ret['attributes']['checked'] = 'checked';
				$ret['label'] = $this->translate( 'Valid' );
			}
			$this->_cache_setting[ $name ] = $ret;
		}

		return $this->_cache_setting[ $name ];
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
			'preview'                 => [
				'name'     => $this->translate( 'preview' ),
				'callback' => function (
					/** @noinspection PhpUnusedParameterInspection */
					$value, $data, $post
				) {
					$setting_details    = $this->get_setting_details( 'list' );
					$data['class_name'] = $this->_get_class_name( $data, $post );
					foreach ( [ 'class_name', 'tag_name' ] as $key ) {
						$setting = $this->app->utility->array_get( $setting_details, $key );
						if ( empty( $setting ) ) {
							continue;
						}
						$is_default = ! is_array( $data[ $key ] ) && '' === (string) ( $data[ $key ] );
						$is_default and $data[ $key ] = $setting['value'];
						$details[ $setting['label'] ] = $data[ $key ];
					}

					return $this->get_view( 'admin/custom_post/setting/preview', [
						'class_name' => $data['class_name'],
						'tag_name'   => $data['tag_name'],
					] );
				},
				'unescape' => true,
			],
			'display'                 => [
				'name'     => $this->translate( 'display' ),
				'callback' => function (
					/** @noinspection PhpUnusedParameterInspection */
					$value, $data, $post
				) {
					$setting_details    = $this->get_setting_details( 'list' );
					$details            = [];
					$data['class_name'] = $this->_get_class_name( $data, $post );
					foreach ( $this->get_setting_list() as $key => $item ) {
						$setting = $this->app->utility->array_get( $setting_details, $key );
						if ( empty( $setting ) ) {
							continue;
						}
						$is_default = ! is_array( $data[ $key ] ) && '' === (string) ( $data[ $key ] );
						$is_default and $data[ $key ] = $setting['value'];
						if ( 'exclude_post_types' === $key ) {
							$details[ $setting['label'] ] = implode( ', ', $data[ $key ] );
						} elseif ( 'icon' === $key ) {
							$details[ $setting['label'] ] = [
								'value'     => $data[ $key ],
								'form_type' => 'icon',
							];
						} else {
							$details[ $setting['label'] ] = $data[ $key ];
						}
					}

					return $this->get_view( 'admin/custom_post/setting/detail', [
						'details' => $details,
					] );
				},
				'unescape' => true,
			],
			'is_valid_toolbar_button' => [
				'name'                  => $this->translate( 'validity of toolbar button' ),
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
	 * @param string $key
	 *
	 * @return null|string
	 */
	protected function get_table_column_name( $key ) {
		if ( $key === 'post_title' ) {
			return $this->get_post_column_title();
		}

		return null;
	}

	/**
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param array $old
	 * @param array $new
	 */
	public function data_updated( $post_id, \WP_Post $post, array $old, array $new ) {
		$this->clear_cache_file();
	}

	/**
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param array $data
	 */
	public function data_inserted( $post_id, \WP_Post $post, array $data ) {
		$this->clear_cache_file();
	}

	/**
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function untrash_post( $post_id, \WP_Post $post ) {
		$this->clear_cache_file();
	}

	/**
	 * @param int $post_id
	 */
	public function trash_post( $post_id ) {
		$this->clear_cache_file();
	}

	/**
	 * @param int $post_id
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
		/** @var \Richtext_Toolbar_Button\Classes\Models\Assets $assets */
		$assets = \Richtext_Toolbar_Button\Classes\Models\Assets::get_instance( $this->app );
		$assets->clear_cache_file();
	}

	/**
	 * @param string $target
	 * @param string|null $post_type
	 *
	 * @return array
	 */
	public function get_settings( $target, $post_type = null ) {
		if ( ! isset( $this->_cache_settings[ $target ][ $post_type ] ) ) {
			$setting_details      = $this->get_setting_details( $target );
			$settings             = $this->get_default_buttons( $target );
			$priority_direction   = 'front' === $target ? 'DESC' : 'ASC';
			$group_name_direction = 'front' === $target ? 'DESC' : 'ASC';
			$updated_at_direction = 'front' === $target ? 'ASC' : 'DESC';
			foreach (
				$this->list_data( true, null, 1, null, [
					'priority'   => $priority_direction,
					'updated_at' => $updated_at_direction,
					'group_name' => $group_name_direction,
				] )['data'] as $data
			) {
				if ( ! empty( $post_type ) && in_array( $post_type, $data['exclude_post_types'] ) ) {
					continue;
				}
				$options = [];
				foreach ( $this->get_setting_list() as $key => $item ) {
					$setting = $this->app->utility->array_get( $setting_details, $key );
					if ( empty( $setting ) ) {
						continue;
					}

					$is_default                          = ! is_array( $data[ $key ] ) && '' === (string) ( $data[ $key ] );
					$setting['attributes']['data-value'] = $is_default ? $setting['value'] : $data[ $key ];
					list( $name, $value ) = $this->parse_setting( $setting, $key );
					$options[ $name ] = $value;
				}
				/** @var \WP_Post $post */
				$post                  = $data['post'];
				$options['class_name'] = $this->_get_class_name( $options, $post );
				$options['title']      = $post->post_title;
				$options['group_name'] = $this->_get_group_name( $options, $post );
				$options['selector']   = $this->get_selector( $options );
				$settings[]            = [
					'id'      => $post->ID,
					'options' => $options,
					'title'   => $post->post_title,
					'hide'    => ! $options['is_valid_toolbar_button'],
				];
			}
			$this->_cache_settings[ $target ][ $post_type ] = $settings;
		}

		return $this->_cache_settings[ $target ][ $post_type ];
	}

	/**
	 * @param string $target
	 *
	 * @return array
	 */
	private function get_default_buttons( $target ) {
		$settings = [];
		if ( 'editor' === $target ) {
			$settings[] = [
				'id'      => 'font-color',
				'options' => [
					'class_name' => $this->get_default_class_name( 'font-color' ),
					'icon'       => $this->apply_filters( 'font_color_icon' ),
				],
				'title'   => $this->translate( 'font color' ),
				'style'   => 'color',
				'hide'    => ! $this->apply_filters( 'is_valid_font_color' ),
			];
			$settings[] = [
				'id'      => 'background-color',
				'options' => [
					'class_name' => $this->get_default_class_name( 'background-color' ),
					'icon'       => $this->apply_filters( 'background_color_icon' ),
				],
				'title'   => $this->translate( 'background color' ),
				'style'   => 'background-color',
				'hide'    => ! $this->apply_filters( 'is_valid_background_color' ),
			];
			$settings[] = [
				'id'      => 'font-size',
				'options' => [
					'class_name' => $this->get_default_class_name( 'font-size' ),
					'icon'       => $this->apply_filters( 'font_size_icon' ),
				],
				'title'   => $this->translate( 'font size' ),
				'style'   => 'font-size',
				'hide'    => ! $this->apply_filters( 'is_valid_font_size' ),
			];
			$settings   = $this->apply_filters( 'get_default_buttons', $settings );
		}

		return $settings;
	}

	/**
	 * @param array $options
	 * @param \WP_Post $post
	 *
	 * @return string
	 */
	private function _get_class_name( array $options, $post ) {
		if ( ! empty( $options['class_name'] ) ) {
			$class_name = $options['class_name'];
		} else {
			$class_name = $this->get_default_class_name( $post->ID );
		}

		return $this->apply_filters( 'class_name', $class_name, $options, $post );
	}

	/**
	 * @param int|string $post_id
	 *
	 * @return string
	 */
	private function get_default_class_name( $post_id ) {
		return $this->apply_filters( 'default_class_name', $this->get_default_class_name_prefix() . $post_id, $post_id );
	}

	/**
	 * @return string
	 */
	private function get_default_class_name_prefix() {
		return 'artb-';
	}

	/**
	 * @param array $options
	 * @param \WP_Post $post
	 *
	 * @return string
	 */
	private function _get_group_name( array $options, $post ) {
		if ( ! empty( $options['group_name'] ) ) {
			$group_name = $options['group_name'];
		} else {
			$group_name = $this->apply_filters( 'default_group' );
			if ( '' === (string) $group_name ) {
				$group_name = $this->get_default_group_name( $post );
			}
		}

		return $this->apply_filters( 'group_name', $group_name, $options, $post );
	}

	/**
	 * @param \WP_Post $post
	 *
	 * @return string
	 */
	private function get_default_group_name( $post ) {
		return $this->apply_filters( 'default_group_name', $post->post_title . '-' . $post->ID, $post );
	}

	/**
	 * @param array $options
	 *
	 * @return string
	 */
	private function get_selector( array $options ) {
		$class_names = $this->app->utility->explode( $options['class_name'], ' ' );
		$class_names = '.' . implode( '.', $class_names );

		return $this->apply_filters( 'get_selector', $options['tag_name'] . $class_names, $options['tag_name'], $options['class_name'], $options );
	}

	/**
	 * @param array $setting
	 * @param string $key
	 *
	 * @return array
	 */
	private function parse_setting( array $setting, $key ) {
		$value = $setting['attributes']['data-value'];
		if ( ! empty( $setting['attributes']['data-option_name'] ) ) {
			$name = $setting['attributes']['data-option_name'];
		} else {
			$name = $key;
		}

		return [ $name, $value ];
	}

	/**
	 * @param array $errors
	 * @param array $post_array
	 *
	 * @return array
	 */
	protected function filter_validate_input(
		/** @noinspection PhpUnusedParameterInspection */
		array $errors, array $post_array
	) {
		$class_name = trim( $this->get_post_field( 'class_name' ) );
		$class_name = preg_replace( '/\s{2,}/', ' ', $class_name );
		if ( '' !== $class_name ) {
			if ( preg_match( '/\A' . preg_quote( $this->get_default_class_name_prefix(), '/' ) . '/', $class_name ) ) {
				$errors['class_name'][] = $this->translate( 'The value is unusable.' );
			} elseif ( ! preg_match( '/\A([_a-zA-Z]+[a-zA-Z0-9-]*)(\s+[_a-zA-Z]+[_a-zA-Z0-9-]*)*\z/', $class_name ) ) {
				$errors['class_name'][] = $this->translate( 'Invalid format.' );
				$errors['class_name'][] = $this->translate( 'A class name must begin with a letter, followed by any number of hyphens, letters, or numbers.' );
			} else {
				! isset( $post_array['ID'] ) and $post_array['ID'] = - 1;
				if ( $this->app->db->select_count( $this->get_related_table_name(), '*', [
						'post_id'    => [ '<>', $post_array['ID'] ],
						'class_name' => $class_name,
					] ) > 0 ) {
					$errors['class_name'][] = $this->translate( 'The value has already been used.' );
				} else {
					// この時点で $class_name は 英数及びアンダーバー、ハイフン、スーペースのみ
					$priority = $this->get_post_field( 'priority', 10 ) - 0;
					$replace  = " {$class_name} ";
					if ( $this->app->db->select_count( $this->get_related_table_name(), '*', [
							'post_id'              => [ '<>', $post_array['ID'] ],
							"LENGTH('{$replace}')" => [ '<>', "LENGTH(REPLACE('{$replace}', CONCAT(' ', class_name, ' '), ''))", true ],
							'priority'             => [ '<=', $priority ],
						] ) > 0 ) {
						$errors['class_name'][] = $this->translate( 'The value is included in the class name of other settings.' );
					}
				}
			}
		}

		$tag_name = trim( $this->get_post_field( 'tag_name' ) );
		if ( '' !== $tag_name ) {
			if ( 'div' === strtolower( $tag_name ) ) {
				$errors['tag_name'][] = $this->translate( 'This tag name is unusable.' );
			} elseif ( ! preg_match( '/\A[a-zA-Z]+\z/', $tag_name ) ) {
				$errors['tag_name'][] = $this->translate( 'Invalid format.' );
			}
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
	 */
	protected function filter_post_field(
		/** @noinspection PhpUnusedParameterInspection */
		$key, $value, $default, $post_array
	) {
		is_string( $value ) and $value = trim( $value );
		if ( 'style' === $key ) {
			return $this->encode_style( $value );
		} elseif ( 'exclude_post_types' === $key ) {
			return $this->encode_exclude_post_types( $value );
		} elseif ( 'group_name' === $key ) {
			return preg_replace( '/[\x00-\x1F\x7F]/', '', $value );
		} elseif ( 'class_name' === $key ) {
			return preg_replace( '/\s{2,}/', ' ', $value );
		}

		return $value;
	}

	/**
	 * @param array $d
	 *
	 * @return array
	 */
	protected function filter_item( array $d ) {
		$d['styles']             = $this->decode_style( $d['style'] );
		$d['style']              = $this->decode_style( $d['style'], true );
		$d['exclude_post_types'] = $this->decode_exclude_post_types( $d['exclude_post_types'] );

		return $d;
	}

	/**
	 * @param mixed $value
	 *
	 * @return string
	 */
	private function encode_exclude_post_types( $value ) {
		! is_array( $value ) and $value = [];

		return json_encode( $value );
	}

	/**
	 * @param string $value
	 *
	 * @return array
	 */
	private function decode_exclude_post_types( $value ) {
		$value = @json_decode( $value, true );
		! is_array( $value ) and $value = [];

		return $value;
	}

	/**
	 * @param mixed $style
	 *
	 * @return string
	 */
	private function encode_style( $style ) {
		! is_string( $style ) and $style = '';
		$style  = trim( stripslashes( $style ) );
		$styles = [];
		$index  = [];

		foreach ( preg_split( "/\R|;/", $style ) as $k => $v ) {
			if ( ! preg_match( '/^(\[([-().#>+~|*a-z]+)]\s*)?(.+?)\s*:\s*(.+?)\s*$/', $v, $matches ) ) {
				continue;
			}

			$pseudo = trim( $matches[2] );
			$key    = trim( $matches[3] );
			$val    = trim( $matches[4] );
			if ( ! preg_match( '/\A[a-z-]+\z/i', $key ) ) {
				continue;
			}

			$value                      = "{$key}: {$val};";
			$index[ $pseudo ][ $value ] = $k;
			$styles[ $pseudo ][ $k ]    = $value;
		}

		ksort( $styles );
		foreach ( $styles as $pseudo => $values ) {
			foreach ( $values as $k => $value ) {
				if ( $index[ $pseudo ][ $value ] !== $k ) {
					unset( $styles[ $pseudo ][ $k ] );
				}
			}
			$styles[ $pseudo ] = array_values( $styles[ $pseudo ] );
		}

		return json_encode( $styles );
	}

	/**
	 * @param string $style
	 * @param bool $editor
	 *
	 * @return array|string
	 */
	private function decode_style( $style, $editor = false ) {
		$styles = @json_decode( $style, true );
		! is_array( $styles ) and $styles = [];

		if ( ! $editor ) {
			return $styles;
		}

		$items = [];
		foreach ( $styles as $pseudo => $values ) {
			foreach ( $values as $k => $value ) {
				$items[] = '' === $pseudo ? $value : "[{$pseudo}] {$value}";
			}
			$items[] = '';
		}

		return implode( "\r\n", $items );
	}

	/**
	 * @return array
	 */
	private function get_groups() {
		$groups  = array_filter( $this->app->utility->array_pluck_unique( $this->app->utility->array_get( $this->list_data(), 'data', [] ), 'group_name' ), function ( $d ) {
			return '' !== $d;
		} );
		$default = $this->apply_filters( 'default_group' );
		if ( $default && ! in_array( $default, $groups ) ) {
			$groups[] = $default;
		}

		return $this->apply_filters( 'get_groups', $this->app->utility->array_combine( $groups, null ) );
	}

	/**
	 * @return array
	 */
	private function get_preset() {
		$font_family = $this->app->get_config( 'config', 'fontawesome_font_family' );

		return $this->apply_filters( 'get_preset', [
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
			'block'            => 'display: block;',
			'inline block'     => 'display: inline-block;',
			'icon'             => [
				'display: block;',
				'padding: 10px;',
				'background: #f0f9ff;',
				'border: 1px solid #acf;',
				"[before] font-family: {$font_family};",
				'[before] content: "\f06a";',
				'[before] font-size: 1.2em;',
				'[before] font-weight: 900;',
				'[before] padding-right: .2em;',
				'[before] margin-right: .2em;',
				'[before] color: #9cf;',
				'[before] border-right: 1px solid #acf;',
				'[before] vertical-align: middle;',
			],
			'tab'              => [
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
			],
			'tag'              => [
				'display: block;',
				'border-left: 6px solid #06c;',
				'padding: 1.2em 1em;',
				'background-color: #def;',
			],
			'label'            => [
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
			],
			'warning'          => [
				'display: block;',
				'position: relative;',
				'padding: 1em;',
				'background-color: #fbeaea;',
				'border-width: 0 0 0 5px;',
				'border-style: solid;',
				'border-color: #dc3232;',
				"[before] font-family: {$font_family};",
				'[before] content: "\f057";',
				'[before] color: #dc3232;',
				'[before] font-weight: 900;',
				'[before] font-size: 2em;',
				'[before] vertical-align: middle;',
				'[before] padding-right: .3em;',
			],
		] );
	}

	/**
	 * @return array
	 */
	public function get_valid_post_types() {
		return $this->app->utility->array_combine( array_intersect( get_post_types_by_support( 'editor' ), get_post_types( [
			'show_in_rest' => true,
		] ) ), null );
	}
}
