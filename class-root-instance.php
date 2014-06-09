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
abstract class Root_Instance extends Instance {

	/**
	 * Initialization point for the configuration
	 * 
	 * @return void No return value
	 */
	static public function init_config() {
		parent::init_config();
		//Global default config
		static::set_default_config( 'shortcodes', array(), TRUE );
		static::set_default_config( 'content_types', array(), TRUE );
		static::set_default_config( 'admin_sections', array(), TRUE );
		static::set_default_config( 'admin_pages', array(), TRUE );
		static::set_default_config( 'meta_boxes', array(), TRUE );
		//Local default config
		static::set_default_config( 'enable_shortcodes_in_text_widget', FALSE );
		static::set_default_config( 'enable_admin_sections', FALSE );
		static::set_default_config( 'enable_admin_pages', FALSE );
		static::set_default_config( 'enable_content_types', FALSE );
		static::set_default_config( 'enable_meta_boxes', FALSE );
		static::set_default_config( 'enable_shortcodes', FALSE );
		static::set_default_config( 'enable_link_manager', FALSE );
		static::set_default_config( 'enable_action_init', FALSE );
		static::set_default_config( 'enable_action_wp_head', FALSE );
		static::set_default_config( 'enable_filter_wp_title', FALSE );
		static::set_default_config( 'filter_wp_title_priority', 10 );

	}

	/**
	 * Method for after init has completed
	 * 
	 * @return void No return value
	 */
	static public function init_done() {
		parent::init_done();
		if ( static::get_config('enable_action_init') ) {
			add_action( 'init', array( static::current_instance(), 'action_init' ) ); //Wordpress init action
		}
		if ( static::get_config('enable_action_wp_head') ) {
			add_action( 'wp_head', array( static::current_instance(), 'action_wp_head' ) ); //Wordpress init action
		}
		if ( static::get_config('enable_filter_wp_title') ) {
			add_filter( 'wp_title', array( static::current_instance(), 'filter_wp_title' ), static::get_config('filter_wp_title_priority'), 2 );
		}
		if ( static::get_config('enable_link_manager') ) {
			add_filter( 'pre_option_link_manager_enabled', '__return_true' ); // Re-enable the link manager
		}
		if ( static::get_config('enable_shortcodes_in_text_widget') ) {
			add_filter( 'widget_text', 'shortcode_unautop'); 
			add_filter( 'widget_text', 'do_shortcode');
		}

		if ( static::get_config('enable_shortcodes') ) {
			static::init_array_of_classes( static::get_config('shortcodes') );
		}
		if ( static::get_config('enable_content_types') ) {
			static::init_array_of_classes( static::get_config('content_types') );
		}
		if ( is_admin() ) {
			if ( static::get_config('enable_admin_sections') ) {
				static::init_array_of_classes( static::get_config('admin_section') );
			}
			if ( static::get_config('enable_admin_pages') ) {
				static::init_array_of_classes( static::get_config('admin_pages') );
			}
			if ( static::get_config('enable_meta_boxes') ) {
				static::init_array_of_classes( static::get_config('meta_boxes') );
			}
			static::init_admin();
		} else {
			static::init_public();
		}
	}

	/*
	 * Method call only if not is_admin
	 * 
	 * @return void No return value
	 */
	static public function init_public() {
		//Place holder
	}


	/*
	 * Method call only if is_admin
	 * 
	 * @return void No return value
	 */
	static public function init_admin() {
		//Place holder
	}

	/**
	 * Init method for an array of classes
	 * 
	 * @return void No return value
	 */
	static public function init_array_of_classes( $classes ) {
		foreach ( (array) $classes as $class ) {
			static::init_static_class( $class );
		}
	}

	/**
	 * Init method for a static class
	 * 
	 * The method loops through the preconfigured admin_controllers 
	 * array set in the plugin options, then 
	 * 
	 * @return void No return value
	 */
	static public function init_static_class( $class ) {
		//static::debug( __METHOD__, array( $class, $config ) );
		if ( class_exists( $class ) && method_exists( $class, 'init' ) ) {
			$config = static::get_config_instance();
			$config::set( 'root_instance', static::current_instance(), $class );
			$class::init();
		} else {
			static::error( __METHOD__, "Static class ( $class ) did not exists and or have the required init method", E_USER_WARNING );
		}
	}

	/*
	 * 
	 */
	static public function action_init() {
		//Place holder
	}

	/*
	 * 
	 */
	static public function action_wp_head() {
		//Place holder
	}

	/*
	 * 
	 */
	static public function filter_wp_title( $title, $sep ) {
		//Place holder
		return $title;
	}


}
