<?php namespace WPP\Foundation_Namespace_Base;
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

	/** Used to set the plugins ID */
	const ID = 'wpp-plugin';

	/** Used to set the default option id */
	const DEFAULT_OPTION_KEY = 'wpp_plugin_options';

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

}
