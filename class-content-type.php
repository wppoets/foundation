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
abstract class Content_Type extends Child_Instance {

	/**
	 * Initialization point for the configuration
	 * 
	 * @return void No return value
	 */
	static public function init_config() {
		parent::init_config();
		static::set_default_config( 'post_type', '' );
		static::set_default_config( 'post_type_args', array() );
		static::set_default_config( 'post_type_lables', array() );
		static::set_default_config( 'post_type_name_single', '' );
		static::set_default_config( 'post_type_name_plural', '' );
		static::set_default_config( 'post_type_description', '' );
		static::set_default_config( 'post_type_is_public', FALSE );
		static::set_default_config( 'post_type_exclude_from_search', TRUE );
		static::set_default_config( 'post_type_publicly_queryable', static::get_default_config( 'post_type_is_public' ) );
		static::set_default_config( 'post_type_show_ui', static::get_default_config( 'post_type_is_public' ) );
		static::set_default_config( 'post_type_show_in_nav_menus', static::get_default_config( 'post_type_is_public' ) );
		static::set_default_config( 'post_type_show_in_menus', static::get_default_config( 'post_type_show_ui' ) );
		static::set_default_config( 'post_type_show_in_admin_bar', static::get_default_config( 'post_type_show_in_menus' ) );
		static::set_default_config( 'post_type_menu_position', NULL );
		static::set_default_config( 'post_type_menu_icon', NULL );
		static::set_default_config( 'post_type_capability', 'post' );
		static::set_default_config( 'post_type_map_meta_cap', TRUE );
		static::set_default_config( 'post_type_hierarchical', FALSE );
		static::set_default_config( 'post_type_supports', array('title','editor') );
		static::set_default_config( 'post_type_taxonomies', array() );
		static::set_default_config( 'post_type_has_archive', FALSE );
		static::set_default_config( 'post_type_permalink_epmask', EP_PERMALINK );
		static::set_default_config( 'post_type_query_var', static::get_default_config( 'post_type' ) );
		static::set_default_config( 'post_type_can_export', TRUE );
		static::set_default_config( 'disable_quick_edit', FALSE );
		static::set_default_config( 'enable_cascade_delete', FALSE );
		static::set_default_config( 'enable_dashboard_item_count', FALSE );
	}

	/**
	 * Init config check
	 * 
	 * @return void No return value
	 */
	static public function init_check_config( $settings = array() ) {
		parent::init_check_config( array_unique ( array_merge( $settings, array(
			'post_type',
		) ) ) );

		$post_args = static::get_config( 'post_type_args' );
		$post_args_changed = FALSE;
		$post_lables = static::get_config( 'post_type_lables' );
		$post_lables_changed = FALSE;

		if ( empty( $post_args ) ) {
			$post_args = array(
				'description'         => static::get_config( 'post_type_description' ),
				'public'              => static::get_config( 'post_type_is_public' ),
				'exclude_from_search' => static::get_config( 'post_type_exclude_from_search' ),
				'publicly_queryable'  => static::get_config( 'post_type_publicly_queryable' ),
				'show_ui'             => static::get_config( 'post_type_show_ui' ),
				'show_in_nav_menus'   => static::get_config( 'post_type_show_in_nav_menus' ),
				'show_in_menu'        => static::get_config( 'post_type_show_in_menus' ),
				'show_in_admin_bar'   => static::get_config( 'post_type_show_in_admin_bar' ),
				'menu_position'       => static::get_config( 'post_type_menu_position' ),
				'menu_icon'           => static::get_config( 'post_type_menu_icon' ),
				'capability_type'     => static::get_config( 'post_type_capability' ),
				'map_meta_cap'        => static::get_config( 'post_type_map_meta_cap' ),
				'hierarchical'        => static::get_config( 'post_type_hierarchical' ),
				'supports'            => static::get_config( 'post_type_supports' ),
				'taxonomies'          => static::get_config( 'post_type_taxonomies' ),
				'has_archive'         => static::get_config( 'post_type_has_archive' ),
				'permalink_epmask'    => static::get_config( 'post_type_permalink_epmask' ),
				'query_var'           => static::get_config( 'post_type_query_var' ),
				'can_export'          => static::get_config( 'post_type_can_export' ),
				'rewrite'             => array( 'slug' => static::get_config( 'post_type' ) ),
			);
			$post_args_changed = TRUE;
		} else if ( ! empty( $post_args[ 'capability_type' ] ) ) {
			static::set_default( 'post_type_capability', $post_args[ 'capability_type' ] );
		}

		if ( empty( $post_args['labels'] ) ) {
			if ( empty( $post_lables ) ) {
				$text_domain = static::get_config( 'text_domain' );
				$name_single = ucfirst( strtolower( static::get_config( 'post_type_name_single' ) ) );
				$name_plural = ucfirst( strtolower( static::get_config( 'post_type_name_plural' ) ) );
				$post_lables = array(
					'name'               => _x( $name_plural, 'post type general name', $text_domain ),
					'singular_name'      => _x( $name_single, 'post type singular name', $text_domain ),
					'menu_name'          => _x( $name_plural, 'admin menu', $text_domain ),
					'name_admin_bar'     => _x( $name_single, 'add new on admin bar', $text_domain ),
					'add_new'            => _x( 'Add New', $name_single, $text_domain ),
					'add_new_item'       => __( 'Add New ' . $name_single, $text_domain ),
					'new_item'           => __( 'New ' . $name_single, $text_domain ),
					'edit_item'          => __( 'Edit ' . $name_single, $text_domain ),
					'view_item'          => __( 'View ' . $name_single, $text_domain ),
					'all_items'          => __( 'All ' . $name_plural, $text_domain ),
					'search_items'       => __( 'Search ' . $name_plural, $text_domain ),
					'parent_item_colon'  => __( 'Parent ' . $name_plural . ':', $text_domain ),
					'not_found'          => __( 'No ' . strtolower( $name_plural ) . ' found.', $text_domain ),
					'not_found_in_trash' => __( 'No ' . strtolower( $name_plural ) . ' found in Trash.', $text_domain ),
				);
				$post_lables_changed = TRUE;
			}
			$post_args['labels'] = $post_lables;
			$post_args_changed = TRUE;
		}

		if ( $post_args_changed ) {
			static::set_config( 'post_type_args', $post_args );
		}
		if ( $post_lables_changed ) {
			static::set_config( 'post_type_lables', $post_lables );
		}

	}

	/**
	 * Method for after init has completed
	 * 
	 * @return void No return value
	 */
	static public function init_done() {
		parent::init_done();
		$current_instance = static::current_instance();

		add_action( 'init', array( $current_instance, 'action_init' ) );

		if ( static::get_config('enable_dashboard_item_count') ) {
			add_action( 'dashboard_glance_items', array( $current_instance, 'action_dashboard_glance_items' ) );
		}
		if ( static::get_config('disable_quick_edit') ) {
			if ( 'post' === static::get_config('post_type_capability') ) {
				add_action( 'post_row_actions', array( $current_instance, 'action_post_row_actions_disable_quick_edit' ), 10, 2 );
			} elseif( 'page' === static::get_config('post_type_capability') ) {
				add_action( 'page_row_actions', array( $current_instance, 'action_post_row_actions_disable_quick_edit' ), 10, 2 );
			}
		}
		if ( static::get_config('enable_cascade_delete') ) {
			add_action( 'delete_post', array( $current_instance, 'action_delete_post_cascade' ) );
		}
	}

	/**
	 * WordPress action method for processing cascade delete of a post
	 * 
	 * @param int $post_id Returns the post id of the post being deleted
	 *
	 * @return string Returns the results of the shortcode
	 */
	static public function action_delete_post_cascade( $post_id ) {
		if ( static::get_config( 'post_type' ) !== get_post_type( $post_id ) ) {
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
		if ( static::get_config( 'post_type' ) == $post->post_type ) {
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
		$config = static::get_config_instance();
		register_post_type( static::get_config( 'post_type' ), static::get_config( 'post_type_args' ) );
	}
	
	/**
	 * WordPress action method for the dashboard glance items
	 *  
	 * @return void No return value
	 */
	static public function action_dashboard_glance_items() {
		$post_type = static::get_config( 'post_type' );
		$labels = static::get_config( 'post_type_lables' );
		$post_type_info = get_post_type_object( $post_type );
		$num_posts = wp_count_posts( $post_type );
		$num = number_format_i18n( $num_posts->publish );
		$text = _n( $labels['singular_name'], $labels['name'], intval( $num_posts->publish ) );
		print( '<li class="page-count ' . $post_type_info->name. '-count"><a href="edit.php?post_type=' . $post_type . '">' . $num . ' ' . $text . '</a></li>' );
	}

	/**
	 * Method for inserting/updating post
	 */
	static public function insert_post( $post, $wp_error = FALSE ) {
		$post[ 'post_type' ] = static::get_config( 'post_type' );
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
		$args[ 'post_type' ] = static::get_config( 'post_type' );
		$posts_query = new \WP_Query( $args );
		$posts = ( empty( $posts_query->posts ) ? array() : $posts_query->posts );
		wp_reset_postdata();
		return $posts;
	}

}
