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
		//Global config
		static::set_default_config( 'text_domain', '', TRUE );
		static::set_default_config( 'asset_version', NULL, TRUE );
		static::set_default_config( 'base_url', '', TRUE );
		static::set_default_config( 'base_scripts_url', '', TRUE );
		static::set_default_config( 'base_styles_url', '', TRUE );
		static::set_default_config( 'extension_js', '.js', TRUE );
		static::set_default_config( 'extension_css', '.css', TRUE );
		static::set_default_config( 'meta_key_prefix', '', TRUE );
		static::set_default_config( 'option_key', '', TRUE );
		static::set_default_config( 'option_autoload', FALSE, TRUE );
		//Local config
		static::set_default_config( 'id', '' ); //Default is empty, this should always be set!
		static::set_default_config( 'html_form_prefix', '' ); // should only use [a-z0-9_-]
		static::set_default_config( 'html_class_prefix', '' ); // should only use [a-z0-9_-]
		static::set_default_config( 'html_id_prefix', '' ); // should only use [a-z0-9_-]
		static::set_default_config( 'ajax_suffix', '' ); // should only use [a-z0-9_-]
		static::set_default_config( 'enable_ajax', FALSE );
		static::set_default_config( 'enable_scripts', FALSE );
		static::set_default_config( 'enable_styles', FALSE );
		static::set_default_config( 'scripts', array() );
		static::set_default_config( 'styles', array() );
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
		$items = static::get_config( 'scripts' );
		foreach ( (array) $items as $key => $array_value ) {
			$item = &$items[ $key ];
			if ( empty( $item['url'] ) && ! empty( $item['ezurl'] ) ) {
				$item['url'] = static::get_config('base_scripts_url') . $item['ezurl'] . static::get_config('extension_js');
			}
			if ( empty( $item['url'] ) ) {
				unset( $item, $items[ $key ] ); //No url was given so remomving it from the list
			}
			$item[ 'id' ] = empty( $item[ 'id' ] ) ? md5( $item['url'] ) : $item[ 'id' ];
			$item['requires'] = empty( $item['requires'] ) ? NULL : $item['requires'];
			$item['version'] = empty( $item['version'] ) ? static::get_config('asset_version') : $item['version'];
			if ( ! is_admin() && ! empty( $item['replace_existing'] ) ) { //Wordpress has checks for removing things in the admin so not going to bother
				wp_deregister_script( $item[ 'id' ] );
			} 
			if ( ! wp_script_is( $item['id'], 'registered' ) ) {
				wp_register_script( $item['id'], $item['url'], $item['requires'], $item['version'] );
			}
		}
		foreach ( (array) $items as $item ) {
			if ( ! wp_script_is( $item[ 'id' ], 'enqueued' ) ) {
				wp_enqueue_script( $item[ 'id' ] );
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
		$items = static::get_config( 'styles' );
		foreach ( (array) $items as $key => $array_value ) {
			$item = &$items[ $key ];
			if ( empty( $item['url'] ) && ! empty( $item['ezurl'] ) ) {
				$item['url'] = static::get_config('base_styles_url') . $item['ezurl'] . static::get_config('extension_css');
			}
			if ( empty( $item['url'] ) ) {
				unset( $item, $items[ $key ] ); //No url was given so remomving it from the list
			}
			$item[ 'id' ] = empty( $item[ 'id' ] ) ? md5( $item['url'] ) : $item[ 'id' ];
			$item['requires'] = empty( $item['requires'] ) ? NULL : $item['requires'];
			$item['version'] = empty( $item['version'] ) ? static::get_config('asset_version') : $item['version'];
			if ( ! is_admin() && ! empty( $item['replace_existing'] ) ) { //Wordpress has checks for removing things in the admin so not going to bother
				wp_deregister_style( $item[ 'id' ] );
			} 
			if ( ! wp_style_is( $item['id'], 'registered' ) ) {
				wp_register_style( $item['id'], $item['url'], $item['requires'], $item['version'] );
			}
		}
		foreach ( (array) $items as $item ) {
			if ( ! wp_style_is( $item[ 'id' ], 'enqueued' ) ) {
				wp_enqueue_style( $item[ 'id' ] );
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
