<?php namespace WPPoets;
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
 * @author Michael Stutz <michaeljstutz@gmail.com>
 * @version 1.0.2
 */
abstract class Theme {

	/** Used to set the ID */
	const ID = 'wpp-theme';
	
	/** Used to set */
	const HAS_INIT = FALSE;

	/** Used to set */
	const HAS_NAV_MENUS = FALSE;

	/** Used to set */
	const HAS_SIDEBARS = FALSE;

	/** Used to set */
	const HAS_AFTER_SWITCH_THEME = FALSE;

	/** Used to set */
	const HAS_WP_HEAD = FALSE;

	/** Used to set */
	const HAS_WP_ENQUEUE_SCRIPTS = FALSE;

	/** Used to keep the init state of the class */
	static private $_initialized = array();
	
	/** Used to store the plugin options */
	static private $_options = array();

	/**
	 * Initialization point for the static class
	 * 
	 * @return void No return value
	 */
	static public function init( $options = array() ) {
		$static_instance = get_called_class();
		if ( ! empty( self::$_initialized[ $static_instance ] ) ) { 
			return; 
		}

		static::set_options( $options );
		static::init_content_types();

		if ( static::HAS_INIT ) {
			add_action( 'init', array( $static_instance, 'action_init' ) ); //Wordpress init action
		}
		if ( static::HAS_NAV_MENUS ) {
			static::init_menus();
		}
		if ( static::HAS_SIDEBARS ) {
			static::init_sidebars();
		}
		if ( static::HAS_AFTER_SWITCH_THEME ) {
			add_action( 'after_switch_theme', array( $static_instance, 'action_after_switch_theme' ) ); //After the theme switches do stuff 
		}
		if ( static::HAS_WP_HEAD ) {
			add_action( 'wp_head', array( $static_instance, 'action_wp_head' ) );
		}
		if ( static::HAS_WP_ENQUEUE_SCRIPTS ) {
			add_action( 'wp_enqueue_scripts', array( $static_instance, 'action_wp_enqueue_scripts' ) );
		}
		if ( ! is_admin() ) {
			static::init_public();
		} else {
			static::init_admin();
			static::init_meta_boxes();
		}
		self::$_initialized[ $static_instance ] = TRUE;
	}

	/**
	 * Init method for the content types
	 * 
	 * @return void No return value
	 */
	static public function init_content_types() {
		$static_instance = get_called_class();
		foreach ( (array) self::$_options[ $static_instance ][ 'content_types' ] as $class => $class_options ) {
			static::init_static_class( $class, $class_options );
		}
	}

	/**
	 * Init method for the meta boxes
	 * 
	 * @return void No return value
	 */
	static public function init_meta_boxes() {
		$static_instance = get_called_class();
		foreach ( (array) self::$_options[ $static_instance ][ 'meta_boxes' ] as $class => $class_options ) {
			static::init_static_class( $class, $class_options );
		}
	}

	/*
	 * 
	 */
	static public function init_menus() {
		$static_instance = get_called_class();
		$options = &self::$_options[ $static_instance ];
		if ( ! empty( $options['nav_menus'] ) ) {
			register_nav_menus( $options['nav_menus'] );
		}
	}

	/*
	 * 
	 */
	static public function init_sidebars() {
		$static_instance = get_called_class();
		$options = &self::$_options[ $static_instance ];
		foreach ( (array) $options['sidebars'] as $sidebar ) {
			if ( ! empty( $sidebar ) ) {
				register_sidebar( $sidebar );
			}
		}
	}

	/*
	 * 
	 */
	static public function init_public() {
		//
	}

	/*
	 * 
	 */
	static public function init_admin() {
		static::init_admin_controllers();
	}

	/*
	 * 
	 */
	static public function init_admin_controllers() {
		$static_instance = get_called_class();
		$options = &self::$_options[ $static_instance ];
	}

	/**
	 * Method to find the current initialized value of the instance
	 * 
	 * @return boolean Returns the initialized value of the instance
	 */
	static public function is_initialized() {
		$static_instance = get_called_class();
		$options = &self::$_options[ $static_instance ];
		return ( empty( self::$_initialized[ $static_instance ] ) ? FALSE : TRUE );
	}
	
	/**
	 * Set method for the options
	 *  
	 * @param string|array $options An array containing the options
	 * @param boolean $merge Should the current options be merged in?
	 * 
	 * @return void No return value
	 */
	static public function set_options( $options, $merge = FALSE ) {
		$static_instance = get_called_class();
		if ( empty( self::$_options[ $static_instance ] ) ) {
			self::$_options[ $static_instance ] = array(); //setup an empty instance if empty
		}
		self::$_options[ $static_instance ] = wpp_array_merge_nested(
			array( //Default options
				'content_types' => array(),
				'meta_boxes' => array(),
				'scripts' => array(),
				'styles' => array(),
				'nav_menus' => array(),
				'sidebars' => array(),
			),
			( $merge ) ? self::$_options[ $static_instance ] : array(), //if merge, merge the excisting values
			(array) $options //Added options
		);
	}

	/*
	 * Get method for the option array
	 *  
	 * @return array Returns the option array
	 */
	static public function get_options() {
		$static_instance = get_called_class();
		return self::$_options[ $static_instance ];
	}

	/*
	 * 
	 */
	static public function action_init() {

	}

	/*
	 * 
	 */
	static public function action_after_switch_theme() {

	}

	/*
	 * 
	 */
	static public function action_wp_enqueue_scripts() {
		$static_instance = get_called_class();
		$options = &self::$_options[ $static_instance ];
		if ( ! empty( $options['scripts'] ) ) {
			foreach ( (array) $options['scripts'] as $script_id => $script ) {
				if ( ! empty( $script['uri'] ) ) {
					$requires = empty( $script['requires'] ) ? NULL : $script['requires'];
					$version = empty( $script['version'] ) ? NULL : $script['version'];
					wp_register_script( $script_id, $script['uri'], $requires, $version );
					unset( $requires, $version );
				}
			}
			foreach ( (array) $options['scripts'] as $script_id => $script ) {
				if ( ! empty( $script['uri'] ) ) {
					wp_enqueue_script( $script_id );
				}
			}
		}
		if ( ! empty( $options['styles'] ) ) {
			foreach ( (array) $options['styles'] as $style_id => $style ) {
				if ( ! empty( $style['uri'] ) ) {
					$requires = empty( $style['requires'] ) ? NULL : $style['requires'];
					$version = empty( $style['version'] ) ? NULL : $style['version'];
					wp_register_style( $style_id, $style['uri'], $requires, $version );
					unset( $requires, $version );
				}
			}
			foreach ( (array) $options['styles'] as $style_id => $style ) {
				if ( ! empty( $style['uri'] ) ) {
					wp_enqueue_style( $style_id );
				}
			}
		}
	}

	/*
	 * 
	 */
	static public function action_wp_head() {

	}

	/**
	 * Init method for a static class
	 * 
	 * The method loops through the preconfigured admin_controllers 
	 * array set in the plugin options, then 
	 * 
	 * @return void No return value
	 */
	static private function init_static_class( $class, $options = array() ) {
		if ( class_exists( $class ) && method_exists( $class, 'init' ) ) {
			$class::init( $options );
		}
	}

}
