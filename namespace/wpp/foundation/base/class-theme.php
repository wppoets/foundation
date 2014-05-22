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
abstract class Theme extends Root_Instance {

	/** Used to set the plugins ID */
	const ID = 'wpp-theme';

	/** Used to set the default option id */
	const DEFAULT_OPTION_KEY = 'wpp_theme_options';

	/** Used to set the deault autoload value */
	const DEFAULT_OPTION_AUTOLOAD = FALSE;

	/** Used to enable admin controllers */
	const ENABLE_ADMIN_CONTROLLERS = FALSE;

	/** Used to enable content types */
	const ENABLE_CONTENT_TYPES = FALSE;

	/** Used to enable meta boxes */
	const ENABLE_META_BOXES = FALSE;

	/** Used to enable shortcodes */
	const ENABLE_SHORTCODES = FALSE;

	/** Used to enable shortcodes */
	const ENABLE_SCRIPTS = FALSE;

	/** Used to enable shortcodes */
	const ENABLE_STYLES = FALSE;

	/** Used to enable action */
	const ENABLE_ACTION_INIT = FALSE;

	/** Used to enable action */
	const ENABLE_ACTION_WP_HEAD = FALSE;

	/** Used to enable filter */
	const ENABLE_FILTER_WP_TITLE = FALSE;

	/** Used to configure wp_title filter */
	const FILTER_WP_TITLE_PRIORITY = 10;

	/** Used to enable filter */
	const ENABLE_LINK_MANAGER = FALSE;

	/** Used to enable nav menus */
	const ENABLE_NAV_MENUS = FALSE;

	/** Used to enable sidebars */
	const ENABLE_SIDEBARS = FALSE;

	/** Used to enable theme support for post thubnails */
	const ENABLE_THEME_POST_THUMBNAILS = FALSE;

	/**  Used to disable the extra admin bar spacing that is injected */
	const DISABLE_ADMIN_BAR_SPACING_BUG = FALSE;

	/** Used to enable action */
	const ENABLE_ACTION_AFTER_SWITCH_THEME = FALSE;

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
			array( //Default config
				'nav_menus' => array(),
				'sidebars' => array(),
			),
			(array) $config //Added config
		), $merge );
	}

	/**
	 * Method called before initialized is set to true
	 * 
	 * @return void No return value
	 */
	static public function init_before_initialized() {
		parent::init_before_initialized();
		if ( static::ENABLE_ACTION_AFTER_SWITCH_THEME ) {
			add_action( 'after_switch_theme', array( static::current_instance(), 'action_after_switch_theme' ) ); //After the theme switches do stuff 
		}
		if ( static::ENABLE_THEME_POST_THUMBNAILS ) {
			add_theme_support( 'post-thumbnails' );
		}
		if ( static::DISABLE_ADMIN_BAR_SPACING_BUG ) {
			//Added to remove the special spacing for the admin bar that was added to the head
			add_theme_support( 'admin-bar', array( 'callback' => '__return_false' ) );
		}
		if ( static::ENABLE_NAV_MENUS ) {
			static::init_nav_menus();
		}
		if ( static::ENABLE_SIDEBARS ) {
			static::init_sidebars();
		}
	}

	/*
	 * 
	 */
	static public function init_nav_menus() {
		$config = static::get_config();
		if ( empty( $config['nav_menus'] ) ) {
			return;
		}
		register_nav_menus( $config['nav_menus'] );
	}

	/*
	 * 
	 */
	static public function init_sidebars() {
		$config = static::get_config();
		if ( empty( $config['sidebars'] ) ) {
			return;
		}
		foreach ( (array) $config['sidebars'] as $sidebar ) {
			if ( ! empty( $sidebar ) ) {
				register_sidebar( $sidebar );
			}
		}
	}

	/*
	 * 
	 */
	static public function action_after_switch_theme() {
		//Place holder
	}
}
