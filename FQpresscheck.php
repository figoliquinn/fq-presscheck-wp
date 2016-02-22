<?php

// Our main class
class FQpresscheck {
	
	/**
	 * Check updates for core, plugins, and themes
	 *
	 * @return obj
	 */
	public function checkUpdates()
	{
		$updates = new stdClass();
		$updates->type = "wordpress";
		$updates->core = $this->checkCoreUpdates();
		$updates->themes = $this->checkThemeUpdates();
		$updates->plugins = $this->checkPluginUpdates();
		
		return $updates;
	}
	
	/**
	 * Check updates for core
	 *
	 * @return obj
	 */
	private function checkCoreUpdates()
	{
		// Check for updates
		$updateCore = get_site_transient( "update_core" ); // get information of updates
		
		// Store whether or not there are updates and return it
		$item = new stdClass();
		
		if ($updateCore->updates[0]->response == 'upgrade')
		{
			$item->title = 'CORE';
			$item->update = ($updateCore->updates[0]->response == 'upgrade') ? TRUE : FALSE;
			$item->currentVersion = $updateCore->version_checked;
			$item->newVersion = $updateCore->updates[0]->version;
		}
		
		return $item;
	}
	
	
	/**
	 * Check updates for themes
	 * 
	 * @return array
	 */
	private function checkThemeUpdates()
	{
		// force WP to check for theme updates
		do_action( "wp_update_themes" ); 
		
		// get information of updates
		$updateThemes = get_site_transient( 'update_themes' );
		$updates = array();
		
		if (isset($updateThemes->response))
		{
			foreach($updateThemes->response as $theme)
			{
				$item = new stdClass();
				$item->title = $theme['theme'];
				$item->update = TRUE;
				$item->currentVersion = $updateThemes->checked[$theme['theme']];
				$item->newVersion = $theme['new_version'];
				
				$updates[] = $item;
			}
		}
		
		return $updates;
	}
	
	
	/**
	 * Check for plugin updates
	 *
	 * @return array
	 */
	private function checkPluginUpdates() 
	{
		// force WP to check plugins for updates
		do_action( "wp_update_plugins" );
		 
		// get information of updates
		$updatePlugins = get_site_transient( 'update_plugins' ); 
		$updates = array();
		
		if (isset($updatePlugins->response))
		{
			foreach ($updatePlugins->response as $plugin)
			{
				$item = new stdClass();
				$item->title = $plugin->slug;
				$item->update = TRUE;
				$item->currentVersion = $updatePlugins->checked[$plugin->plugin];
				$item->newVersion = $plugin->new_version;
				
				$updates[] = $item;
			}
		}
		
		return $updates;
	}
}

