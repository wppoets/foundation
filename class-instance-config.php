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
		// Global config
		static::set_default_config( 'text_domain', '', TRUE );
		static::set_default_config( 'asset_version', '', TRUE );
		static::set_default_config( 'config_group_type_global', array( 'admin_section', 'admin_page', 'content_type', 'meta_box', 'shortcode' ), TRUE );
		static::set_default_config( 'config_group_type_instance', array( 'scripts', 'styles' ), TRUE );
	}

	static public function __callStatic( $name, $arguments ) {
		$params = explode( '_', $name, 2 );
		if ( empty( $params[0] ) || empty( $params[1] ) ) {
			return;
		}

		$config = static::get_config_instance();
		$verb = $params[0];
		$group = $params[1];

		$global_groups = $config::get( 'config_group_type_global' );
		$is_global_group = in_array( $group, $global_groups );
		$instance_groups = $config::get( 'config_group_type_instance' );
		$is_instance_group = in_array( $group, $instance_groups );

		if ( ! $is_global_group && ! $is_instance_group ) {
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
				$add_class = isset($arguments[0]) ? ltrim( $arguments[0], '\\' ) : NULL;
				$instance_class = isset($arguments[1]) ? ltrim( $arguments[1], '\\' ) : NULL;
				if ( empty( $add_class ) ) {
					return;
				}
				if ( $is_instance_group && empty( $instance_class ) ) {
					return; // TODO trigger an error or notice?
				}
				$values = $is_global_group ? $config::get( 'config__' . $group ) : $config::get( 'config__' . $group, $instance_class );
				if ( ! is_array( $values ) ) {
					$values = array();
				}
				if ( ! in_array( $add_class, $values ) ) {
					$values[] = $add_class;
					$is_global_group ? $config::set( 'config__' . $group, $values ) : $config::set( 'config__' . $group, $values, $instance_class );
				}
				return $add_class;
				break;
			case 'remove':
				$remove_class = isset($arguments[0]) ? ltrim( $arguments[0], '\\' ) : NULL;
				$instance_class = isset($arguments[1]) ? ltrim( $arguments[1], '\\' ) : NULL;
				if ( empty( $remove_class ) ) {
					return;
				}
				if ( $is_instance_group && empty( $instance_class ) ) {
					return; // TODO trigger an error or notice?
				}
				$values = $is_global_group ? $config::get( 'config__' . $group ) : $config::get( 'config__' . $group, $instance_class );
				if ( ! is_array( $values ) ) {
					return;
				}
				$remove_key = array_search($remove_class, $values);
				if ( $remove_key !== FALSE && $remove_key !== NULL ) {
					unset( $values[ $remove_key ] );
					$values = array_values( $values );
					$is_global_group ? $config::set( 'config__' . $group, $values ) : $config::set( 'config__' . $group, $values, $instance_class );
				}
				return;
				break;
			case 'config':
				$config_class = isset($arguments[0]) ? ltrim( $arguments[0], '\\' ) : NULL;
				$config_key = isset($arguments[1]) ? $arguments[1] : NULL;
				$config_value = isset($arguments[2]) ? $arguments[2] : NULL;
				$instance_class = isset($arguments[3]) ? ltrim( $arguments[3], '\\' ) : NULL;
				if ( empty( $config_class ) || empty( $config_key ) ) {
					return; // TODO: trigger a warning? or error
				}
				if ( $is_instance_group && empty( $instance_class ) ) {
					return; // TODO trigger an error or notice?
				}
				$config::set( $config_key, $config_value, $config_class );
				return TRUE;
				break;
			case 'get':
				$instance_class = isset($arguments[0]) ? ltrim( $arguments[0], '\\' ) : NULL;
				if ( $is_instance_group && empty( $instance_class ) ) {
					return; // TODO trigger an error or notice?
				}
				$values = $is_global_group ? $config::get( 'config__' . $group ) : $config::get( 'config__' . $group, $instance_class );
				if ( ! is_array( $values ) ) {
					$values = array();
				}
				return $values;
				break;
			case 'set':
				$values = isset($arguments[0]) ? ltrim( $arguments[0], '\\' ) : NULL;
				$instance_class = isset($arguments[1]) ? ltrim( $arguments[1], '\\' ) : NULL;
				if ( empty( $values ) || ! is_array( $values ) ) {
					return FALSE; // TODO: return required field missing error?
				}
				if ( $is_instance_group && empty( $instance_class ) ) {
					return; // TODO trigger an error or notice?
				}
				$is_global_group ? $config::set( 'config__' . $group, $values ) : $config::set( 'config__' . $group, $values, $instance_class );
				break;
		}
	}

}
