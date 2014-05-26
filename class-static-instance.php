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
abstract class Static_Instance extends Static_Class {

	/** Used as the ID of the instance */
	const ID = 'wpp-static-instance';

	/** Used to enable ajax callbacks */
	const ENABLE_AJAX = FALSE;

	/** Used to store the class configuration */
	static private $_config = array();
	
	/**
	 * Initialization point for the static class
	 * 
	 * @return void No return value
	 */
	static public function init( $config = array() ) {
		if ( static::is_initialized() ) { 
			return; 
		}
		parent::init();
		static::set_config( $config );

		//$config::set_default('text_domain', '');
		//$config::set_default('asset_version', '');
		//$config::set_default('ajax_suffix', '', $instance);
		//$config::set_default('scripts', array(), $instance);
		//$config::set_default('styles', array(), $instance);
		
		static::init_before_initialized();
		static::init_after_initialized();
	}

	/**
	 * Method called before initialized is set to true
	 * 
	 * @return void No return value
	 */
	static public function init_before_initialized() {
		if ( static::ENABLE_AJAX ) {
			add_action( 'wp_ajax_' . static::get_ajax_suffix(), array( static::current_instance(), 'action_wp_ajax' ) );
		}
	}

	/**
	 * Method called after initialized is set to true
	 * 
	 * @return void No return value
	 */
	static public function init_after_initialized() {
		// Holder
	}


	/**
	 * Set method for the options
	 *  
	 * @param string|array $config An array containing the config
	 * @param boolean $merge Should the current config be merged in?
	 * 
	 * @return void No return value
	 */
	static public function set_config( $config, $merge = FALSE ) {
		$static_instance = get_called_class();
		if ( empty( self::$_config[ $static_instance ] ) ) {
			self::$_config[ $static_instance ] = array(); // Setup an empty instance
		}
		self::$_config[ $static_instance ] = static::array_merge_nested(
			array( //Default config
				'text_domain' => '',
				'asset_version' => '',
				'ajax_suffix' => static::ID,
				'scripts' => array(),
				'styles' => array(),
			),
			( $merge ) ? self::$_config[ $static_instance ] : array(), //if merge, merge the excisting values
			(array) $config //Added config
		);
	}

	/**
	 * Get method for the option array
	 *  
	 * @return array Returns the option array
	 */
	static public function get_config() {
		$static_instance = get_called_class();
		if ( ! isset( self::$_config[ $static_instance ] ) ) {
			static::error( __METHOD__, 'The instance config is not set, something went very wrong!' );
			return array();
		}
		return self::$_config[ $static_instance ];
	}

	/**
	 * Method
	 * 
	 * @return void No return value
	 */
	static public function get_ajax_suffix() {
		$config = static::get_config();
		return ( empty( $config[ 'ajax_suffix' ] ) ? static::ID : $config[ 'ajax_suffix' ] );
	}

	/**
	 * Method for the text_domain
	 *  
	 * @return array Returns the option array
	 */
	static public function get_text_domain() {
		$config = static::get_config();
		return $config['text_domain'];
	}

	/**
	 * Method for the asset_version
	 *  
	 * @return array Returns the option array
	 */
	static public function get_asset_version() {
		$config = static::get_config();
		return $config['asset_version'];
	}

	/**
	 * Method
	 * 
	 * @return void No return value
	 */
	static public function get_scripts() {
		$config = static::get_config();
		return ( empty( $config[ 'scripts' ] ) ? array() : (array) $config[ 'scripts' ] );
	}

	/**
	 * Method
	 * 
	 * @return void No return value
	 */
	static public function get_styles() {
		$config = static::get_config();
		return ( empty( $config[ 'styles' ] ) ? array() : (array) $config[ 'styles' ] );
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
	
}
