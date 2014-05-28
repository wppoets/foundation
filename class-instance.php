<?php namespace <%= php_namespace_base_classes %>;
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
abstract class Instance extends Static_Class {

	/**
	 * Initialization point for the configuration
	 * 
	 * @return void No return value
	 */
	static public function init_config() {
		parent::init_config();
		$current_instance = static::current_instance();
		$config = static::get_config_instance();
		$config::set_default( 'text_domain', '' );
		$config::set_default( 'asset_version', NULL );
		$config::set_default( 'base_url', '' );
		$config::set_default( 'base_scripts_url', '' );
		$config::set_default( 'base_styles_url', '' );
		$config::set_default( 'extension_js', '.js' );
		$config::set_default( 'extension_css', '.css' );
		$config::set_default( 'meta_key_prefix', '' );
		$config::set_default( 'option_key', '' );
		$config::set_default( 'option_autoload', FALSE );
		$config::set_default( 'id', '', $current_instance ); //Default is empty, this should always be set!
		$config::set_default( 'html_form_prefix', '', $current_instance ); // should only use [a-z0-9_-]
		$config::set_default( 'html_class_prefix', '', $current_instance ); // should only use [a-z0-9_-]
		$config::set_default( 'html_id_prefix', '', $current_instance ); // should only use [a-z0-9_-]
		$config::set_default( 'ajax_suffix', '', $current_instance ); // should only use [a-z0-9_-]
		$config::set_default( 'enable_ajax', FALSE, $current_instance );
		$config::set_default( 'enable_scripts', FALSE, $current_instance );
		$config::set_default( 'enable_styles', FALSE, $current_instance );
	}

	/**
	 * Init config check
	 * 
	 * @return void No return value
	 */
	static public function init_check_config( $settings = array() ) {
		parent::init_check_config( array_unique ( array_merge( $settings, array(
			'id',
			'base_url',
			'base_scripts_url',
			'base_styles_url',
		) ) ) );
	}

	/**
	 * Method for after init has completed
	 * 
	 * @return void No return value
	 */
	static public function init_done() {
		if ( static::get_config('enable_ajax') ) {
			$ajax_suffix = static::get_config('ajax_suffix');
			if ( ! empty( $ajax_suffix ) ) {
				add_action( 'wp_ajax_' . static::get_config('ajax_suffix'), array( static::current_instance(), 'action_wp_ajax' ) );
			}
		}
		if ( static::get_config('enable_scripts') ) {
			add_action( 'wp_enqueue_scripts', array( static::current_instance(), 'enqueue_scripts' ) );
		}
		if ( static::get_config('enable_styles') ) {
			add_action( 'wp_enqueue_scripts', array( static::current_instance(), 'enqueue_styles' ) );
		}
		//Check enable_js and enabled_css for auto adding!
	}

	/**
	 * WordPress action for an ajax call
	 * 
	 * @return void No return value
	 */
	static public function action_wp_ajax( $data = array() ) {
		print( json_encode( $data ) );
		die(); //The recomended method after processing the request
	}

	/**
	 * Method for enqueing scripts
	 *
	 * @param array $scripts An array containing the scripts to include
	 *
	 * @return void No return value
	 */
	static public function enqueue_scripts() {
		$scripts = array(); //TODO: pull the correct data and do seomthine!
		foreach ( (array) $scripts as $script_id => $script ) {
			$url = '';
			if ( ! empty( $script['url'] ) ) {
				$url = $script['url'];
			} else if ( ! empty( $script['ezurl'] ) ) {
				$url = static::get_config('base_scripts_url') . $script['ezurl'] . static::get_config('extension_js');
			}
			if ( ! empty( $url ) ) {
				$requires = empty( $script['requires'] ) ? NULL : $script['requires'];
				$version = empty( $script['version'] ) ? static::get_config('asset_version') : $script['version'];
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
	static public function enqueue_styles() {
		$styles = array();
		foreach ( (array) $styles as $style_id => $style ) {
			$url = '';
			if ( ! empty( $style['url'] ) ) {
				$url = $style['url'];
			} else if ( ! empty( $style['ezurl'] ) ) {
				$url = static::get_config('base_styles_url') . $style['ezurl'] . static::get_config('extension_css');
			}
			if ( ! empty( $url ) ) {
				$requires = empty( $style['requires'] ) ? NULL : $style['requires'];
				$version = empty( $style['version'] ) ? static::get_config('asset_version') : $style['version'];
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

	/**
	 * Get method for the wp options
	 *  
	 * @return array Returns the option array
	 */
	static public function get_option( $key = NULL ) {
		if ( empty ( $key ) ) {
			$key = static::get_config('option_key');
		}
		if ( empty( $key ) ) {
			return NULL;
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
			$key = static::get_config('option_key');
		}
		if ( empty( $key ) ) {
			return NULL;
		}
		if ( empty ( $autoload ) ) {
			$autoload = static::get_config('option_autoload');
		}
		$enable_autoload = $autoload ? 'yes' : 'no';
		$return_value = add_option( $key, $value, NULL, $enable_autoload );
		if ( ! $return_value ) {
			$return_value = update_option( $key, $value );
		}
		return $return_value;
	}

}
