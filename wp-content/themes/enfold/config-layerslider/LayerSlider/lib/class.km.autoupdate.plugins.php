<?php

/**
 * Subclass of KM_Updates for plugins
 *
 * @package KM_Updates
 * @since 4.6.3
 * @author John Gera
 * @copyright Copyright (c) 2013  John Gera, George Krupa, and Kreatura Media Kft.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 */

require_once 'class.km.autoupdate.php';

class KM_PluginUpdates extends KM_Updates {

	public function __construct($config) {

		// Set up auto updater
		parent::__construct($config);

		// Hook into Plugins API
		add_filter('pre_set_site_transient_update_plugins', array(&$this, 'set_update_transient'));
		add_filter('plugins_api_result', array(&$this, 'set_updates_api_results'), 10, 3);
	}
}
