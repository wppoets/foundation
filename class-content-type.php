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
abstract class Content_Type extends Child_Instance {

	/** Used as the ID of the instance */
	const ID = 'wpp-content-type';

	/** Used to store the post-type id*/
	const POST_TYPE = 'wpp-content-type';

	/** Used to store the singular form of the name */
	const NAME_SINGLE = 'Content';

	/** Used to store the plural form of the name */
	const NAME_PLURAL = 'Contents';

	/** Used to store the text domain */
	const TEXT_DOMAIN = '';

	/** Used by register_post_type args */
	const DESCRIPTION = '';

	/** Used by register_post_type args */
	const IS_PUBLIC = FALSE;

	/** Used by register_post_type args */
	const EXCLUDE_FROM_SEARCH = TRUE;

	/** Used by register_post_type args */
	const PUBLICLY_QUERYABLE = self::IS_PUBLIC;

	/** Used by register_post_type args */
	const SHOW_UI = self::IS_PUBLIC;

	/** Used by register_post_type args */
	const SHOW_IN_NAV_MENUS = self::IS_PUBLIC;

	/** Used by register_post_type args */
	const SHOW_IN_MENU = self::SHOW_UI;

	/** Used by register_post_type args */
	const SHOW_IN_ADMIN_BAR = self::SHOW_IN_MENU;

	/** Used by register_post_type args */
	const MENU_POSITION = NULL;

	/** Used by register_post_type args */
	const MENU_ICON = NULL;

	/** Used by register_post_type args */
	const CAPABILITY_TYPE = 'post';

	/** Used by register_post_type args */
	const MAP_META_CAP = TRUE;

	/** Used by register_post_type args */
	const HIERARCHICAL = FALSE;

	/** Used by register_post_type args, comma delimited */
	const SUPPORTS = 'title,editor';

	/** Used by register_post_type args, comma delimited */
	const TAXONOMIES = '';

	/** Used by register_post_type args */
	const HAS_ARCHIVE = FALSE;

	/** Used by register_post_type args */
	const PERMALINK_EPMASK = EP_PERMALINK;

	/** Used by register_post_type args */
	const QUERY_VAR = self::POST_TYPE;

	/** Used by register_post_type args */
	const CAN_EXPORT = TRUE;

	/** Used by register_post_type args */
	const SHOW_DASHBOARD = FALSE;

	/** Used to disable the quick edit box */
	const DISABLE_QUICK_EDIT = FALSE;

	/** Used to enable cascade delete */
	const ENABLE_CASCADE_DELETE = FALSE;

	/**
	 * Method called before initialized is set to true
	 * 
	 * @return void No return value
	 */
	static public function init_before_initialized() {
		parent::init_before_initialized();
		add_action( 'init', array( $static_instance, 'action_init' ) );
		if ( static::SHOW_DASHBOARD ) {
			add_action( 'dashboard_glance_items', array( $static_instance, 'action_dashboard_glance_items' ) );
		}
		if ( static::DISABLE_QUICK_EDIT ) {
			if ( 'post' === static::CAPABILITY_TYPE ) {
				add_action( 'post_row_actions', array( $static_instance, 'action_post_row_actions_disable_quick_edit' ), 10, 2 );
			} elseif( 'page' === static::CAPABILITY_TYPE ) {
				add_action( 'page_row_actions', array( $static_instance, 'action_post_row_actions_disable_quick_edit' ), 10, 2 );
			}
		}
		if ( static::ENABLE_CASCADE_DELETE ) {
			add_action( 'delete_post', array( $static_instance, 'action_delete_post_cascade' ) );
		}
	}

	/**
	 * Set method for the options
	 *  
	 * @param string|array $config An array containing the config
	 * @param boolean $merge Should the current config be merged in?
	 * 
	 * @return void No return value
	 */
	static public function set_config( $config, $merge = FALSE ) {
		return parent::set_config( wpp_array_merge_nested(
			array ( 
				'args' => array(
					'labels'             => array(
						'name'               => _x( ucfirst( strtolower( static::NAME_PLURAL ) ), 'post type general name', static::TEXT_DOMAIN ),
						'singular_name'      => _x( ucfirst( strtolower( static::NAME_SINGLE ) ), 'post type singular name', static::TEXT_DOMAIN ),
						'menu_name'          => _x( ucfirst( strtolower( static::NAME_PLURAL ) ), 'admin menu', static::TEXT_DOMAIN ),
						'name_admin_bar'     => _x( ucfirst( strtolower( static::NAME_SINGLE ) ), 'add new on admin bar', static::TEXT_DOMAIN ),
						'add_new'            => _x( 'Add New', strtolower( static::NAME_SINGLE ), static::TEXT_DOMAIN ),
						'add_new_item'       => __( 'Add New ' . ucfirst( strtolower( static::NAME_SINGLE ) ), static::TEXT_DOMAIN ),
						'new_item'           => __( 'New ' . ucfirst( strtolower( static::NAME_SINGLE ) ), static::TEXT_DOMAIN ),
						'edit_item'          => __( 'Edit ' . ucfirst( strtolower( static::NAME_SINGLE ) ), static::TEXT_DOMAIN ),
						'view_item'          => __( 'View ' . ucfirst( strtolower( static::NAME_SINGLE ) ), static::TEXT_DOMAIN ),
						'all_items'          => __( 'All ' . ucfirst( strtolower( static::NAME_PLURAL ) ), static::TEXT_DOMAIN ),
						'search_items'       => __( 'Search ' . ucfirst( strtolower( static::NAME_PLURAL ) ), static::TEXT_DOMAIN ),
						'parent_item_colon'  => __( 'Parent ' . ucfirst( strtolower( static::NAME_PLURAL ) ) . ':', static::TEXT_DOMAIN ),
						'not_found'          => __( 'No ' . strtolower( static::NAME_PLURAL ) . ' found.', static::TEXT_DOMAIN ),
						'not_found_in_trash' => __( 'No ' . strtolower( static::NAME_PLURAL ) . ' found in Trash.', static::TEXT_DOMAIN ),
					),
					'description'         => static::DESCRIPTION,
					'public'              => static::IS_PUBLIC,
					'exclude_from_search' => static::EXCLUDE_FROM_SEARCH,
					'publicly_queryable'  => static::PUBLICLY_QUERYABLE,
					'show_ui'             => static::SHOW_UI,
					'show_in_nav_menus'   => static::SHOW_IN_NAV_MENUS,
					'show_in_menu'        => static::SHOW_IN_MENU,
					'show_in_admin_bar'   => static::SHOW_IN_ADMIN_BAR,
					'menu_position'       => static::MENU_POSITION,
					'menu_icon'           => static::MENU_ICON,
					'capability_type'     => static::CAPABILITY_TYPE,
					'map_meta_cap'        => static::MAP_META_CAP,
					'hierarchical'        => static::HIERARCHICAL,
					'supports'            => static::SUPPORTS == '' ? array() : explode( ',', static::SUPPORTS ),
					'taxonomies'          => static::TAXONOMIES == '' ? array() : explode( ',', static::TAXONOMIES ),
					'has_archive'         => static::HAS_ARCHIVE,
					'permalink_epmask'    => static::PERMALINK_EPMASK,
					'query_var'           => static::QUERY_VAR,
					'can_export'          => static::CAN_EXPORT,
					'rewrite'             => array( 'slug' => static::POST_TYPE, -1 ),
				),
			),
			(array) $config //Added options
		), $merge );
	}

	/**
	 * WordPress action method for processing cascade delete of a post
	 * 
	 * @param int $post_id Returns the post id of the post being deleted
	 *
	 * @return string Returns the results of the shortcode
	 */
	static public function action_delete_post_cascade( $post_id ) {
		if ( static::POST_TYPE !== get_post_type( $post_id ) ) {
			return;
		}
		$cascade_posts = new \WP_Query( array(
			'post_type'      => get_post_types( array(), 'names' ), // Need to use get_post_types because 'any' doesnt work as expected
			'post_parent'    => $post_id,
			'post_status'    => 'any',
			'nopaging'       => TRUE,
			'posts_per_page' => -1,
			'fields'         => 'ids',
		) );
		$cascade_post_ids = ( empty( $cascade_posts->posts ) ? array() : $cascade_posts->posts );
		foreach ( (array) $cascade_post_ids as $cascade_post_id ) {
			wp_delete_post( $cascade_post_id, TRUE );
		}
	}

	/*
	 * WordPress action method for removing the quick edit option from the page listing
	 *
	 * ref: https://core.trac.wordpress.org/ticket/19343 
	 * If the ever fix it ( I agree its a hack and should be supported in the register post type to disable)
	 *
	 * @param array $actions Array of actions??
	 * @param object $post Post object
	 * @return array Updated actions array
	 */
	static public function action_post_row_actions_disable_quick_edit( $actions, $post ) {
		if ( static::POST_TYPE == $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	/**
	 * WordPress action method for the primary wordpress init
	 *  
	 * @return void No return value
	 */
	static public function action_init() {
		$config = static::get_config();
		register_post_type( static::POST_TYPE, $config[ 'args' ] );
	}
	
	/**
	 * WordPress action method for the dashboard glance items
	 *  
	 * @return void No return value
	 */
	static public function action_dashboard_glance_items() {
		$config = static::get_config();
		$labels = &$config['args']['labels'];
		$post_type_info = get_post_type_object( static::POST_TYPE );
		$num_posts = wp_count_posts( static::POST_TYPE );
		$num = number_format_i18n( $num_posts->publish );
		$text = _n( $labels['singular_name'], $labels['name'], intval( $num_posts->publish ) );
		print( '<li class="page-count ' . $post_type_info->name. '-count"><a href="edit.php?post_type=' . static::POST_TYPE . '">' . $num . ' ' . $text . '</a></li>' );
	}

	/**
	 * Method for inserting/updating post
	 */
	static public function insert_post( $post, $wp_error = FALSE ) {
		$post[ 'post_type' ] = static::POST_TYPE;
		return wp_insert_post( $post, $wp_error );
	}

	/**
	 * Method for deleting data
	 */
	static public function delete_post( $post_id, $force_delete = FALSE ) {
		return wp_delete_post( $post_id, $force_delete );
	}

	/**
	 * Method for getting the post
	 */
	static public function get_post( $id, $output = 'OBJECT', $filter = 'raw' ) {
		return get_post( $id, $output, $filter );
	}

	/**
	 * Method for getting multiple posts
	 */
	static public function get_posts( $args ) {
		$args[ 'post_type' ] = static::POST_TYPE;
		$posts_query = new \WP_Query( $args );
		$posts = ( empty( $posts_query->posts ) ? array() : $posts_query->posts );
		wp_reset_postdata();
		return $posts;
	}

}