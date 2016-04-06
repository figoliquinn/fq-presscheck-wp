<?php
/**
 * Plugin Name: FQ Press Check
 * Author: Steven Quinn <steven@figoliquinn.com>
 * Description: Allows for the remote checking if updates are required
 *
 */

if (!class_exists('BFIGitHubPluginUpdater'))
{
	require_once( 'BFIGitHubPluginUploader.php' );
}

require_once( 'FQpresscheck.php' );

if ( is_admin() ) {
    new BFIGitHubPluginUpdater( __FILE__, 'stevenquinn', "presscheck-wp" );
}

// Add our endpoint on install
function install_fq_presscheck() {
	fq_presscheck_endpoint();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'install_fq_presscheck' );

/**
* Flush rewrite rules
*/
function uninstall_fq_presscheck() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'uninstall_fq_presscheck' );
 
 // Add our new endpoint for checking for updates
 function fq_presscheck_endpoint()
 {
	add_rewrite_endpoint('fq/update-check', EP_ROOT);
 }
 add_action( 'init', 'fq_presscheck_endpoint' );
 
 
 // The routing for our endpoint
function fq_presscheck_routes($query) 
{		
	switch ($query->request)
	{
		case 'fq/update-check':
			fq_check_updates();
			break;
			
		default:
			// No default
	}
}

add_action('parse_request', 'fq_presscheck_routes');


/**
 * Where the magic happens
 * Checks for updates
 */
function fq_check_updates()
{
	$username = $_REQUEST['username'];
	$password = $_REQUEST['password'];
	
	// Make sure a username and password are provided
	if (!empty($username) && !empty($password))
	{
		// Try logging in
		$credentials = array(
			'user_login' => $username,
			'user_password' => $password	
		);
		
		$user = wp_signon($credentials, FALSE);
		
		if ( !is_wp_error($user) )
		{
			// See if it's an admin
			foreach ($user->roles as $role)
			{
				if ($role == 'administrator')
				{
					$presscheck = new FQpresscheck();
					echo json_encode($presscheck->checkUpdates());
					die();
				}				
			}
		}
	}
}



