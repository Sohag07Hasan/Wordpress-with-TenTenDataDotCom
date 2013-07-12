<?php

class AuthorizationController{
	
	const taxonomy = '1010data';
	
	static function init(){
		add_action('init', array(get_class(), 'authorization_register_taxonomy'));
		add_action('init', array(get_class(), 'api_check'), 100);
	}
	
	static function api_check(){
		$tenten = new TenTenDataDotCom('mhasan', '1010data123');
		$tenten->login();
		exit;
	}
	
	/**
	 * register taxonomy
	 * not hierarchy
	 * */
	static function authorization_register_taxonomy(){
		$labels = array(
				'name'                       => _x( '1010 Groups', '1010data' ),
				'singular_name'              => _x( '1010 Group', 'taxonomy singular name' ),
				'search_items'               => __( 'Search 1010 Groups' ),
				'popular_items'              => __( 'Popular 1010 Groups' ),
				'all_items'                  => __( 'All 1010 Groups' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit 1010 Group' ),
				'update_item'                => __( 'Update 1010 Group' ),
				'add_new_item'               => __( 'Add New 1010 Group' ),
				'new_item_name'              => __( 'New 1010 Group Name' ),
				'separate_items_with_commas' => __( 'Separate 1010 Groups with commas' ),
				'add_or_remove_items'        => __( 'Add or remove 1010 Groups' ),
				'choose_from_most_used'      => __( 'Choose from the most used 1010 Groups' ),
				'not_found'                  => __( 'No 1010 Groups found.' ),
				'menu_name'                  => __( '1010 Groups' ),
		);
		
		$args = array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array( 'slug' => '1010_Group' ),
		);
				
		register_taxonomy(self::taxonomy, array_values(get_post_types()), $args );
	}
	
}