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
abstract class Child_Instance extends Instance {

	/** Used to store if save_post has run before */
	static private $_save_post = array();

	/**
	 * Initialization point for the configuration
	 * 
	 * @return void No return value
	 */
	static public function init_config() {
		parent::init_config();
		$current_instance = static::current_instance();
		$config = static::get_config_instance();
		$config::set_default( 'root_instance', '', $current_instance );
		$config::set_default( 'enable_save_post', FALSE, $current_instance );
		$config::set_default( 'enable_save_post_nonce_check', FALSE, $current_instance );
		$config::set_default( 'enable_save_post_autosave_check', FALSE, $current_instance );
		$config::set_default( 'enable_save_post_revision_check', FALSE, $current_instance );
		$config::set_default( 'enable_save_post_check_capabilities_check', FALSE, $current_instance );
		$config::set_default( 'enable_save_post_single_run', FALSE, $current_instance );
		$config::set_default( 'save_post_check_capabilities',array(), $current_instance );
	}

	/**
	 * Init config check
	 * 
	 * @return void No return value
	 */
	static public function init_check_config( $settings = array() ) {
		parent::init_check_config( array_unique ( array_merge( $settings, array(
			'root_instance',
		) ) ) );
	}
	
	/**
	 * Method for after init has completed
	 * 
	 * @return void No return value
	 */
	static public function init_done() {
		parent::init_done();
		if ( static::get_config('enable_save_post') ) {
			add_action( 'save_post', array( static::current_instance(), 'action_save_post' ) );
		}
	}

	/**
	 * WordPress action for saving the post
	 * 
	 * @return void No return value
	 */
	static public function action_save_post( $post_id ) {
		if ( static::get_config('enable_save_post_autosave_check') && defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  {  // Check if is auto saving
			return; 
		}
		if ( static::get_config('enable_save_post_revision_check') && wp_is_post_revision( $post_id ) ) {  // Check if is revision
			return; 
		}
		$current_instance = static::current_instance();
		if ( static::get_config('enable_save_post_single_run') ) {
			if ( ! empty( self::$_save_post[ $current_instance ][ $post_id ] ) ) { 
				return; 
			}
			if ( ! isset( self::$_save_post[ $current_instance ] ) ) {
				self::$_save_post[ $current_instance ] = array();
			}
			self::$_save_post[ $current_instance ][ $post_id ] = TRUE;
		}
		if ( static::get_config('enable_save_post_check_capabilities_check') ) {
			foreach ( (array) static::get_config('save_post_check_capabilities') as $capability ) {
				if ( ! empty( $capability ) && ! current_user_can( $capability, $post_id ) ) {  // Check user has capability to continue
					return;
				}
			}
		}
		if ( static::get_config('enable_save_post_nonce_check')
			&& ! wp_verify_nonce( filter_input( INPUT_POST, static::get_config('html_form_prefix') . '_wpnonce', FILTER_SANITIZE_STRING ), $current_instance ) 
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
		foreach( (array) $excludes as $exclude ) {
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
		$root_instance = static::get_config('root_instance');
		if ( ! empty( $root_instance ) ) {
			return $root_instance;
		} else {
			static::error( __METHOD__, 'Empty root instance' );
		}
	}

}
