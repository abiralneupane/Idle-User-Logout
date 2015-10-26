<?php
/*
Plugin Name: Idle User Logout
Plugin URI: http://wordpress.org/extend/plugins/idle-user-logout/
Description: This plugin automatically logs out the user after a period of idle time. The time period can be configured from admin end.
Version: 2.2
Author: Abiral Neupane
Author URI: http://abiralneupane.com.np
*/

global $iul,$admin_iul,$dashboard_iul,$iul_action;
class IDLE_USER_LOGOUT{
	function __construct(){
		register_activation_hook(__FILE__,array($this,'iul_activate'));
		register_uninstall_hook(__FILE__,'iul_deactivate');
		add_image_size( 'popup-image', 545, 220, true );
		add_action('wp_enqueue_scripts',array($this,'add_iul_scripts') );
		add_action('admin_enqueue_scripts',array($this,'add_iul_scripts') );
	}

	static function iul_activate() {
		if( get_option( 'iul_data' ) ) {
			update_option( 'iul_data', array('iul_idleTimeDuration'=>20, 'iul_disable_admin' => true) );		
		} else {
			add_option( 'iul_data', array('iul_idleTimeDuration'=>20, 'iul_disable_admin' => true ) );
		}
	}

	static function iul_deactivate() {
		delete_option( 'iul_data' );
	}

	function add_iul_scripts(){
		wp_register_script( 'jquery-idle',plugins_url('js/idle-timer.min.js',__FILE__), array('jquery'), '1.2.1', true );
		wp_register_script( 'uikit',plugins_url('js/uikit.min.js',__FILE__), array('jquery'), '1.2.1', true );		
		
		if(is_user_logged_in()){
			wp_enqueue_script( 'iul-script',plugins_url('js/script.js',__FILE__), array('jquery-idle','uikit'), '2.0', true );
			wp_enqueue_style( 'iul-style',plugins_url('css/style.css',__FILE__));
		}
	}
}

require(dirname(__FILE__).'/inc/admin/admin_menu.php');
require(dirname(__FILE__).'/inc/admin/dashboard.php');
require(dirname(__FILE__).'/inc/iul_actions.php');

$iul = new IDLE_USER_LOGOUT();
$admin_iul = new IUL_ADMIN();
$dashboard_iul = new IUL_DASHBOARD();
$iul_action =  new IUL_ACTIONS();