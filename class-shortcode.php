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

	/** Used as the ID of the instance */
	const ID = 'wpp-shortcode';

	/** Used to store the form prefex */
	const HTML_FORM_PREFIX = 'wpp_shortcode'; // should only use [a-z0-9_]

	/** Used to store the form prefex */
	const HTML_CLASS_PREFIX = 'wpp-shortcode-'; // should only use [a-z0-9_-]

	/** Used to store the form prefex */
	const HTML_ID_PREFIX = 'wpp-shortcode-'; // should only use [a-z0-9_-]

	/**
	 * Initialization point for the static class
	 * 
	 * @return void No return value
	 */
	static public function init( $config = array(), $merge = FALSE ) {
		if ( static::is_initialized() ) { 
			return; 
		}
		parent::init( static::array_merge_nested( 			
			array( //Default config
				'shortcode_tag' => static::ID,
				'enable_filter_atts' => FALSE,
			),
			(array) $config //Added config
		), $merge );
	}

	/**
	 * Method called after initialized is set to true
	 * 
	 * @return void No return value
	 */
	static public function init_after_initialized() {
		add_shortcode( static::shortcode_tag(), array( static::current_instance(), 'action_shortcode' ) );
		if ( static::is_filter_atts() ) {
			add_filter( "shortcode_atts_" . static::shortcode_tag(), array( static::current_instance(), 'filter_shortcode_atts' ), 10, 3 );
		}
	}

	/**
	 * Method to find if filter atts is enabled
	 * 
	 * @return void No return value
	 */
	static public function shortcode_tag() {
		$config = static::get_config();
		return ( empty( $config[ 'shortcode_tag' ] ) ? static::ID : $config[ 'shortcode_tag' ] );
	}

	/**
	 * Method to find if filter atts is enabled
	 * 
	 * @return void No return value
	 */
	static public function is_filter_atts() {
		$config = static::get_config();
		return ( empty( $config[ 'enable_filter_atts' ] ) ? FALSE : TRUE );
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
