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
abstract class Static_Instance {

	/** Used as the ID of the instance */
	const ID = 'wpp-static-instance';

	/** Used to enable ajax callbacks */
	const ENABLE_AJAX = FALSE;

	/** Used to keep the state of the class */
	static private $_initialized = array();

	/** Used to store the class configuration */
	static private $_config = array();
	
	/**
	 * Initialization point for the static class
	 * 
	 * @return void No return value
	 */
	static public function init( $config = array() ) {
		$static_instance = get_called_class();
		if ( static::is_initialized() ) { 
			return; 
		}
		static::set_config( $config );
		static::init_before_initialized();
		self::$_initialized[ $static_instance ] = TRUE;
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
	 * Method to find the current initialized value of the instance
	 * 
	 * @return boolean Returns the initialized value of the instance
	 */
	static public function is_initialized() {
		$static_instance = get_called_class();
		return ( empty( self::$_initialized[ $static_instance ] ) ? FALSE : TRUE );
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

	/**
	 * Method for returning the current instance
	 * 
	 * @param string $location The location the message was for
	 * @param string $message The message to send to the error log
	 * @param string $options The options
	 * @return void No return value
	 */
	static public function current_instance() {
		return get_called_class();
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
