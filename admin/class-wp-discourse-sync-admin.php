<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    wp_discourse_sync
 * @subpackage wp_discourse_sync/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    wp_discourse_sync
 * @subpackage wp_discourse_sync/admin
 * @author     Your Name <email@example.com>
 */
class wp_discourse_sync_Admin {

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
	 * @param      string    $wp_discourse_sync       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wp_discourse_sync, $version ) {

		$this->wp_discourse_sync = $wp_discourse_sync;
		$this->version = $version;

	}

	/**
	 * callback the unpriv admin POST endpoint
	 *
	 * @since    1.0.0
	 */
	public function webhook_endpoint_callback() {
		status_header(200);

		$data = json_decode(file_get_contents('php://input'), true)['topic'];
		print_r($data);

		if(!empty($data)){
			$this->create_or_update_posts($data);
		}

    die;

	}


	/**
	 * Create or update post based on webhook data
	 *
	 * @since    1.0.0
 	 * @param      	string json-formated webhook payload
	 */
	public function create_or_update_posts($data) {

		$option = get_option( 'discourse_sync' );
		$relations = $option['relations'];
		$data_id = $data['id'];
		$body = $this->get_body($data_id);

		$status = ((!$data['deleted_at'] || !$data['archived']) ? 'publish' : 'draft');
		$wp_id = $relations[$data_id] != NULL ? $relations[$data_id] : 0;

		try {

			$res = wp_insert_post(array(
					'ID' => $wp_id,
					'post_title' => $data['fancy_title'],
					'post_content' => $body,
					'post_status' => $status,
					'post_type' => 'post',
					'sticky' => $data['pinned'],
					'post_category' => array($option['category'])
				), true
			);

			$data['pinned'] ? stick_post($res) : unstick_post($res);
			$relations[$data_id] = $res;
			$option['relations'] = $relations;

			update_option('discourse_sync', $option);

		} catch (Exception $e) {
    	echo 'Post insertion failed: ',  $e->getMessage(), "\n";
		}

	}


	/**
	 * Get the discourse body(markup) from the discourse api
	 *
	 * @since    1.0.0
	 * @param      	string Discourse topic id
	 * @return    string    html makrup (Discourse 'cooked')
	 *
	 */
	private function get_body($id) {

		 $option = get_option( 'discourse_sync' );
		 $discourse_base_url = $option['webhook_url'];
		 $discourse_key = $option['key'];

		 $req = wp_remote_get($discourse_base_url . "/t/" . $id . ".json?api_key=" . $discourse_key);
		 $cooked = json_decode($req['body'])->cooked;

		 return $cooked;

	}


	/**
	 * returns an array containing the discourse base url
	 *
	 * @since    1.0.0
	 */
	public function allowed_origin_filter() {

		 $origins[] = get_option( 'discourse_sync' )['webhook_url'];
		 return $origins;

	}

	/**
	 * Render a WP notice if required fields are empty in admin
	 *
	 * @since    1.0.0
	 */
	public function notice_info_needed_render() {
		$option = get_option( 'discourse_sync' );

		if(empty($option['webhook_url']) || empty($option['key'])){
			echo '<div class="notice notice-error is-dismissible">';
		 	echo '<p>Hey! WP Discourse Sync will <b>NOT</b> work before <b>REQUIRED</b> field are filled. Go to <b>Settings -> Discourse Sync Settings</b>.</p>';
			echo '</div>';
		}

	}

	/**
	 * add this plugin under the Settings menu
	 *
	 * @since    1.0.0
	 */
	public function wp_discourse_sync_add_admin_menu(  ) {

		add_options_page( 'Discourse Sync Settings', 'Discourse Sync Settings', 'manage_options', 'wp_discourse_sync', array(&$this, 'wp_discourse_sync_options_page') );

	}

	/**
	 * Initialize all admin fields
	 *
	 * @since    1.0.0
	 */
	public function wp_discourse_sync_settings_init(  ) {
		register_setting( 'pluginPage', 'discourse_sync' );

		add_settings_section(
			'wp_discourse_sync_pluginPage_section',
			__( '', 'wordpress' ),
			array(&$this, 'wp_discourse_sync_settings_section_callback'),
			'pluginPage'
		);

		add_settings_field(
			'wp_discourse_sync_discourse_url',
			__( 'Discourse base url*', 'wordpress' ),
			array(&$this, 'wp_discourse_sync_discourse_url_render'),
			'pluginPage',
			'wp_discourse_sync_pluginPage_section'
		);

		add_settings_field(
			'wp_discourse_sync_discourse_key',
			__( 'Discourse api key*', 'wordpress' ),
			array(&$this, 'wp_discourse_sync_discourse_key_render'),
			'pluginPage',
			'wp_discourse_sync_pluginPage_section'
		);

		add_settings_field(
			'wp_discourse_sync_cat_id',
			__( 'Category ID to post to', 'wordpress' ),
			array(&$this, 'wp_discourse_sync_cat_id_render'),
			'pluginPage',
			'wp_discourse_sync_pluginPage_section'
		);

	}

	/**
	 * render the webhook_url field
	 *
	 * @since    1.0.0
	 */
	public function wp_discourse_sync_discourse_url_render(  ) {

		$option = get_option( 'discourse_sync' );
		echo "<input type='text' placeholder='https://forum.example.com' name='discourse_sync[webhook_url]' value='{$option['webhook_url']}'>";

	}

	/**
	 * render the key field
	 *
	 * @since    1.0.0
	 */
	public function wp_discourse_sync_discourse_key_render(  ) {

		$option = get_option( 'discourse_sync' );
		echo "<input type='text' name='discourse_sync[key]' value='{$option['key']}'>";

	}

	/**
	 * render the category field
	 *
	 * @since    1.0.0
	 */
	public function wp_discourse_sync_cat_id_render(  ) {

		$option = get_option( 'discourse_sync' );
		echo "<input type='number' name='discourse_sync[category]' value='{$option['category']}'>";

	}

	/**
	 * render the section text
	 *
	 * @since    1.0.0
	 */
	public function wp_discourse_sync_settings_section_callback(  ) {

		echo __( 'All fields marked with * are required.', 'wordpress' );

	}


	/**
	 * render the whole page
	 *
	 * @since    1.0.0
	 */
	public function wp_discourse_sync_options_page(  ) {

		echo "<form action='options.php' method='post'><h2>Discourse Sync Settings</h2>";

			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();

		echo "</form>";

	}

}
