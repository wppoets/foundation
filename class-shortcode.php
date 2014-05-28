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
abstract class Shortcode extends Child_Instance {

	/**
	 * Initialization point for the configuration
	 * 
	 * @return void No return value
	 */
	static public function init_config() {
		parent::init_config();
		$config = static::get_config_instance();
		$current_instance = static::current_instance();
		$config::set_default( 'short_code_tag', '', $current_instance );
		$config::set_default( 'enable_filter_atts', FALSE, $current_instance );
	}

	/**
	 * Init config check
	 * 
	 * @return void No return value
	 */
	static public function init_check_config( $settings = array() ) {
		parent::init_check_config( array_unique ( array_merge( $settings, array(
			'short_code_tag',
		) ) ) );
	}

	/**
	 * Method for after init has completed
	 * 
	 * @return void No return value
	 */
	static public function init_done() {
		add_shortcode( static::get_config('short_code_tag'), array( static::current_instance(), 'action_shortcode' ) );
		if ( static::get_config('enable_filter_atts') ) {
			add_filter( "shortcode_atts_" . static::get_config('short_code_tag'), array( static::current_instance(), 'filter_shortcode_atts' ), 10, 3 );
		}
	}

	/**
	 * Method to find if filter atts is enabled
	 * 
	 * @return void No return value
	 */
	static public function is_filter_atts() {
		return ( empty( static::get_config('enable_filter_atts') ) ? FALSE : TRUE );
	}

	/**
	 * WordPress action method for processing the shortcode
	 * 
	 * The method processes the shortcode command
	 * 
	 * @return string Returns the results of the shortcode
	 */
	static public function action_shortcode( $atts, $content='' ) {
		// Holder
		extract( shortcode_atts( 
			array(
				'id' => '',
				'title' => '',
				'slug' => '',
			),
			$atts,
			static::shortcode_tag()
		) );
		return $contents;
	}

	/**
	 * WordPress filter method for processing the shortcode atts
	 * 
	 * The method processes the shortcode atts
	 * 
	 * @return $out Returns the shortcode_atts results
	 */
	static public function filter_shortcode_atts( $out, $pairs, $atts ) {
		// Holder
		return $out;
	}

}
