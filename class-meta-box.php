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
abstract class Meta_Box extends Child_Instance {

	/** Used to set the meta-box ID */
	const ID = 'wpp-meta-box';

	/** Used to store the meta-box title */
	const DISPLAY_TITLE = 'WPP Meta Box';

	/** Used to store waht context the meta-box should be located */
	const DISPLAY_CONTEXT = 'advanced'; //('normal', 'advanced', or 'side')

	/** Used to store what priority the meta-box should have */
	const DISPLAY_PRIORITY = 'default'; //('high', 'core', 'default' or 'low')

	/** Used to store the form prefex */
	const HTML_FORM_PREFIX = 'wpp_meta_box'; // should only use [a-z0-9_]

	/** Used to store the form prefex */
	const HTML_CLASS_PREFIX = 'wpp-meta-box-'; // should only use [a-z0-9_-]

	/** Used to store the form prefex */
	const HTML_ID_PREFIX = 'wpp-meta-box-'; // should only use [a-z0-9_-]

	/** Used to enable ajax callbacks */
	const ENABLE_AJAX = FALSE;

	/** Used to enable enqueue_media function */
	const ENABLE_ENQUEUE_MEDIA = FALSE;

	/** Used to enable the admin footer */
	const ENABLE_ADMIN_FOOTER = FALSE;

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
	 * @param string|array $options An optional array containing the meta box options
	 *
	 * @return void No return value
	 */
	static public function init( $config = array(), $merge = FALSE ) {
		static::set_config( $config, TRUE ); //Addes so that no mater if the init as been run before we want to set the options with merge on
		if ( ! is_admin() || static::is_initialized() ) { 
			return; 
		}
		parent::init( $config, TRUE );
	}

	/**
	 * Method called before initialized is set to true
	 * 
	 * @return void No return value
	 */
	static public function init_before_initialized() {
		parent::init_before_initialized();
		add_action( 'add_meta_boxes', array( static::current_instance(), 'action_add_meta_boxes' ) );
		if ( static::ENABLE_ADMIN_INIT ) {
			add_action( 'admin_init', array( static::current_instance(), 'action_admin_init' ) );
		}
		if ( static::ENABLE_ADMIN_FOOTER ) {
			add_action( 'admin_footer', array( static::current_instance(), 'action_admin_footer' ) );
		}
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
				'post_types_includes' => array(),
				'post_types_excludes' => array(),
				'post_types_all' => FALSE,
				'scripts' => array(),
				'styles' => array(),
			),
			(array) $config //Added options
		), $merge );
	}

	/**
	 * WordPress action for adding meta boxes
	 * 
	 * @return void No return value
	 */
	static public function action_add_meta_boxes() {
		$post_types = static::post_types( static::is_post_types_all(), static::get_post_types_includes() , static::get_post_types_excludes() );
		foreach ( $post_types as $post_type ) {
			add_meta_box(
				static::ID,
				__( static::DISPLAY_TITLE, static::get_text_domain() ),
				array( static::current_instance(), 'action_meta_box_display' ),
				$post_type,
				static::DISPLAY_CONTEXT,
				static::DISPLAY_PRIORITY
			);
			add_action( "add_meta_boxes_{$post_type}", array( static::current_instance(), 'action_add_meta_boxes_content_type' ) );
		}
	}

	/**
	 * WordPress action for adding a meta-box to a specific content type
	 * 
	 * We use this to only enqueue scripts/styles for pages that are going
	 * to display the meta-box 
	 *
	 * @return void No return value
	 */
	static public function action_add_meta_boxes_content_type() {
		if ( static::ENABLE_ENQUEUE_MEDIA ) { 
			wp_enqueue_media(); 
		}
		add_action( 'admin_enqueue_scripts', array( static::current_instance(), 'action_admin_enqueue_scripts' ) );
		if ( static::ENABLE_ADMIN_FOOTER ) {
			add_action( 'admin_footer', array( static::current_instance(), 'action_admin_footer' ) );
		}
	}

	/**
	 * WordPress action for enqueueing admin scripts
	 *
	 * @return void No return value
	 */
	static public function action_admin_enqueue_scripts() {
		$root_instance = static::get_root_instance();
		$root_instance::enqueue_scripts( static::get_scripts() );
		$root_instance::enqueue_styles( static::get_styles() );
	}

	/**
	 * WordPress action for adding things to the admin init
	 *
	 * @return void No return value
	 */
	static public function action_admin_init() {
		//Holder
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
	 * WordPress action for displaying the meta-box
	 *
	 * @param object $post The post object the metabox is working with
	 * @param array $callback_args Extra call back args
	 *
	 * @return void No return value
	 */
	static public function action_meta_box_display( $post, $callback_args ) {
		if ( static::ENABLE_SAVE_POST_NONCE_CHECK ) {
			wp_nonce_field( static::current_instance(), static::HTML_FORM_PREFIX . '_wpnonce' );
		}
	}

	/**
	 * WordPress action for saving the post
	 * 
	 * @return void No return value
	 */
	static public function action_save_post( $post_id ) {
		//Holder
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
		return parent::action_wp_ajax( $data );
	}

	/**
	 * Method
	 * 
	 * @return void No return value
	 */
	static public function get_post_types_includes() {
		$config = static::get_config();
		return ( empty( $config[ 'post_types_includes' ] ) ? array() : (array) $config[ 'post_types_includes' ] );
	}

	/**
	 * Method
	 * 
	 * @return void No return value
	 */
	static public function get_post_types_excludes() {
		$config = static::get_config();
		return ( empty( $config[ 'post_types_excludes' ] ) ? array() : (array) $config[ 'post_types_excludes' ] );
	}

	/**
	 * Method to find if filter atts is enabled
	 * 
	 * @return void No return value
	 */
	static public function is_post_types_all() {
		$config = static::get_config();
		return ( empty( $config[ 'post_types_all' ] ) ? FALSE : TRUE );
	}

}
