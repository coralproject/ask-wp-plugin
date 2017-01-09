<?php
  /*
  Plugin Name: Ask
  Plugin URI: https://coralproject.net
  Description: a plugin for integrating ASK forms within wordpress
  Version: 1.0
  Author: Dan Zajdband
  Author URI: https://zajdband.com
  License: MIT
  */

class Ask_Plugin {

  public function __construct() {
    add_shortcode('ask-form', array($this, 'render_form_shortcode'));
    add_shortcode('ask-gallery', array($this, 'render_gallery_shortcode'));
    add_action('admin_menu', array($this, 'create_settings_page'));
    add_action( 'admin_init', array( $this, 'setup_sections' ));
    add_action( 'admin_init', array( $this, 'setup_fields' ));
  }

  public function create_settings_page () {
    add_submenu_page('options-general.php', 'Ask Settings', 'Ask Settings', 'manage_options', 'ask-settings', array( $this, 'render_settings_page' ), 'dashicons-admin-plugins', 100);
    if (get_option('admin_url')) {
      add_submenu_page('options-general.php', 'Ask Admin', 'Ask Admin', 'manage_options', 'ask-admin', array( $this, 'render_admin_page' ), 'dashicons-admin-plugins', 100);
    }
  }

  public function setup_sections() {
    add_settings_section('integration', 'Integration configuration', array( $this, 'section_callback' ), 'ask-settings' );
  }

  public function setup_fields () {
    $fields = array(
      array(
        'uid' => 'base_url',
        'label' => 'Server Base URL',
        'section' => 'integration',
        'type' => 'url',
        'options' => false,
        'placeholder' => 'https://',
        'default' => '',
        'callback' => 'base_url_callback'
      ),
      array(
        'uid' => 'admin_url',
        'label' => 'Admin Base URL',
        'section' => 'integration',
        'type' => 'url',
        'options' => false,
        'placeholder' => 'https://',
        'default' => '',
        'callback' => 'admin_url_callback'
      )
    );

    foreach( $fields as $field ){
      add_settings_field( $field['uid'], $field['label'], array( $this, $field['callback'] ), 'ask-settings', $field['section'], $field );
      register_setting( 'ask-settings', $field['uid'] );
    }
  }

  public function section_callback($arguments) {
  }

  public function base_url_callback($arguments) {
    echo '<input style="width: 600px; height: 40px;" name="base_url" placeholder="'. $arguments['placeholder'] .'" id="base_url" type="url" value="' . get_option( 'base_url' ) . '" />';
  }


  public function admin_url_callback($arguments) {
    echo '<input style="width: 600px; height: 40px;" name="admin_url" placeholder="'. $arguments['placeholder'] .'" id="admin_url" type="url" value="' . get_option( 'admin_url' ) . '" />';
  }

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

  public function render_admin_page() { ?>
    <div class="wrap">
    	<h2>Ask Admin</h2>
      <iframe width="100%" height="600px" src="<?php echo get_option('admin_url'); ?>" frameborder="0" hspace="0" vspace="0" marginheight="0" marginwidth="0"></iframe>
    </div>
  <?php }

  public function render_shortcode($type, $attrs) {
    $height = isset($attrs['height']) ? $attrs['height'] : '580';
    $id = isset($attrs['id']) ? $attrs['id'] : '';
    if (isset($attrs['iframe']) && $attrs['iframe'] == 'true') {
      return '<iframe width="100%" height="' .  $height . '" src="' . get_option('base_url') . sanitize_text_field($attrs['id']) . '.html" frameborder="0" hspace="0" vspace="0" marginheight="0" marginwidth="0"></iframe>';
    } else {
      return '<div id="ask-' . $type . '"></div><script src="' . get_option('base_url') . sanitize_text_field($id) . '.js"></script>';
    }
  }

  public function render_form_shortcode($attrs) {
    return $this->render_shortcode('form', $attrs);
  }

  public function render_gallery_shortcode($attrs) {
    return $this->render_shortcode('gallery', $attrs);
  }

}

new Ask_Plugin();

?>
