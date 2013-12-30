<?php

/**
 * Subclass of KM_Updates for themes
 *
 * @package KM_Updates
 * @since 4.6.3
 * @author John Gera
 * @copyright Copyright (c) 2013  John Gera, George Krupa, and Kreatura Media Kft.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 */

require_once 'class.km.autoupdate.php';

class KM_ThemeUpdates extends KM_Updates {

	public function __construct($config) {

		// Set up auto updater
		parent::__construct($config);

	}
}
