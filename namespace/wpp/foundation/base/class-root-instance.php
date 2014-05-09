<?php namespace WPP\Foundation\Base;
/**
 * Copyright (c) 2014, WP Poets and/or its affiliates <wppoets@gmail.com>
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
/**
 * @author Michael Stutz <michaeljstutz@gmail.com>
 */
abstract class Root_Instance extends Static_Instance {

	/** Used as the ID of the instance */
	const ID = 'wpp-root-instance';

	/** Used as the default option key */
	const DEFAULT_OPTION_KEY = 'wpp-root-instance';

	/** Used as the default option key */
	const DEFAULT_OPTION_AUTOLOAD = FALSE;

	/** Used to enable admin controllers */
	const ENABLE_ADMIN_CONTROLLERS = FALSE;

	/** Used to enable content types */
	const ENABLE_CONTENT_TYPES = FALSE;

	/** Used to enable meta boxes */
	const ENABLE_META_BOXES = FALSE;

	/** Used to enable shortcodes */
	const ENABLE_SHORTCODES = FALSE;

	/**
	 * Set method for the options
	 *  
	 * @param string|array $config An array containing the config
	 * @param boolean $merge Should the current config be merged in?
	 * 
	 * @return void No return value
	 */
	static public function set_config( $config, $merge = FALSE ) {
		return parent::set_config( static::array_merge_nested(
			array( //Default config
				'admin_controllers' => array(),
				'admin_controller_configs' => array(),
				'content_types' => array(),
				'content_type_configs' => array(),
				'meta_boxes' => array(),
				'meta_box_configs' => array(),
				'shortcodes' => array(),
				'shortcode_configs' => array(),
				'base_urls' => array(
					'base' => '',
					'scripts' => '',
					'styles' => '',
				),
				'metadata_keys' => array(
					'prefix' => '_' . str_replace('-', '_', static::ID),
				),
			),
			(array) $config //Added options
		) );
	}

	/**
	 * Method called before initialized is set to true
	 * 
	 * @return void No return value
	 */
	static public function init_before_initialized() {
		parent::init_before_initialized();
		if ( static::ENABLE_SHORTCODES ) {
			static::init_shortcodes();
		}
		if ( static::ENABLE_CONTENT_TYPES ) {
			static::init_content_types();
		}
		if ( is_admin() ) {
			if ( static::ENABLE_ADMIN_CONTROLLERS ) {
				static::init_admin_controllers();
			}
			if ( static::ENABLE_META_BOXES ) {
				static::init_meta_boxes();
			}
		}
	}

	/**
	 * Init method for an array of classes
	 * 
	 * @return void No return value
	 */
	static public function init_class_array( $classes, $configs = array() ) {
		foreach ( $classes as $class ) {
			$config = empty( $configs[ $class ] ) ? array() : $configs[ $class ];
			static::init_static_class( $class, $config );
			unset( $config );
		}
	}

	/**
	 * Init method for the admin controllers
	 * 
	 * The method loops through the preconfigured admin_controllers 
	 * array
	 * 
	 * @return void No return value
	 */
	static public function init_admin_controllers() {
		static::init_class_array( static::admin_controllers(), static::admin_controller_configs() );
	}

	/**
	 * Init method for the content types
	 * 
	 * The method loops through the preconfigured content_types 
	 * array
	 *
	 * @return void No return value
	 */
	static public function init_content_types() {
		static::init_class_array( static::content_types(), static::content_type_configs() );
	}

	/**
	 * Init method for the meta boxes
	 * 
	 * The method loops through the preconfigured meta_boxes 
	 * array
	 *
	 * @return void No return value
	 */
	static public function init_meta_boxes() {
		static::init_class_array( static::meta_boxes(), static::meta_box_configs() );
	}

	/**
	 * Init method for the shortcodes
	 * 
	 * The method loops through the preconfigured shortcodes 
	 * array
	 *
	 * @return void No return value
	 */
	static public function init_shortcodes() {
		static::init_class_array( static::shortcodes(), static::shortcode_configs() );
	}

	/**
	 * Init method for a static class
	 * 
	 * The method loops through the preconfigured admin_controllers 
	 * array set in the plugin options, then 
	 * 
	 * @return void No return value
	 */
	static public function init_static_class( $class, $config = array() ) {
		//static::debug( __METHOD__, array( $class, $config ) );
		if ( class_exists( $class ) && method_exists( $class, 'init' ) ) {
			$class_config = array(
				'root_instance' => static::current_instance(),
				'text_domain' => static::get_text_domain(),
			);
			if ( ! empty( $config ) ) {
				$class_config = static::array_merge_nested( $class_config, $config );
			}
			$class::init( $class_config );
			unset( $class_config );
		} else {
			static::error( __METHOD__, "Static class ( $class ) did not exists and or have the required init method", E_USER_WARNING );
		}
	}

	/**
	 * Get method for the wp options
	 *  
	 * @return array Returns the option array
	 */
	static public function get_default_option_key() {
		return static::DEFAULT_OPTION_KEY;
	}

	/**
	 * Get method for the wp options
	 *  
	 * @return array Returns the option array
	 */
	static public function get_option( $key = NULL ) {
		if ( empty ( $key ) ) {
			$key = static::DEFAULT_OPTION_KEY;
		}
		return get_option( $key );
	}

	/**
	 * Set method for the wp options
	 *  
	 * @return array Returns the option array
	 */
	static public function set_option( $value, $key = NULL, $autoload = NULL ) {
		if ( empty ( $key ) ) {
			$key = static::DEFAULT_OPTION_KEY;
		}
		if ( empty ( $autoload ) ) {
			$key = static::DEFAULT_OPTION_AUTOLOAD;
		}
		$enable_autoload = $autoload ? 'yes' : 'no';
		$return_value = add_option( $key, $value, NULL, $enable_autoload );
		if ( ! $return_value ) {
			$return_value = update_option( $key, $value );
		}
		return $return_value;
	}

	/**
	 * Method for the metadata key prefix
	 *  
	 * @return string Returns the metadata key prefix
	 */
	static public function get_metadata_key_prefix() {
		$config = static::get_config();
		return isset( $config[ 'metadata_keys' ][ 'prefix' ] ) ? $config[ 'metadata_keys' ][ 'prefix' ] : '';
	}

	/**
	 * Method for the metadata key
	 *  
	 * @return string Returns the metadata key
	 */
	static public function get_metadata_key( $key ) {
		$config = static::get_config();
		return isset( $config[ 'metadata_keys' ][ $key ] ) ? static::get_metadata_key_prefix() . $config[ 'metadata_keys' ][ $key ] : NULL;
	}

	/**
	 * Method for the base_url from key
	 *  
	 * @return string Returns the base url
	 */
	static public function get_base_url( $key = '' ) {
		$config = static::get_config();
		if ( empty( $key ) ) {
			return isset( $config[ 'base_urls' ][ 'base' ] ) ? $config[ 'base_urls' ][ 'base' ] : '';
		}
		return isset( $config[ 'base_urls' ][ $key ] ) ? $config[ 'base_urls' ][ $key ] : '';
	}

	/**
	 * Method for the extention based on key
	 *  
	 * @return string Returns the base url
	 */
	static public function get_extention( $key ) {
		$config = static::get_config();
		return isset( $config[ 'extentions' ][ $key ] ) ? $config[ 'extentions' ][ $key ] : '.' . $key;
	}

	/**
	 * Method for the admin_controllers
	 *  
	 * @return array Returns the admin controllers
	 */
	static public function admin_controllers() {
		$config = static::get_config();
		return (array) $config[ 'admin_controllers' ];
	}

	/**
	 * Method for the admin_controller_configs
	 *  
	 * @return array Returns the admin control configs
	 */
	static public function admin_controller_configs() {
		$config = static::get_config();
		return (array) $config[ 'admin_controller_configs' ];
	}


	/**
	 * Method for the content_types
	 *  
	 * @return array Returns the admin controllers
	 */
	static public function content_types() {
		$config = static::get_config();
		return (array) $config[ 'content_types' ];
	}

	/**
	 * Method for the content_type_configs
	 *  
	 * @return array Returns the admin control configs
	 */
	static public function content_type_configs() {
		$config = static::get_config();
		return (array) $config[ 'content_type_configs' ];
	}


	/**
	 * Method for the meta_boxes
	 *  
	 * @return array Returns the admin controllers
	 */
	static public function meta_boxes() {
		$config = static::get_config();
		return (array) $config[ 'meta_boxes' ];
	}

	/**
	 * Method for the meta_box_configs
	 *  
	 * @return array Returns the admin control configs
	 */
	static public function meta_box_configs() {
		$config = static::get_config();
		return (array) $config[ 'meta_box_configs' ];
	}


	/**
	 * Method for the shortcodes
	 *  
	 * @return array Returns the admin controllers
	 */
	static public function shortcodes() {
		$config = static::get_config();
		return (array) $config[ 'shortcodes' ];
	}

	/**
	 * Method for the shortcode_configs
	 *  
	 * @return array Returns the admin control configs
	 */
	static public function shortcode_configs() {
		$config = static::get_config();
		return (array) $config[ 'shortcode_configs' ];
	}

	/**
	 * Method for enqueing scripts
	 *
	 * @param array $scripts An array containing the scripts to include
	 *
	 * @return void No return value
	 */
	static public function enqueue_scripts( $scripts = array() ) {
		foreach ( (array) $scripts as $script_id => $script ) {
			$url = '';
			if ( ! empty( $style['url'] ) ) {
				$url = $style['url'];
			} else if ( ! empty( $style['ezurl'] ) ) {
				$url = static::get_base_url( 'scripts' ) . $style['ezurl'] . static::get_extention('js');
			}
			if ( ! empty( $url ) ) {
				$requires = empty( $script['requires'] ) ? NULL : $script['requires'];
				$version = empty( $script['version'] ) ? static::get_asset_version() : $script['version'];
				if ( ! is_admin() && ! empty( $script['replace_existing'] ) ) { //Wordpress has checks for removing things in the admin so not going to bother
					wp_deregister_script( $script_id );
				} 
				if ( ! wp_script_is( $script_id, 'registered' ) ) {
					wp_register_script( $script_id, $url, $requires, $version );
				}
				unset( $requires, $version );
			} else {
				unset( $scripts[ $script_id ] ); //No url was given so remomving it from the list
			}
		}
		foreach ( (array) $scripts as $script_id => $script ) {
			if ( ! wp_script_is( $script_id, 'enqueued' ) ) {
				wp_enqueue_script( $script_id );
			}
		}
	}

	/**
	 * Method for enqueing styles
	 *
	 * @param array $scripts An array containing the styles to include
	 *
	 * @return void No return value
	 */
	static public function enqueue_styles( $styles = array() ) {
		foreach ( (array) $styles as $style_id => $style ) {
			$url = '';
			if ( ! empty( $style['url'] ) ) {
				$url = $style['url'];
			} else if ( ! empty( $style['ezurl'] ) ) {
				$url = static::get_base_url( 'styles' ) . $style['ezurl'] . static::get_extention('css');
			}
			if ( ! empty( $url ) ) {
				$requires = empty( $style['requires'] ) ? NULL : $style['requires'];
				$version = empty( $style['version'] ) ? static::get_asset_version() : $style['version'];
				if ( ! is_admin() && ! empty( $style['replace_existing'] ) ) { //Wordpress has checks for removing things in the admin so not going to bother
					wp_deregister_style( $style_id );
				} 
				if ( ! wp_style_is( $style_id, 'registered' ) ) {
					wp_register_style( $style_id, $url, $requires, $version );
				}
				unset( $requires, $version );
			} else {
				unset( $scripts[ $style_id ] ); //No url was given so remomving it from the list
			}
			unset( $url );
		}
		foreach ( (array) $styles as $style_id => $style ) {
			if ( ! wp_style_is( $style_id, 'enqueued' ) ) {
				wp_enqueue_style( $style_id );
			}
		}
	}

}