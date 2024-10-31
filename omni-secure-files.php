<?php
/*
Plugin Name: Omni Secure Files
Plugin URI: http://www.slicedprojects.ca/wordpress/omni-secure-files
Description:Secure file upload, download, browser. Integrates with WP Roles
Version: 0.1.15
Commit: blank
Author: Sliced Projects Inc.
Author URI: http://www.slicedprojects.ca/
 */
?>
<?php

require_once('lib/OmniSecureFilesNode.php');

define('DS',DIRECTORY_SEPARATOR);
class OmniSecureFiles {

  static $instance = null;

  var $version = 3; // integer version increment for doing updates as required
  var $plugin_url = null; // url to plugin
  var $plugin_path = null; // path to plugin
  var $Node = null; // Nodes Model
  var $Acl = null; // ACL Model

  function __construct() {
    $this->plugin_path = WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
    $this->plugin_url = sprintf('%s/%s',WP_PLUGIN_URL,str_replace(basename(__FILE__),"",plugin_basename(__FILE__)));
    // These hooks ironically don't fire when appropriate, switching to manual
    $this->update();
    add_action('admin_menu',array(&$this,'menus'));	
    add_action('admin_init',array(&$this,'register_settings'));
    add_action('admin_enqueue_scripts',array(&$this,'admin_enqueue_scripts'));
    add_action('admin_print_styles',array(&$this,'admin_print_styles'));
    // register ajax callbacks
    add_action('wp_ajax_secure_files',array(&$this,'ajax_admin'));
    // register scripts
    
    $this->Node = new OmniSecureFilesNode();
  }

  /*
   * Static Loader
   */

  function sharedInstance() {
    if ( self::$instance === null ) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /*
   * Checks app version number, updates as required
   */
  function update() {
    $current_version = get_option("osf_version");
    if (empty($current_version) || $current_version < $this->version) {
      $this->activation();
      update_option('osf_version',$this->version);
    }
  }

  function activation() {
    include('lib/activation.php');
  }

  function deactivation() {
    include('lib/deactivation.php');
  }

  function menus() {
    // Note, osf_view is the min required capability to use this system

    $page = add_menu_page('Omni Secure Files','Secure Files','osf_view','omni-secure-files-menu-root',array(&$this,'menu_root'));
    //add_action('load-'.$page, array(&$this,'load_menu_root'));


    add_submenu_page('omni-secure-files-menu-root','Secure Files Settings','Settings','manage_options','omni-secure-files-menu-settings',array(&$this,'menu_settings'));
  }

  function admin_print_styles() {
    if ( isset($_GET['page']) && $_GET['page'] == 'omni-secure-files-menu-root' ) {
      $this->register_styles();
      wp_enqueue_style('jquery-ui');
      wp_enqueue_style('omni-secure-files');
      wp_enqueue_style('jquery-ui-plupload');
    }
  }

  function admin_enqueue_scripts($hook) {
    if ( strpos($hook,'omni-secure-files-menu-root') !== false ) {
      $this->register_scripts();
      wp_enqueue_script('omni-secure-files-menu-root');
      wp_enqueue_script("jquery");
    }
  }
  
  function register_scripts() {
    wp_deregister_script(array('plupload'));
    wp_register_script('plupload',$this->plugin_url.'plupload/js/plupload.full.js',array(),'1.5.1.1');
    wp_register_script('jquery-ui-plupload',
      plugins_url('/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js',__FILE__),
      array('jquery','plupload','jquery-ui-core','jquery-ui-widget','jquery-ui-sortable','jquery-ui-dialog','jquery-ui-progressbar','jquery-ui-button'),
      '1.5.1.1'
    );
    wp_register_script('jquery-plupload-queue',
      $this->plugin_url.'plupload/js/jquery.plupload.queue/jquery.plupload.queue.js',
      array('jquery','plupload','jquery-ui','jquery-ui-widget','jquery-ui-sortable','jquery-ui-dialog','jquery-ui-progressbar','jquery-ui-button'),
      '1.5.1.1'
    );
    wp_register_script('omni-secure-files-menu-root',
      plugins_url('/js/omni-secure-files-menu-root.js',__FILE__),
      array('jquery','jquery-ui-core','jquery-ui-widget','jquery-ui-dialog','jquery-ui-tabs','plupload','jquery-ui-plupload'),
      '0.1.10'
    );
  }

  function register_styles() {
    wp_register_style('jquery-ui','https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/smoothness/jquery-ui.css',array(),'1.8.16','all');
    wp_register_style('jquery-plupload-queue',$this->plugin_url.'plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css',array(),false,'all');
    wp_register_style('jquery-ui-plupload',$this->plugin_url.'plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css',array(),false,'all');
    wp_register_style('omni-secure-files',$this->plugin_url.'css/style.css',array(),false,'all');
  }

  function menu_root() {
    if (!current_user_can('osf_view')) {
      die('Access denied'); // TODO make a proper error page or redirect
    }
    // do controller like stuff here
    $base_url = sprintf("%s?page=omni-secure-files-menu-root",admin_url('admin.php'));
    $action = null;
    if (isset($_POST['action'])) $action = $_POST['action'];
    if ($action === null && isset($_GET['action'])) $action = $_GET['action'];
    if ($action !== null) {
      switch($action) {
      case 'new_folder':
        // verify user can make new folders here
        if (!current_user_can('osf_directory')) break; // skip
        if (isset($_POST['node_name']) && isset($_POST['node_parent_ID'])) {
          $data = array(
            'node_name' => $_POST['node_name'],
            'node_type' => 'directory',
            'node_parent_ID' => $_POST['node_parent_ID']
          );
          $this->Node->save($data);
          $_GET['parent_id'] = $this->Node->ID();
        }

        break;
      case 'new_file':
        // verify use can upload files
        if (!current_user_can('osf_upload')) break; // skip
        if (isset($_POST['node_parent_ID']) && isset($_POST['secure-files-upload-queue_count'])) {
          $paths = get_option('osf_paths');
          $destDir = sprintf('%s%s%d',$paths['storage'],DS,$_POST['node_parent_ID']);
          for($i=0; $i < $_POST['secure-files-upload-queue_count'];$i++) {
            $tmpName = $_POST[sprintf('secure-files-upload-queue_%d_tmpname',$i)];
            $tmpFull = sprintf("%s%splupload%s%s",$paths['tmp'],DS,DS,$tmpName);
            $displayName = $_POST[sprintf('secure-files-upload-queue_%d_name',$i)];
            $splitDisplayName = explode('.',$displayName);

            $destName = sprintf("%s%s",uniqid('',true),(count($splitDisplayName) == 1 ? '' : '.' . end($splitDisplayName)));

            if (!file_exists($destDir)) {
              @mkdir($destDir);
            }

            $destFull = sprintf('%s%s%s',$destDir,DS,$destName);

            @rename($tmpFull,$destFull); // I feel dirty consuming this warning

            $data = array(
              'node_name' => $displayName,
              'node_type' => 'file',
              'node_parent_ID' => $_POST['node_parent_ID'],
              'node_file_ext' => (count($splitDisplayName) == 1 ? '' :  end($splitDisplayName)),
              'node_uri' => $destName
            );
            $this->Node->save($data);
            $_GET['parent_id'] = $_POST['node_parent_ID'];
          }

        }
      case 'delete':
        // verify user can delete
        if (!current_user_can('osf_delete')) break; // skip
        if (isset($_GET['id'])) {
          $node = $this->Node->node($_GET['id']);
          if (!empty($node)) {
            $this->Node->del($node['node_ID']);
            $_GET['parent_ID'] = $node['node_parent_ID'];
          }
        }
        break;
      case 'rename':
        // fetch node and determine type
        $node = $this->Node->node($_POST['node_id']);
        if ( !empty($node) ) {
          if ( $node['node_type'] == 'directory' ) {
            if ( !current_user_can('osf_directory') ) break; // skip
            $_GET['parent_id'] = $node['node_parent_ID'];
          } else if ( $node['node_type'] == 'file' ) {
            if ( !current_user_can('osf_upload') ) break; // skip
            $_GET['parent_id'] = $node['node_parent_ID'];
          }
          $node['node_name'] = $_POST['node_name'];
          $this->Node->save($node);

        }
        break;
      }
    }
    // our view
    include('menus/root.php');
  }

  function menu_settings() {
    global $wp_roles;
    // update roles
    $options = get_option('osf_roles');
    if (is_array($options)) { // options is a valid array
      foreach(array('view','upload','directory','download','delete') as $cap) { // our capability postfix names
        $cap_name = sprintf("osf_%s",$cap);
        $all_roles = $wp_roles->get_names();
        if ( isset($options[$cap]) && is_array($options[$cap]) ) { // our options array has one of our capabilities
          foreach($all_roles as $a_role_key => $a_role_name) { // loop over all current roles in system
            $role = $wp_roles->get_role($a_role_key);
            if ( in_array($a_role_key,$options[$cap]) ) {

              $role->add_cap($cap_name);
            } else {
              $role->remove_cap($cap_name);
            }
          } // end foreach
        } // end if
      } // end cap loop
    } // end if
    clearstatcache();
    include('menus/settings.php');
  }

  function register_settings() {
    register_setting('omni-secure-files-settings-group','osf_paths');
    register_setting('omni-secure-files-settings-group','osf_settings');
    register_setting('omni-secure-files-settings-group','osf_roles');
  }

  // Ajax callback for admin backend
  function ajax_admin() {
    $response = array();
    $response_type = 'html';
    $does = array('directory_listing','file_upload','download','rename','get_acl','acl_list','acl_add');
    if (isset($_GET['do']) && in_array($_GET['do'],$does)) {
      require( sprintf("lib/ajax/%s.php",$_GET['do']));
    } else {
      echo "invalid do";
      exit;
    }

    switch($response_type) {
    case 'json':
      $response = json_encode($response);
      header("Content-type: application/json");
      echo $response;
      exit;
    case 'html':
      header("Content-type: text/html");
      exit;
    default:
      echo 'response type not set';
      exit;
    }
  }

 
}

OmniSecureFiles::sharedInstance();
