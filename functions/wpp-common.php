<?php
/**
 * Copyright (c) 2014, WP Poets and/or its affiliates <copyright@wppoets.com>
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
 * WordPress Helper function for the debug process
 * 
 * @author Unknown
 * @param string $message The message to send to the error log
 * @return void No return value
 * @version 1.0.0
 */
if ( ! function_exists( 'wpp_debug' ) ) {
	function wpp_debug( $message, $options = array() ) {
		if ( WP_DEBUG === true ) {
			$options = array_merge(
				array(
					'var_export' => FALSE,
				),
				$options
			);
			if ( is_array( $message ) || is_object( $message ) ) {
				error_log( $options['var_export'] ? var_export( $message, TRUE) : print_r( $message, true ) );
			} else {
				error_log( $options['var_export'] ? var_export( $message, TRUE) : $message );
			}
		}
	}
}

/**
 * General Helper function merging nested arrays
 *
 * @author Michael Stutz <michaeljstutz@gmail.com>
 * @param array $... Will loop through all arrays passed in
 * @return array The merged results
 * @version 1.0.10
 */
if ( ! function_exists( 'wpp_array_merge_nested' ) ) {
	function wpp_array_merge_nested() {
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
					$return_array[ $k ] = wpp_array_merge_nested( $return_array[ $k ], $v ); // Return array key value equals the merged results
				}
				unset( $k, $v ); // Clean up
			}
			unset( $a ); // Clean up
		}
		return $return_array; // Return results
	}
}

/**
 * General Helper function for creating
 *
 * The code for the most part was taken from WordPress's shortcode_atts() function
 * 
 * @author Michael Stutz <michaeljstutz@gmail.com>
 * @param array $default_options The base options, anything not in this array will not be set
 * @param array $options The options to be set
 * @return array The completed options array
 * @version 1.0.1
 */
if ( ! function_exists( 'wpp_array_merge_options' ) ) {
	function wpp_array_merge_options( $default_options, $options ) {
		$return_options = array();
		foreach ( $default_options as $name => $value ) {
			if ( array_key_exists( $name, $options ) ) {
				$return_options[ $name ] = $options[ $name ];
			} else {
				$return_options[ $name ] = $value;
			}
		}
		return $return_options;
	}
}