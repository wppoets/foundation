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
abstract class Plugin extends Root_Instance {

	/**
	 * Initialization point for the configuration
	 * 
	 * @return void No return value
	 */
	static public function init_config() {
		parent::init_config();
		$config = static::get_config_instance();
		$current_instance = static::current_instance();
		$config::set_default( 'enable_admin_sections', FALSE, $current_instance );
		$config::set_default( 'enable_admin_pages', FALSE, $current_instance );
		$config::set_default( 'enable_content_types', FALSE, $current_instance );
		$config::set_default( 'enable_meta_boxes', FALSE, $current_instance );
		$config::set_default( 'enable_shortcodes', FALSE, $current_instance );
	}

}
