<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    wp_discourse_sync
 * @subpackage wp_discourse_sync/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    wp_discourse_sync
 * @subpackage wp_discourse_sync/public
 * @author     Your Name <email@example.com>
 */
class wp_discourse_sync_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wp_discourse_sync    The ID of this plugin.
	 */
	private $wp_discourse_sync;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $wp_discourse_sync       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wp_discourse_sync, $version ) {

		$this->wp_discourse_sync = $wp_discourse_sync;
		$this->version = $version;

	}

}
