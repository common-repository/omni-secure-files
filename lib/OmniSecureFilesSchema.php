<?php
/**
 * Created by PhpStorm.
 * User: jsapara
 * Date: Nov 4, 2010
 * Time: 1:49:53 PM
 * To change this template use File | Settings | File Templates.
 */
 
class OmniSecureFilesSchema {

    var $version = "4"; // increment each time and document schema data updates

    var $db_osf_nodes = "CREATE TABLE %sosf_nodes (
    node_ID bigint(20) NOT NULL auto_increment,
    node_name varchar(55) NOT NULL default 'untitled',
    node_type varchar(25) NOT NULL default 'file',
    node_parent_ID bigint(20) NOT NULL default 0,
    node_uri varchar(36) NOT NULL DEFAULT '',
    node_file_ext varchar(36) NOT NULL DEFAULT '',
    created datetime NOT NULL,
    modified datetime NOT NULL,
    PRIMARY KEY  (node_ID),
    KEY node_parent (node_parent_ID)
    );";

    function __construct() {
        
    }
    
    function go() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // nodes
        $query = sprintf($this->db_osf_nodes,$wpdb->prefix);
        dbDelta($query);

        // remove old prefix_osf_acls table

        update_option("osf_db_version",$this->version);

        
    }
}
