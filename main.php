<?php
/**
 * Plugin Name: Wordpress with 1010data.com
 * author: Mahibul Hasan Sohag
 * Description: feeds of 1010data.com are shown in wordpress regular page and posts 
 * */

define("WP1010DATA_DIR", dirname(__FILE__) . '/');
define("WP1010DATA_URL", plugins_url('/', __FILE__));

session_start();

//data api
include WP1010DATA_DIR . 'classes/1010api.php';

//controlling classes
include WP1010DATA_DIR . 'classes/authorization-controller.php';
AuthorizationController::init();
