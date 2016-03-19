<?php
/*
Plugin Name: Idle User Logout
Plugin URI: http://wordpress.org/extend/plugins/idle-user-logout/
Description: This plugin automatically logs out the user after a period of idle time. The time period can be configured from admin end.
Version: 2.2
Author: Abiral Neupane
Author URI: http://abiralneupane.com.np
*/

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

if ( !defined( 'IUL_PATH' ) ) {
    define( 'IUL_PATH', plugin_dir_path( __FILE__ ) );
}


global $iul,$admin_iul,$dashboard_iul,$iul_action;
class IDLE_USER_LOGOUT{
	function __construct(){
		register_activation_hook(__FILE__,array($this,'iul_activate'));
		add_image_size( 'popup-image', 545, 220, true );
		add_action('wp_enqueue_scripts',array($this,'add_iul_scripts') );
		add_action('admin_enqueue_scripts',array($this,'add_iul_scripts') );
		add_action('init',array($this,'iul_check_last_session') );
        add_action('admin_init',array($this,'iul_check_last_session') );
	}

	static function iul_activate() {
		update_option( 'iul_data', array('iul_idleTimeDuration'=>20, 'iul_disable_admin' => true) );
        update_option('iul_behavior',array());
	}

	function add_iul_scripts(){
		wp_register_script( 'jquery-idle',plugins_url('js/idle-timer.min.js',__FILE__), array('jquery'), '1.2.1', true );
		wp_register_script( 'uikit',plugins_url('js/uikit.min.js',__FILE__), array('jquery'), '1.2.1', true );		
		
		if(is_user_logged_in()){
			wp_register_script( 'iul-active-worker',plugins_url('js/active-worker.js',__FILE__), array('jquery-idle','uikit'), '2.0', true );
			wp_enqueue_script( 'iul-script',plugins_url('js/script.js',__FILE__), array('iul-active-worker'), '2.0', true );
			wp_enqueue_style( 'iul-style',plugins_url('css/style.css',__FILE__));
		}
	}


	function iul_check_last_session(){
        if ( defined( 'DOING_AJAX' ) &&  DOING_AJAX )  {
            return;
        }

        if( is_user_logged_in() ):
            $current_time = date('H:i:s');
            $iul_data 	=  get_option('iul_data');
            $iul_disable_admin = isset($iul_data['iul_disable_admin'])?$iul_data['iul_disable_admin']:false;
            
            if( !is_admin() || ( is_admin() && !$iul_disable_admin ) ):
            	if( is_user_idle() ):
					//$last_active_time = get_user_meta(get_current_user_id(),'last_active_time',true);
            		iul_execute_behavioural_action();
                    //update_user_meta(get_current_user_id(),'last_active_time',date('H:i:s'));
                    do_action( 'uil_after_logout' );
                else:
                    update_user_meta(get_current_user_id(),'last_active_time',$current_time);
                endif;
            else:
            	update_user_meta(get_current_user_id(),'last_active_time',$current_time);
            endif;
        endif;
    }

}

require IUL_PATH.'/inc/iul_functions.php';
require IUL_PATH.'/inc/admin/admin_menu.php';
require IUL_PATH.'/inc/admin/dashboard.php';
require IUL_PATH.'/inc/iul_actions.php';


$iul = new IDLE_USER_LOGOUT();
$admin_iul = new IUL_ADMIN();
$dashboard_iul = new IUL_DASHBOARD();
$iul_action =  new IUL_ACTIONS();