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
abstract class Static_Class {

	/** Used to keep the state of the class */
	static private $_initialized = array();
	
	/**
	 * Initialization point for the static class
	 * 
	 * @return void No return value
	 */
	static public function init() {
		if ( static::is_initialized() ) { 
			return; 
		}
		self::$_initialized[ static::current_instance() ] = TRUE;
	}

	/**
	 * Method to find the current initialized value of the instance
	 * 
	 * @return boolean Returns the initialized value of the instance
	 */
	static public function is_initialized() {
		return isset( self::$_initialized[ static::current_instance() ] ) ? TRUE : FALSE ;
	}

	/**
	 * Method for returning the current instance
	 * 
	 * @return string Class name of the called instance
	 */
	static public function current_instance() {
		return get_called_class();
	}

	/**
	 * Method for merging nested arrays
	 *
	 * @param array $... Will loop through all arrays passed in
	 * @return array The merged results
	 */
	static public function array_merge_nested() {
		$return_array = array(); // Empty return
		foreach( func_get_args() as $a ) { // Loop through the passed arguments
			foreach( (array) $a as $k => $v ) { // Loop through the array casted argument
				if ( is_int( $k ) && ( is_array( $v ) || ! empty( $v ) ) ) { // If the key is an int and is an array or not empty
					$return_array[] = $v; // Ammend to the return array
				} elseif ( is_int( $k ) && ( ! is_array( $v ) || empty( $v ) ) ) { // If the key is an int and is not an array or is empty
					// Do nothing!
				} elseif ( ! isset( $return_array[ $k ] ) ) { // The key is not an int and the return array does not have a set value
					$return_array[ $k ] = $v; // Overwrite old return array key value with new value
				} elseif ( ! is_array( $return_array[ $k ] ) && ! is_array( $v ) ) { // Both values are not arrays
					$return_array[ $k ] = $v; // Overwrite old return array key value with new value
				} elseif ( empty( $return_array[ $k ] ) && is_array( $v ) ) { // The return array key value is empty and the new value is an array
					$return_array[ $k ] = $v; // Overwrite old return array key value with new value
				} elseif ( is_array( $return_array[ $k ] ) && empty( $v ) && $v !== '' ) { //If the return array key value is an array and the value is empty but not an empty string
					$return_array[ $k ] = $v; // Overwrite old return array key value with new value
				} else { // Else
					$return_array[ $k ] = static::array_merge_nested( $return_array[ $k ], $v ); // Return array key value equals the merged results
				}
				unset( $k, $v ); // Clean up
			}
			unset( $a ); // Clean up
		}
		return $return_array; // Return results
	}

	/**
	 * Method for merging two arrays with only keys from the source 
	 *
	 * The code for the most part was taken from WordPress's shortcode_atts() function
	 * 
	 * @param array $source_values The base array, anything not in this array will not be set
	 * @param array $new_values The new values to be set
	 * @return array The new merged array
	 */
	static public function array_merge_source_only( $source_values, $new_values ) {
		$return_values = array();
		foreach ( $source_values as $key => $value ) {
			if ( array_key_exists( $key, $new_values ) ) {
				$return_values[ $key ] = $new_values[ $key ];
			} else {
				$return_values[ $key ] = $value;
			}
		}
		return $return_values;
	}

	/**
	 * Method for the debug process
	 * 
	 * @param string $message The message to send to the error log
	 * @param string $options The options
	 * @return void No return value
	 */
	static public function debug( $location, $message, $options = array() ) {
		$options = static::array_merge_source_only(
			array(
				'var_export' => FALSE,
				'backtrace' => FALSE,
			),
			$options
		);
		error_log( static::formated_log_message( $location, $message, $options ) );
		if ( $options[ 'backtrace' ] ) {
			error_log( static::formated_log_message( $location . ' debug_backtrace()', debug_backtrace(), $options ) );
		}
	}

	/**
	 * Method for the debug process
	 * 
	 * @param string $message The message to send to the error log
	 * @param string $options The options
	 * @return void No return value
	 */
	static public function error( $location, $message, $error_type = E_USER_NOTICE, $options = array() ) {
		$options = static::array_merge_source_only(
			array(
				'var_export' => FALSE,
				'backtrace' => FALSE,
			),
			$options
		);
		trigger_error( static::formated_log_message( $location, $message, $options ), $error_type );
		if ( $options[ 'backtrace' ] ) {
			trigger_error( static::formated_log_message( $location . ' debug_backtrace()', debug_backtrace(), $options ), $error_type );
		}
	}

	/**
	 * Method for formating the log message
	 * 
	 * @param string $location The location the message was for
	 * @param string $message The message to send to the error log
	 * @param string $options The options
	 * @return void No return value
	 */
	static public function formated_log_message( $location, $message, $options = array() ) {
		$options = static::array_merge_source_only(
			array(
				'var_export' => FALSE,
			),
			$options
		);
		$formated_message = $location . ': ';
		if ( $options['var_export'] ) {
			$formated_message .= var_export( $message, TRUE );
		} else if ( is_array( $message ) || is_object( $message ) ) {
			$formated_message .= print_r( $message, true );
		} else {
			$formated_message .= $message;
		}
		return $formated_message;
	}
}
