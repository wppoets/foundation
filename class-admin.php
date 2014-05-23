<?php namespace WPP\Foundation_Namespace_Base;
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
abstract class Admin extends Child_Instance {

	/** Used to set the meta-box ID */
	const ID = 'wpp-admin';

	/** Used to store the form prefex */
	const HTML_FORM_PREFIX = 'wpp_admin'; // should only use [a-z0-9_]

	/** Used to store the form prefex */
	const HTML_CLASS_PREFIX = 'wpp-admin-'; // should only use [a-z0-9_-]

	/** Used to store the form prefex */
	const HTML_ID_PREFIX = 'wpp-admin-'; // should only use [a-z0-9_-]

	/** Used to enable admin pages */
	const ENABLE_PAGES = FALSE;

	/** Used to enable enqueue_media function */
	const ENABLE_ENQUEUE_MEDIA = FALSE;

	/** Used to enable the admin footer */
	const ENABLE_ADMIN_FOOTER = FALSE;

	/** Used to enable the action admin_menu */
	const ENABLE_ADMIN_MENU = FALSE;

	/** Used to enable the action admin_init */
	const ENABLE_ADMIN_INIT = FALSE;

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

	/**
	 * Initialization point for the static class
	 * 
	 * @return void No return value
	 */
	static public function init( $config = array(), $merge = FALSE ) {
		if ( ! is_admin() || static::is_initialized() ) { 
			return; 
		}
		parent::init( $config, $merge );
	}

	/**
	 * Method called before initialized is set to true
	 * 
	 * @return void No return value
	 */
	static public function init_before_initialized() {
		parent::init_before_initialized();
		if ( static::ENABLE_PAGES ) {
			static::init_pages();
		}
		if ( static::ENABLE_ADMIN_INIT ) {
			add_action( 'admin_init', array( static::current_instance(), 'action_admin_init' ) );
		}
		if ( static::ENABLE_ADMIN_MENU ) {
			add_action( 'admin_menu', array( static::current_instance(), 'action_admin_menu' ) );
		}
		if ( static::ENABLE_ADMIN_FOOTER ) {
			add_action( 'admin_footer', array( static::current_instance(), 'action_admin_footer' ) );
		}
	}

	/**
	 * Init method for the admin pages
	 * 
	 * The method loops through the preconfigured admin pages 
	 * array
	 * 
	 * @return void No return value
	 */
	static public function init_pages() {
		$root_instance = static::get_root_instance();
		$root_instance::init_class_array( static::get_pages(), static::get_page_configs() );
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
		return parent::set_config( static::array_merge_nested(
			array( //Default options
				//'pages' => array(),
				//'page_configs' => array(),
			),
			(array) $config //Added options
		), $merge );
	}

	/**
	 * WordPress action for admin_init
	 * 
	 * @return void No return value
	 */
	static public function action_admin_init( ) {
		// Holder
	}

	/**
	 * WordPress action for admin_menu
	 * 
	 * @return void No return value
	 */
	static public function action_admin_menu( ) {
		// Holder
	}

	/**
	 * WordPress action for adding things to the admin footer
	 *
	 * @return void No return value
	 */
	static public function action_admin_footer() {
		//Holder
	}

	/**
	 * WordPress action for saving the post
	 * 
	 * @return void No return value
	 */
	static public function action_save_post( $post_id ) {
		// Holder
		if ( ! parent::action_save_post( $post_id ) ) {
			return;
		}
	}

	/**
	 * WordPress action for an ajax call
	 * 
	 * @return void No return value
	 */
	static public function action_wp_ajax( $data = array() ) {
		// Holder
		return parent::action_wp_ajax( $data );
	}

	/**
	 * Method
	 * 
	 * @return void No return value
	 */
	static public function get_pages() {
		$config = static::get_config();
		return ( empty( $config[ 'pages' ] ) ? array() : (array) $config[ 'pages' ] );
	}

	/**
	 * Method
	 * 
	 * @return void No return value
	 */
	static public function get_page_configs() {
		$config = static::get_config();
		return ( empty( $config[ 'page_configs' ] ) ? array() : (array) $config[ 'page_configs' ] );
	}
}
