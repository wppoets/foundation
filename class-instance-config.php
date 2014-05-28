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
abstract class Instance_Config extends Static_Config {

	/**
	 * Initialization point for the configuration
	 * 
	 * @return void No return value
	 */
	static public function init_config() {
		parent::init_config();
		$config = static::get_config_instance();
		$config::set_default( 'text_domain', '' );
		$config::set_default( 'asset_version', '' );
		$config::set_default( 'config_group_global', array( 'admin_controller', 'admin_page', 'content_type', 'meta_box', 'shortcode' ) );
		$config::set_default( 'config_group_instance', array( 'scripts', 'styles' ) );
	}

	static public function __callStatic( $name, $arguments ) {
		$params = explode( '_', $name, 2 );
		if ( empty( $params[0] ) || empty( $params[1] ) || empty( $arguments[0] ) ) {
			return;
		}

		$config = static::get_config_instance();
		$verb = $params[0];
		$group = $params[1];

		$global_groups = $config::get( 'config_group_type_global' );
		$is_global_group = in_array( $group, $global_groups );
		$instance_groups = $config::get( 'config_group_type_instance' );
		$is_instance_group = in_array( $group, $instance_groups );

		if ( ! $is_global_group || ! $is_instance_group ) {
			return;
		}
		if ( $is_instance_group && empty( $arguments[1] ) ) { // Instance group commands should have a 3rd argument
			return;
		}
		if ( ! in_array( $verb, array( 'add', 'config', 'remove', 'get', 'set' ) ) ) {
			return;
		} 

		switch( $verb ) { 
			case 'add':
				$values = $is_global_group ? $config::get( 'config__' . $group ) : $config::get( 'config__' . $group, $arguments[1] );
				if ( ! is_array( $values ) ) {
					$values = array();
				}
				if ( ! in_array( $arguments[0], $values ) ) {
					$values[] = $arguments[0];
					$is_global_group ? $config::set( 'config__' . $group, $values ) : $config::set( 'config__' . $group, $values, $arguments[1] );
				}
				return $arguments[0];
				break;
			case 'config':
				if ( $is_instance_group ) {
					return FALSE; // TODO: need to return a warning, sense you cant set config options to an instance group
				}
				if ( empty( $arguments[2] ) ) {
					return FALSE; // TODO: return required field missing error?
				}
				$config::set( $arguments[1], $arguments[2], $arguments[0] );
				return TRUE;
				break;
			case 'remove':
				$values = $is_global_group ? $config::get( 'config__' . $group ) : $config::get( 'config__' . $group, $arguments[1] );
				if ( ! is_array( $values ) ) {
					return;
				}
				$remove_key = array_search($arguments[0], $values);
				if ( $remove_key !== FALSE && $remove_key !== NULL ) {
					unset( $values[ $remove_key ] );
					$values = array_values( $values );
					$is_global_group ? $config::set( 'config__' . $group, $values ) : $config::set( 'config__' . $group, $values, $arguments[1] );
				}
				return;
				break;
			case 'get':
				$values = $is_global_group ? $config::get( 'config__' . $group ) : $config::get( 'config__' . $group, $arguments[1] );
				if ( ! is_array( $values ) ) {
					$values = array();
				}
				return $values;
				break;
			case 'set':
				if ( empty( $arguments[1] ) ) {
					return FALSE; // TODO: return required field missing error?
				}
				if ( ! is_array( $arguments[0] ) ) {
					return FALSE; // TODO: return must be an array error?
				}
				$is_global_group ? $config::set( 'config__' . $group, $arguments[0] ) : $config::set( 'config__' . $group, $arguments[0], $arguments[1] );
				break;
		}
	}


}
