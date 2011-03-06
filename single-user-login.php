<?php
/**
 * @package single-user-login
 * @author cosbeta
 * @version 1.0
 */
/*
Plugin Name: multi-login-checker
Plugin URI: http://www.vpnso.com/vpnblog/?p=1
Description: avoid multi-login
Version: 1.0
Author URI: http://www.vpnso.com
*/
 

if( !function_exists('single_user_login_install')){
	function single_user_login_install(){
			global $wpdb;
			$users  	= $wpdb->users ; 
			$sql = "ALTER TABLE  `".$users."` ADD  `uni_hash` VARCHAR( 80 ) NOT NULL"	;
			$wpdb->get_results($sql);
			
	}	
}
if( !function_exists('single_user_login_uid_create')){
	function single_user_login_uid_create($ID){
			global $wpdb;
			$users  	= $wpdb->users ;
			$randUID = md5(microtime().$_SERVER['REMOTE_ADD'] );
			$sql = "UPDATE  `".$users."` set  `uni_hash`='".$randUID."' WHERE user_login='".$ID."'"	;
			$wpdb->get_results($sql);
			setcookie("user_uni_uid", $randUID);  
			 
			
	}	
}
 
 
if( !function_exists('single_user_login_uid_check')){
	function single_user_login_uid_check(){
		global $wpdb;
		$users  	= $wpdb->users ;
		$user_uni_uid = $_COOKIE['user_uni_uid'];
		$sql = "SELECT  uni_hash FROM  `".$users."`  WHERE uni_hash='".$user_uni_uid."'"	;
		$getinfo = $wpdb->get_results($sql); 
		$logout_url = wp_logout_url( home_url() );
		if( ($getinfo[0]->uni_hash != $user_uni_uid  )&&(  is_user_logged_in() ) 
		){
			wp_clearcookie();
			do_action('wp_logout');
			nocache_headers();
			$redirect_to = home_url();
			wp_redirect($redirect_to);
			exit();
		}
	}
}
register_activation_hook( __FILE__, 'single_user_login_install' );
add_action('wp_login','single_user_login_uid_create');
add_action('init','single_user_login_uid_check');


 
?>
