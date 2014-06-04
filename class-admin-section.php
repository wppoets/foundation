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
abstract class Admin_Section extends Child_Instance {

	/**
	 * Initialization point for the configuration
	 * 
	 * @return void No return value
	 */
	static public function init_config() {
		parent::init_config();
		$current_instance = static::current_instance();
		$config = static::get_config_instance();
		$config::set_default( 'enable_admin_footer', FALSE, $current_instance );
		$config::set_default( 'enable_admin_menu', FALSE, $current_instance );
		$config::set_default( 'enable_admin_init', FALSE, $current_instance );
	}

	/**
	 * Method for after init has completed
	 * 
	 * @return void No return value
	 */
	static public function init_done() {
		parent::init_done();
		if ( static::get_config('enable_admin_init') ) {
			add_action( 'admin_init', array( static::current_instance(), 'action_admin_init' ) );
		}
		if ( static::get_config('enable_admin_menu') ) {
			add_action( 'admin_menu', array( static::current_instance(), 'action_admin_menu' ) );
		}
		if ( static::get_config('enable_admin_footer') ) {
			add_action( 'admin_footer', array( static::current_instance(), 'action_admin_footer' ) );
		}
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
	 * WordPress action for admin_footer
	 *
	 * @return void No return value
	 */
	static public function action_admin_footer() {
		//Holder
	}

}
