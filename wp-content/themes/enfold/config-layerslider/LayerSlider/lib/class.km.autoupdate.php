<?php

/**
 * Automatic Updater Class
 *
 * Receive updates for plugins and themes on-the-fly from
 * self-hosted repositories using the WordPress Updates API.
 *
 * @package KM_Updates
 * @since 4.6.3
 * @author John Gera
 * @copyright Copyright (c) 2013  John Gera, George Krupa, and Kreatura Media Kft.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 */


class KM_Updates {

	/**
	 * Adds additional one minute caching between WP API calls
	 * to prevent parallel requests of self-hosted repository.
	 */
	const TIMEOUT = 60;

	/**
	 * @var string $repo The URL of self-hosted repository
	 * @access protected
	 */
	protected $repo;


	/**
	 * @var string $slug The slug of the plugin or theme.
	 * @access protected
	 */
	protected $slug;


	/**
	 * @var string $base The base path of plugin or theme.
	 * @access protected
	 */
	protected $base;


	/**
	 * @var string $version The version number of plugin or theme.
	 * @access protected
	 */
	protected $version;


	/**
	 * @var string $channel Relese channel to look for updates in.
	 * @access protected
	 */
	protected $channel;


	/**
	 * @var string $license Envato Item Purchase Code of plugin or theme.
	 * @access protected
	 */
	protected $license;


	/**
	 * @var object $data Received update data.
	 * @access protected
	 */
	protected $data;


	/**
	 * @var string $option Option key of WP Options API to save update info
	 * @access protected
	 */
	protected $option;



	/**
	 * Init class and set up config for update checking
	 *
	 * @since 4.6.3
	 * @access public
	 * @param array $config Config for setting up auto updates
	 * @return void
	 */
	public function __construct($config = array()) {

		// Get and check params
		extract($config, EXTR_SKIP);
		if(!isset($repo, $root, $version, $channel, $license)) {
			wp_die('Missing params in $config for KM_Updates constructor');
		}

		// Build config
		$this->repo = $config['repo'];
		$this->slug = basename(dirname($config['root']));
		$this->base = plugin_basename($config['root']);
		$this->version = $config['version'];
		$this->channel = $config['channel'];
		$this->license = $config['license'];
		$this->option = strtolower($this->slug) . '_update_info';
	}



	/**
	 * Adds self-hosted updates for site transients.
	 *
	 * @since 4.6.3
	 * @access public
	 * @param object $transient WP plugin updates site transient
	 * @return object $transient WP plugin updates site transient
	 */
	public function set_update_transient($transient) {

		$this->_check_updates();

		if(!isset($transient->response)) {
			$transient->response = array();
		}

		if(is_object($this->data->basic) && !empty($this->data->basic)) {
			if(version_compare($this->version, $this->data->basic->version, '<')) {

				$this->data->basic->new_version = $this->data->basic->version;
				$transient->response[$this->base] = $this->data->basic;
			}
		}

		return $transient;
	}



	/**
	 * Adds self-hosted updates for WP Updates API.
	 *
	 * @since 4.6.3
	 * @access public
	 * @param object $result Result object containing update info
	 * @param string $action WP Updates API action
	 * @param object $args Object containing additional information
	 * @return object $result Result object containing update info
	 */
	public function set_updates_api_results($result, $action, $args) {

		$this->_check_updates();

		if(isset($args->slug) && $args->slug == $this->slug && $action == 'plugin_information') {
			if(is_object($this->data->full) && !empty($this->data->full)) {
				$result = $this->data->full;
			}
		}

		return $result;
	}



	/**
	 * Check for update info.
	 *
	 * @since 4.6.3
	 * @access protected
	 * @return void
	 */
	protected function _check_updates() {

		// Get data
		if(empty($this->data)) {
			$data = get_option($this->option, false);
			$data = $data ? $data : new stdClass;
			$this->data = is_object($data) ? $data : maybe_unserialize($data);
		}

		// Just installed
		if(!isset($this->data->checked)) {
			$this->data->checked = time();
		}

		// Check for updates
		if($this->data->checked < time() - self::TIMEOUT) {

			$data = $this->_retrieve_update_info();

			if(isset($data->basic)) {
				$this->data->checked = time();
				$this->data->basic = $data->basic;
				$this->data->full = $data->full;
			}
		}

		// Save results
		update_option($this->option, $this->data);
	}



	/**
	 * Retrieves update info from self-hosted repository.
	 *
	 * @since 4.6.3
	 * @access protected
	 * @return object of update info
	 */
	protected function _retrieve_update_info() {

		global $wp_version;
		$data = new stdClass;

		// Build request
		$request = wp_remote_post($this->repo, array(
			'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
			'body' => array(
				'slug' => $this->slug,
				'version' => $this->version,
				'channel' => $this->channel,
				'license' => $this->license,
			)
		));

		if(!is_wp_error($request)) {
			if($response = unserialize($request['body'])) {
				$data = $response;
			}
		}

		return $data;
	}
}
