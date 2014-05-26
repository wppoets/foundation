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
	 * 
	 */
	static public function set_default( $key, $value, $instance = 'global_instance' ) {
		if ( ! isset( self::$_defaults[ $instance ] ) ) {
			self::$_defaults[ $instance ] = array();
		}
		self::$_defaults[ $instance ][ $key ] = $value;
	}

	/**
	 * 
	 */
	static public function get_default( $key, $instance = 'global_instance' ) {
		if ( static::has_default( $key, $instance ) ) {
			return self::$_defaults[ $instance ][ $key ];
		}
		return NULL;
	}

	/**
	 * 
	 */
	static public function has_default( $key, $instance = 'global_instance' ) {
		return isset( self::$_defaults[ $instance ][ $key ] );
	}

	/**
	 * 
	 */
	static public function set( $key, $value, $instance = 'global_instance' ) {
		if ( ! isset( self::$_values[ $instance ] ) ) {
			self::$_values[ $instance ] = array();
		}
		self::$_values[ $instance ][ $key ] = $value;
	}

	/**
	 * 
	 */
	static public function get( $key, $instance = 'global_instance' ) {
		if ( static::has( $key, $instance ) ) {
			return self::$_values[ $instance ][ $key ];
		}
		return static::get_default( $key, $instance );
	}

	/**
	 * 
	 */
	static public function has( $key, $instance = 'global_instance' ) {
		return isset( self::$_values[ $instance ][ $key ] );
	}
}
