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
abstract class Theme extends Root_Instance {

	/**
	 * Initialization point for the configuration
	 * 
	 * @return void No return value
	 */
	static public function init_config() {
		parent::init_config();
		static::set_default_config( 'nav_menus', array() );
		static::set_default_config( 'sidebars', array() );
		static::set_default_config( 'enable_nav_menus', FALSE );
		static::set_default_config( 'enable_sidebars', FALSE );
		static::set_default_config( 'enable_theme_post_thumbnails', FALSE );
		static::set_default_config( 'enable_action_after_switch_theme', FALSE );
		static::set_default_config( 'disable_admin_bar_spacing_bug', FALSE );
	}

	/**
	 * Method for after init has completed
	 * 
	 * @return void No return value
	 */
	static public function init_done() {
		parent::init_done();
		if ( static::get_config('enable_action_after_switch_theme') ) {
			add_action( 'after_switch_theme', array( static::current_instance(), 'action_after_switch_theme' ) ); //After the theme switches do stuff 
		}
		if ( static::get_config('enable_theme_post_thumbnails') ) {
			add_theme_support( 'post-thumbnails' );
		}
		if ( static::get_config('disable_admin_bar_spacing_bug') ) {
			//Added to remove the special spacing for the admin bar that was added to the head
			add_theme_support( 'admin-bar', array( 'callback' => '__return_false' ) );
		}
		if ( static::get_config('enable_nav_menus') ) {
			static::init_nav_menus();
		}
		if ( static::get_config('enable_sidebars') ) {
			static::init_sidebars();
		}
	}

	/*
	 * 
	 */
	static public function init_nav_menus() {
		$nav_menus = static::get_config('nav_menus');
		if ( empty( $nav_menus ) ) {
			return;
		}
		register_nav_menus( (array) $nav_menus );
	}

	/*
	 * 
	 */
	static public function init_sidebars() {
		$sidebars = static::get_config('sidebars');
		if ( empty( $sidebars ) ) {
			return;
		}
		foreach ( (array) $sidebars as $sidebar ) {
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
