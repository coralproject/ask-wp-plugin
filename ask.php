<?php
/*
Plugin Name: Ask
Plugin URI: https://coralproject.net
Description: A plugin to easily embed Ask Forms and Ask Galleries in WordPress
Version: 1.0
Author: The Coral Project
Author URI: https://coralproject.net
License: Apache 2.0
*/

/**
 * Class Ask_Plugin
 */
class Ask_Plugin {

	/**
	 * Ask_Plugin constructor.
	 */
	public function __construct() {
		add_shortcode( 'ask-form', array( $this, 'render_form_shortcode' ) );
		add_shortcode( 'ask-gallery', array( $this, 'render_gallery_shortcode' ) );
		add_action( 'admin_menu', array( $this, 'create_settings_page' ) );
		add_action( 'admin_init', array( $this, 'setup_sections' ) );
		add_action( 'admin_init', array( $this, 'setup_fields' ) );
	}

	/**
	 * Registers the functions to create the settings page.
	 *
	 * @since 1.0.0
	 */
	public function create_settings_page() {
		add_menu_page( 'Ask Forms', 'Ask Forms', 'manage_options', 'ask-forms', array(
			$this,
			'render_settings_page',
		), 'dashicons-list-view', 100 );
		add_submenu_page( 'ask-forms', 'Ask Settings', 'Ask Settings', 'manage_options', 'ask-forms', array(
			$this,
			'render_settings_page',
		), 'dashicons-admin-plugins', 100 );
		if ( get_option( 'admin_url' ) ) {
			add_submenu_page( 'ask-forms', 'Ask Admin', 'Ask Admin', 'manage_options', 'ask-admin', array(
				$this,
				'render_admin_page',
			), 'dashicons-admin-plugins', 100 );
		}
	}

	/**
	 * Registers the settings sections.
	 *
	 * @since 1.0.0
	 */
	public function setup_sections() {
		add_settings_section( 'integration', 'About Ask', array( $this, 'section_callback' ), 'ask-settings' );
	}

	/**
	 * Register the settings fields.
	 *
	 * @since 1.0.0
	 */
	public function setup_fields() {
		$fields = array(
			array(
				'uid'         => 'base_url',
				'label'       => 'Server Base URL',
				'section'     => 'integration',
				'type'        => 'url',
				'options'     => false,
				'placeholder' => 'https://s3-us-west-2.amazonaws.com/my-s3-bucket/',
				'default'     => '',
				'callback'    => 'base_url_callback',
			),
			array(
				'uid'         => 'admin_url',
				'label'       => 'Admin Base URL',
				'section'     => 'integration',
				'type'        => 'url',
				'options'     => false,
				'placeholder' => 'https://ask.mydomain.com',
				'default'     => '',
				'callback'    => 'admin_url_callback',
			),
		);

		foreach ( $fields as $field ) {
			add_settings_field( $field['uid'], $field['label'], array(
				$this,
				$field['callback'],
			), 'ask-settings', $field['section'], $field );
			register_setting( 'ask-settings', $field['uid'] );
		}
	}

	/**
	 * Creates the markup for the settings page.
	 *
	 * @internal
	 * @since 1.0.0
	 *
	 * @param array $arguments The data sent from {@see add_settings_section()}.
	 */
	public function section_callback( $arguments ) { ?>
		<p>Ask is a form tool, built specifically for journalists.</p>
		<p>Ask lets you easily create embeddable forms, manage submissions, and display galleries of the best responses. It’s fast, flexible, and you control the design and the data.</p>
		<p>You can find out how to install and manage Ask <a href="https://docs.coralproject.net/products/ask/">here</a>.
		</p>
		<p>Ask is an open source product brought to you by The Coral Project. Find out more about Coral and the tools we build
			<a href="https://coralproject.net">here</a>.</p>

		<h2>Instructions</h2>
		<p>Use your Ask shortcode in any post or page where you want to embed an Ask form:</p>
		<p><code>[ask-form id="1234567890abcdefghij"]</code></p>
		<p>You can find your Ask form ID in the URL of your form, ex:
			<strong>https://ask.yourdomain.com/forms/1234567890abcdefghij</strong></p>

		<h2>Ask Settings</h2>
		<p>Questions/feedback? Reach out to us on <a href="https://twitter.com/coralproject">Twitter</a> or join our
			<a href="https://community.coralproject.net/">Community</a>.
		<p>You are using the version <?php echo get_plugin_data( __FILE__ )['Version'] ?> of the Ask WordPress Plugin. View the code, documentation, and latest releases
			<a href="https://github.com/coralproject/ask-wp-plugin">here</a>.</p>

	<?php }

	/**
	 * Output the settings field for the form's base URL.
	 *
	 * @since 1.0.0
	 *
	 * @param array $arguments Data sent from {@see add_settings_field()}.
	 */
	public function base_url_callback( $arguments ) { ?>
		<p>To use Ask forms in WordPress, you will need to set a Form Base URL, which is where your forms are stored:</p>
		<input style="width: 600px; height: 40px;" name="base_url" placeholder="<?php echo $arguments['placeholder']; ?>" id="base_url" type="url" value="<?php echo get_option( 'base_url' ); ?>" />
	<?php }


	/**
	 * Output the settings field for the form's admin URL.
	 *
	 * @since 1.0.0
	 *
	 * @param array $arguments Data sent from {@see add_settings_field()}.
	 */
	public function admin_url_callback( $arguments ) { ?>
		<p>You can also optionally manage your forms in WordPress, by providing the URL where your Ask admin is located:</p>
		<input style="width: 600px; height: 40px;" name="admin_url" placeholder="<?php echo $arguments['placeholder']; ?>" id="admin_url" type="url" value="<?php echo get_option( 'admin_url' ); ?>" />
	<?php }

	/**
	 * Generates the markup for the settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() { ?>
		<div class="wrap">
			<h2>Ask Settings</h2>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'ask-settings' );
				do_settings_sections( 'ask-settings' );
				submit_button();
				?>
			</form>
		</div>
	<?php }

	/**
	 * Generates the output for the plugin's admin page.
	 *
	 * @since 1.0.0
	 */
	public function render_admin_page() { ?>
		<div class="wrap">
			<h2>Ask Admin</h2>
			<iframe width="100%" height="600px" src="<?php echo get_option( 'admin_url' ); ?>" frameborder="0" hspace="0" vspace="0" marginheight="0" marginwidth="0"></iframe>
		</div>
	<?php }

	/**
	 * Generates output for the shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type   The type of shortcode to generate. Accepts 'form' or 'gallery'.
	 * @param array	 $attrs  {
	 *		The options passed in the shortcode.
	 *
	 *		@type int|string  $height The unitless height of the container. Default '580'.
	 * 		@type int|string  $id     The form ID.
	 * 		@type bool|string $iframe Whether to use an iframe or div. Default 'true'.
	 * }
	 * @return string The HTML output.
	 */
	public function render_shortcode( $type, $attrs ) {
		$height = isset( $attrs['height'] ) ? $attrs['height'] : '580';
		$id     = isset( $attrs['id'] ) ? $attrs['id'] : '';
		if ( isset( $attrs['iframe'] ) && $attrs['iframe'] == 'true' ) {
			return '<iframe width="100%" height="' . $height . '" src="' . get_option( 'base_url' ) . sanitize_text_field( $attrs['id'] ) . '.html" frameborder="0" hspace="0" vspace="0" marginheight="0" marginwidth="0"></iframe>';
		} else {
			return '<div id="ask-' . $type . '"></div><script src="' . get_option( 'base_url' ) . sanitize_text_field( $id ) . '.js"></script>';
		}
	}

	/**
	 * Generate output for the 'ask-form' shortcode.
	 *
	 * @since 1.0.0
	 * @see Ask_Plugin::render_shortcode()
	 *
	 * @param array $attrs The options to pass to render_shortcode().
	 * @return string The HTML generated for the Ask form.
	 */
	public function render_form_shortcode( $attrs ) {
		return $this->render_shortcode( 'form', $attrs );
	}

	/**
	 * Generate output for the 'ask-gallery' shortcode.
	 *
	 * @since 1.0.0
	 * @see   Ask_Plugin::render_shortcode()
	 *
	 * @param array $attrs The options to pass to render_shortcode().
	 * @return string The HTML generated for the Ask gallery.
	 */
	public function render_gallery_shortcode( $attrs ) {
		return $this->render_shortcode( 'gallery', $attrs );
	}

}

new Ask_Plugin();
