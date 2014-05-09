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
abstract class Child_Instance extends Static_Instance {

	/** Used as the ID of the instance */
	const ID = 'wpp-child-instance';

	/** Used to store the form prefex */
	const HTML_FORM_PREFIX = 'wpp_child_instance'; // should only use [a-z0-9_]

	/** Used to store the form prefex */
	const HTML_CLASS_PREFIX = 'wpp-child-instance-'; // should only use [a-z0-9_-]

	/** Used to store the form prefex */
	const HTML_ID_PREFIX = 'wpp-child-instance-'; // should only use [a-z0-9_-]

	/** Used to enable the action save_post */
	const ENABLE_SAVE_POST = FALSE;

	/** Used to set if the class uses action_save_post */
	const ENABLE_SAVE_POST_NONCE_CHECK = FALSE;

	/** Used to set if the class uses action_save_post */
	const ENABLE_SAVE_POST_AUTOSAVE_CHECK = FALSE;

	/** Used to set if the class uses action_save_post */
	const ENABLE_SAVE_POST_REVISION_CHECK = FALSE;

	/** Used to set if the class uses action_save_post */
	const ENABLE_SAVE_POST_CHECK_CAPABILITIES_CHECK = FALSE;

	/** Used to enable the admin footer */
	const ENABLE_SAVE_POST_SINGLE_RUN = FALSE;

	/** Used to set if the class uses action_save_post */
	const SAVE_POST_CHECK_CAPABILITIES = '';

	/** Used to store if save_post has run before */
	static private $_save_post = array();

	/**
	 * Method called before initialized is set to true
	 * 
	 * @return void No return value
	 */
	static public function init_before_initialized() {
		parent::init_before_initialized();
		if ( static::ENABLE_SAVE_POST ) {
			add_action( 'save_post', array( static::current_instance(), 'action_save_post' ) );
		}
	}

	/**
	 * Set method for the config
	 *  
	 * @param string|array $config An array containing the config
	 * @param boolean $merge Should the current config be merged in?
	 * 
	 * @return void No return value
	 */
	static public function set_config( $config, $merge = FALSE ) {
		return parent::set_config( static::array_merge_nested(
			array( //Default config
				'scripts' => array(),
				'styles' => array(),
			),
			(array) $config //Added config
		), $merge );
	}

	/**
	 * WordPress action for saving the post
	 * 
	 * @return void No return value
	 */
	static public function action_save_post( $post_id ) {
		if ( static::ENABLE_SAVE_POST_AUTOSAVE_CHECK && defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  {  // Check if is auto saving
			return; 
		}
		if ( static::ENABLE_SAVE_POST_REVISION_CHECK && wp_is_post_revision( $post_id ) ) {  // Check if is revision
			return; 
		}
		if ( static::ENABLE_SAVE_POST_SINGLE_RUN ) {
			$static_instance = get_called_class();
			if ( ! empty( self::$_save_post[ $static_instance ][ $post_id ] ) ) { 
				return; 
			}
			if ( ! isset( self::$_save_post[ $static_instance ] ) ) {
				self::$_save_post[ $static_instance ] = array();
			}
			self::$_save_post[ $static_instance ][ $post_id ] = TRUE;
		}
		if ( static::ENABLE_SAVE_POST_CHECK_CAPABILITIES_CHECK ) {
			foreach ( explode( ',', static::SAVE_POST_CHECK_CAPABILITIES ) as $capability ) {
				if ( ! empty( $capability ) && ! current_user_can( $capability, $post_id ) ) {  // Check user has capability to continue
					return;
				}
			}
		}
		if ( static::ENABLE_SAVE_POST_NONCE_CHECK 
			&& ! wp_verify_nonce( filter_input( INPUT_POST, static::HTML_FORM_PREFIX . '_wpnonce', FILTER_SANITIZE_STRING ), static::current_instance() ) 
			) {
			return;
		}
		return TRUE;

		// Example usage
		//if ( ! parent::action_save_post( $post_id ) ) {
		//	return;
		//}
	}

	/**
	 * Get method for a wp option
	 *  
	 * @return mixed
	 */
	static public function get_option( $key = NULL ) {
		$root_instance = static::get_root_instance();
		if ( ! empty( $key ) ) {
			return $root_instance::get_option( $key );
		}
		return $root_instance::get_option();
	}

	/**
	 * Set method for a wp option
	 *  
	 * @return mixed
	 */
	static public function set_option( $value, $key = NULL, $autoload = NULL ) {
		$root_instance = static::get_root_instance();
		if ( ! empty( $key ) && ! empty( $autoload ) ) {
			return $root_instance::set_option( $value, $key, $autoload );
		} else if ( ! empty( $key ) ) {
			return $root_instance::set_option( $value, $key );
		}
		return $root_instance::set_option( $value );
	}

	/**
	 * Method for the base_url from key
	 *  
	 * @return string Returns the base url
	 */
	static public function get_base_url( $key = '' ) {
		$root_instance = static::get_root_instance();
		return $root_instance::get_base_url( $key );
	}

	/**
	 * Method for the extention from key
	 *  
	 * @return string Returns the base url
	 */
	static public function get_extention( $key ) {
		$root_instance = static::get_root_instance();
		return $root_instance::get_extention( $key );
	}

	/**
	 * Method for the default option key
	 *  
	 * @return string Returns the base url
	 */
	static public function get_default_option_key() {
		$root_instance = static::get_root_instance();
		return $root_instance::get_default_option_key();
	}

	/**
	 * Method for the metadata key prefix
	 *  
	 * @return string Returns the metadata key prefix
	 */
	static public function get_metadata_key_prefix() {
		$root_instance = static::get_root_instance();
		return $root_instance::get_metadata_key_prefix();
	}

	/**
	 * Method for the metadata key
	 *  
	 * @return string Returns the metadata key
	 */
	static public function get_metadata_key( $key ) {
		$root_instance = static::get_root_instance();
		return $root_instance::get_metadata_key( $key );
	}

	/**
	 * Method for returning list of post types
	 *
	 * @param boolean $include_all Should we include all post types?
	 * @param array $include An array containing the post types to include
	 * @param array $exclude An array containing the post types to exclude
	 *
	 * @return array Returns an array of post types
	 */
	static public function post_types( $include_all, $includes = array(), $excludes = array() ) {
		$post_types = (array) $includes;
		if ( $include_all ) {
			$post_types = get_post_types( array( 'public' => TRUE ), 'names' );
		}
		$post_types = array_unique( $post_types );
		foreach( $excludes as $exclude ) {
			$matched_key = array_search( $exclude, $post_types );
			if( ! empty( $matched_key ) ) {
				unset( $post_types[ $matched_key] ); //Remove the excluded post type
			}
		}
		return $post_types;
	}

	/**
	 * Method to return the root instance
	 * 
	 * @return string Returns the root instance name
	 */
	static public function get_root_instance() {
		$config = static::get_config();
		if ( ! empty( $config[ 'root_instance' ] ) ) {
			return $config[ 'root_instance' ];
		} else {
			static::error( __METHOD__, 'Empty root instance' );
		}
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

}