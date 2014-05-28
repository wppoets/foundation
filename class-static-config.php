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
abstract class Static_Config extends Static_Class {

	/** Used to keep the defaults */
	static private $_defaults = array();

	/** Used to keep the data */
	static private $_values = array();

	/**
	 * Initialization point for the class
	 * 
	 * @return string Returns the current class instance
	 */
	static public function init() {
		if ( static::is_initialized() ) { 
			return static::current_instance();
		}
		self::$_defaults[ static::current_instance() ] = array();
		self::$_values[ static::current_instance() ] = array();
		parent::init();
		return static::current_instance();
	}

	/**
	 * Init point for the configuration
	 * 
	 * @return void No return value
	 */
	static public function init_config() {
		//Prevent the defualt
	}

	/**
	 * $current_instance = static::current_instance();
	 */
	static public function set_default( $key, $value, $instance = 'global_instance' ) {
		if ( ! isset( self::$_defaults[ static::current_instance() ][ $instance ] ) ) {
			self::$_defaults[ static::current_instance() ][ $instance ] = array();
		}
		if ( ! isset( self::$_defaults[ $instance ] ) ) {
			self::$_defaults[ static::current_instance() ][ $instance ] = array();
		}
		self::$_defaults[ static::current_instance() ][ $instance ][ $key ] = $value;
	}

	/**
	 * 
	 */
	static public function get_default( $key, $instance = 'global_instance' ) {
		if ( static::has_default( $key, $instance ) ) {
			return self::$_defaults[ static::current_instance() ][ $instance ][ $key ];
		}
		return NULL;
	}

	/**
	 * 
	 */
	static public function has_default( $key, $instance = 'global_instance' ) {
		return isset( self::$_defaults[ static::current_instance() ][ $instance ][ $key ] );
	}

	/**
	 * 
	 */
	static public function set( $key, $value, $instance = 'global_instance' ) {
		if ( ! isset( self::$_values[ static::current_instance() ][ $instance ] ) ) {
			self::$_values[ static::current_instance() ][ $instance ] = array();
		}
		self::$_values[ static::current_instance() ][ $instance ][ $key ] = $value;
	}

	/**
	 * 
	 */
	static public function get( $key, $instance = 'global_instance' ) {
		if ( static::has( $key, $instance ) ) {
			return self::$_values[ static::current_instance() ][ $instance ][ $key ];
		}
		return static::get_default( $key, $instance );
	}

	/**
	 * 
	 */
	static public function has( $key, $instance = 'global_instance' ) {
		return isset( self::$_values[ static::current_instance() ][ $instance ][ $key ] );
	}

	/**
	 * Get method
	 *  
	 * @return string Returns static class name
	 */
	static public function get_config_instance() {
		return static::current_instance();
	}

	/**
	 * Get method for finding missing required fields
	 *  
	 * @return array Returns an array of missing keys to empty values
	 */
	static public function get_missing_required( $required, $instance = 'global_instance' ) {
		$required = (array) $required;
		foreach ( $required as $id => $key ) {
			$test_value_1 = static::get( $key, $instance );
			$test_value_2 = static::get( $key );
			if ( ! empty( $test_value_1 ) ) {
				unset( $required[ $id ] );
			} else if ( ! empty( $test_value_2 ) ) {
				unset( $required[ $id ] );
			}
			unset( $test_value );
		}
		$required = array_values( $required );
		if ( ! empty( $required ) ) {
			static::debug(__METHOD__, self::$_values);
		}
		return $required;
	}
}
