<?php

class AuthorizationController{
	
	const taxonomy = '1010data';
	static $tenten;
	static $message;
	
	static function init(){
		add_action('init', array(get_class(), 'authorization_register_taxonomy'));
		//add_action('init', array(get_class(), 'api_check'), 100);
		
		add_filter('the_content', array(get_class(), 'content_checking'));
		add_filter('the_title', array(get_class(), 'title_checking'));
		add_action('wp_enqueue_scripts', array(get_class(), 'enqueue_scripts'));
		
		//form submitted
		add_action('init', array(get_class(), 'form_submitted'), 0);
	}
	
	
	/**
	 * Enqueue scripts (css/js)
	 */
	static function enqueue_scripts(){
		
		
		/*
		wp_enqueue_script('jquery');
		wp_register_script('revealmodal_tenten_js', WP1010DATA_URL . 'assets/reveal/jquery.reveal.js', array('jquery'));
		wp_enqueue_script('revealmodal_tenten_js');		
		wp_register_style('revealmodal_tenten_css', WP1010DATA_URL . 'assets/reveal/reveal.css');
		wp_enqueue_style('revealmodal_tenten_css');
		
		
		wp_register_script('leanmodal_tenten_js', WP1010DATA_URL . 'assets/lean/jquery.leanModal.min.js', array('jquery'));
		wp_enqueue_script('leanmodal_tenten_js');
		
		wp_register_script('tenten_driver_js', WP1010DATA_URL . 'js/tenten.js', array('jquery'));
		wp_enqueue_script('tenten_driver_js');
		*/
		
		wp_register_style('tenten_driver_css', WP1010DATA_URL . 'css/tenten.css');
		wp_enqueue_style('tenten_driver_css');
		
	}
	
	
	static function api_check(){
		$tenten = new TenTenDataDotCom('mhasan', '1010data123');
		
	//	$tenten->unset_session();
		
		$membership = $tenten->get_membership();
	}
	
	/**
	 * register taxonomy (tag)
	 * 
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
	
	
	/**
	 * parsing links to get the tenten information
	 * */
	static function content_checking($content){
		global $post;
		/*		
		$modal_markup = '<div id="myModal" class="reveal-modal">
						     <h1>Modal Title</h1>
						     <p>Any content could go in here.</p>
						     <a class="close-reveal-modal">&#215;</a>
						</div>';
						
		
		//return $content . $modal_markup;
		$lean_modal = '<div><a href="#myModal">Click Here</a></div>';
		*/
		
		$tenten_groups = wp_get_object_terms($post->ID, self::taxonomy, array('fields' => 'names'));

		if($tenten_groups){
			
			if(self::user_credentails_exists()){
				self::$tenten = new TenTenDataDotCom($_SESSION['pretenten']['uid'], $_SESSION['pretenten']['pswd']);
				$allowed_groups = self::get_allowed_groups();
				
			//	var_dump($allowed_groups);
			//	var_dump($tenten_groups);
				
				if(is_array($allowed_groups)){
					foreach($tenten_groups as $group){
						if(in_array($group, $allowed_groups)){
							return $content;
						}
					}
					
					$content = "403 forbidden";
				}
				else{
					$content = self::$message;
				}				
			}
			else{
				$content = self::get_modal_form($post->ID);
			}			
		}
		
		return $content;
		
	}
	
	
	//modal form template
	static function get_modal_form($post_id){
		ob_start();
		include WP1010DATA_DIR . '/includes/modal-form.php';
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	
	
	//check if the user credential exists in session
	static function user_credentails_exists(){
		$uid = self::get_session('uid');
		$pswd = self::get_session('pswd');
		
		if(empty($uid) || empty($pswd)){
			return false;
		}
		else{
			return true;
		}
	}
	
	
	static function get_allowed_groups(){
		$groups = self::get_session('groups');
		
		if(empty($groups)){
			$membership = self::$tenten->get_membership();
			if((string) $membership->rc == '0'){
				$groups = (string)$membership->user[0]->attributes()['groups'];
				$groups = explode(' ', $groups);
				self::set_session(array('groups' => $groups));
			}
			else{
				self::$message = (string)$membership->msg;
			}
		}
		
		return $groups;
		
	}
	
	
	
	//title chekcing
	static function title_checking($title){
		return $title;
	}
		
	
	//for is saved and 
	static function form_submitted(){
		if($_POST['modal-form-sbumitted'] == 'y'){
			if(empty($_POST['tenten']['uid']) || empty($_POST['tenten']['pswd'])){
				self::$message = "Both fields are mendatory";
			}
			else{
				self::set_session(array(
					'uid' => $_POST['tenten']['uid'],
					'pswd' => $_POST['tenten']['pswd']			
				));
			}
		}
	}
	
	
	//session save for pretenten
	static function set_session($data = array()){
		if(!empty($data)){
			foreach($data as $key => $value){
				$_SESSION['pretenten'][$key] = $value;
			}
		}
	}
	
	
	static function get_session($key){
		return $_SESSION['pretenten'][$key];
	}
	
}