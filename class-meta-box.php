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

	/**
	 * Initialization point for the configuration
	 * 
	 * @return void No return value
	 */
	static public function init_config() {
		parent::init_config();
		static::set_default_config( 'display_title', '' );
		static::set_default_config( 'display_content', 'advanced' ); //('normal', 'advanced', or 'side')
		static::set_default_config( 'display_priority', 'default' ); //('high', 'core', 'default' or 'low')
		static::set_default_config( 'post_types_includes', array() );
		static::set_default_config( 'post_types_excludes', array() );
		static::set_default_config( 'post_types_all', FALSE );
		static::set_default_config( 'enable_enqueue_media', FALSE );
		static::set_default_config( 'enable_admin_footer', FALSE );
		static::set_default_config( 'enable_admin_init', FALSE );
	}

	/**
	 * Method for after init has completed
	 * 
	 * @return void No return value
	 */
	static public function init_done() {
		parent::init_done();
		add_action( 'add_meta_boxes', array( static::current_instance(), 'action_add_meta_boxes' ) );
		if ( static::get_config('enable_admin_init') ) {
			add_action( 'admin_init', array( static::current_instance(), 'action_admin_init' ) );
		}
	}

	/**
	 * WordPress action for adding meta boxes
	 * 
	 * @return void No return value
	 */
	static public function action_add_meta_boxes() {
		$post_types = static::post_types( static::get_config('post_types_all'), (array) static::get_config('post_types_includes'), (array) static::get_config('post_types_excludes') );
		foreach ( $post_types as $post_type ) {
			add_meta_box(
				static::get_config('id'),
				__( static::get_config('display_title'), static::get_config('text_domain') ),
				array( static::current_instance(), 'action_meta_box_display' ),
				$post_type,
				static::get_config('display_content'),
				static::get_config('display_priority')
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
		if ( static::get_config('enable_enqueue_media') ) { 
			wp_enqueue_media(); 
		}
		add_action( 'admin_enqueue_scripts', array( static::current_instance(), 'action_admin_enqueue_scripts' ) );
		if ( static::get_config('enable_admin_footer') ) {
			add_action( 'admin_footer', array( static::current_instance(), 'action_admin_footer' ) );
		}
	}

	/**
	 * WordPress action for enqueueing admin scripts
	 *
	 * @return void No return value
	 */
	static public function action_admin_enqueue_scripts() {
		static::enqueue_scripts();
		static::enqueue_styles(); //Because wordpress does not have a admin_enqueue_styless
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
		if ( static::get_config('enable_save_post_nonce_check') ) {
			wp_nonce_field( static::current_instance(), static::get_config('html_form_prefix') . '_wpnonce' );
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

}
