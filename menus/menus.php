a<?php

add_action('admin_menu','omni_secure_files_menu');

function omni_secure_files_menu() {
}

function omni_secure_files_menu_root() {
	include('root.php');
}

function omni_secure_files_menu_settings() {
	include('settings.php');
}

// Register all the plugin settings here, possibly decide what settings to have based on role/is_admin()/etc
function omni_secure_files_register_settings() {
}
