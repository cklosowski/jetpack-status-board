<?php
/*
Plugin Name: Jetpack - Status Board
Plugin URI: http://kungfugrep.com
Description: Creates an endpoint for your Jetpack stats for use on the Status Board iPad app
Version: 1.1.1
Author: Chris Klosowski
Author URI: http://kungfugrep.com
License: GPLv2
*/



add_action( 'init', 'jssb_add_endpoint' );
function jssb_add_endpoint( $rewrite_rules ) {
	add_rewrite_endpoint( 'jssb-stats', EP_ALL );
}

register_activation_hook( __FILE__, 'jssb_activation_tasks' );
function jssb_activation_tasks() {
	// Flush the rules so the jssb-stats endpoint is found
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'jssb_deactivation_tasks' );
function jssb_deactivation_hook() {
	// Flush the rules since we no longer have the endpoint
	flush_rewrite_rules();
}


add_filter( 'query_vars', 'jssb_query_vars' );
function jssb_query_vars( $vars ) {
	$vars[] = 'jssb-verify';

	return $vars;
}

add_action( 'template_redirect', 'jssb_process_request', -1 );
function jssb_process_request() {
	global $wp_query;
	if ( ! isset( $wp_query->query_vars['jssb-stats'] ) )
		return;

	if ( ! isset( $wp_query->query_vars['jssb-verify'] ) ) {
		jssb_output( array( 'graph' => 
								array( 'title' => 'Jetpack Stats Error', 
										'error' => 
											array( 'message' => 'No Verification Token', 
													'detail' => 'Please use the supplied link in wp-admin to add this graph to Status Board' ) ) ) );
	}

	if ( $wp_query->query_vars['jssb-verify'] && $wp_query->query_vars['jssb-verify'] != sha1( wp_salt() ) ) {
		jssb_output( array( 'graph' => 
								array( 'title' => 'Jetpack Stats Error', 
										'error' => 
											array( 'message' => 'Invalid Verification Token', 
													'detail' => 'Please use the supplied link in wp-admin to add this graph to Status Board' ) ) ) );
	}

	$stats = array();
	$days = 7;
	while ( $days >= 0 ) {
		$end 	= ( $days > 0 ) ? date( 'Y-m-d', strtotime( '-' . $days . 'day' ) ) : date( 'Y-m-d' );
		$daily_visits = stats_get_csv( 'views', array( 'days' => 1, 'end' => $end ) );

		$views = ( ! empty( $daily_visits[0]['views'] ) ) ? $daily_visits[0]['views'] : 0;
		$stats[] = array( 'title' => date( 'n\/j', strtotime( $end ) ), 'value' => $views );
		$days--;
	}

	$data = array();
	$data['graph']['title'] = get_bloginfo( 'name' );
	$data['graph']['total'] = true;
	$data['graph']['datasequences'][] = array( 'title' => __( 'Daily Views', 'js-statusboard-txt' ), 'datapoints' => $stats );

	jssb_output( apply_filters( 'jssb_output', $data ) );
}

function jssb_output( $output ) {
	$ob_status = ob_get_level();
	while ( $ob_status > 0 ) {
		ob_end_clean();
		$ob_status--;
	}
	
	header( 'Content-Type: application/json' );
	echo json_encode( $output );
	exit;
}

add_action( 'jetpack_admin_menu', 'jssb_add_menu' );
function jssb_add_menu() {
	global $jssb_plugin_hook;
	$jssb_plugin_hook = add_submenu_page( 'jetpack', __( 'Status Board', 'js-statusboard-txt' ), __( 'Status Board', 'js-statusboard-txt' ), 'view_stats', 'add-jssb-stats', 'jssb_add_to_statusboard' );
}



function jssb_add_to_statusboard() {
    if ( empty( $_GET['page'] ) && $_GET['page'] )
		return false;
	
	$key = sha1( wp_salt() );
	$sb_url = get_bloginfo( 'url' ) . '/jssb-stats/?jssb-verify=' . $key;
	?>
	<div id="icon-themes" class="icon32"></div><h2><?php _e( 'Jetpack Status Board', 'js-statusboard-txt' ); ?></h2>
		<a class="button secondary" id="sbsales" href="panicboard://?url=<?php echo urlencode( $sb_url ); ?>&panel=graph"><?php _e( 'Add Jetpack Stats to Status Board', 'js-statusboard-txt' ); ?></a>
	</div>
	<?php
}

function jssb_help_menu( $contextual_help, $screen_id, $screen ) {
	global $jssb_plugin_hook;
	if ($screen_id == $jssb_plugin_hook) {
		$contextual_help = __( 'This plugin allows you to view your Jetpack stats on the Status Board App for iPads. If you are having any issues, please see the "Troubleshooting" tab.', 'js-statusboard-txt' );

		$key = sha1( wp_salt() );
		$sb_url = get_bloginfo( 'url' ) . '/jssb-stats/?jssb-verify=' . $key;
		$troubleshooting  = '<ol>';
		$troubleshooting .= '<li>' . __( 'To add the graph manually, copy this URL and add it to your Status Board iPad app.', 'js-statusboard-txt' ) . '<br />';
		$troubleshooting .= '<span id="jssb-key_url"><pre>' . $sb_url . '</pre></span></li>';
		$troubleshooting .= '<li>' . sprintf( __( 'If this gives you a \'404\' error, try clicking \'Save Changes\' on the <a href="%s">Permalinks</a> page to help flush the rewrite rules, and re-add the graph', 'js-statusboard-txt' ), admin_url( 'options-permalink.php' ) ) . '</li>';
		$troubleshooting .= '<li>' . sprintf( __( 'If you are still having problems, you can <a href="%s" target="new">submit a support request</a> and we can try and solve the issue.', 'js-statusboard-txt' ), 'http://wordpress.org/support/plugin/jetpack-status-board' ) . '</li>';
		$troubleshooting .= '</ol>';

		// Add if current screen is My Admin Page
		$screen->add_help_tab( array(
			'id'	=> 'jssb_troubleshooting',
			'title'	=> __( 'Troubleshooting', 'js-statusboard-txt' ),
			'content'	=> '<p>' . $troubleshooting . '</p>',
		) );
	}
	return $contextual_help;
}
add_filter( 'contextual_help', 'jssb_help_menu', 10, 3 );