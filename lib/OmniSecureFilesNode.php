<?php
/**
 * Created by PhpStorm.
 * User: jsapara
 * Date: Nov 4, 2010
 * Time: 1:58:21 PM
 * To change this template use File | Settings | File Templates.
 */
 
class OmniSecureFilesNode {
    var $table = "osf_nodes";

    function __construct() {
        global $wpdb;
        
        $this->table = sprintf("%s%s",$wpdb->prefix,$this->table);
    }

    /**
     * @param int $parent_id default 0
     * @param array $filters
     * @return array
     */
    function get($parent_id=0,$filters=array()) {
        global $wpdb;

        $where = array('node_parent_ID = %d');
        $where_values = array($parent_id);
        if (!empty($filters)) {
            foreach($filters as $filter_name=>$filter_value) {
                switch ($filter_name) {
                    case 'node_type':
                        $where[] = sprintf("%s = %s",$filter_name,'%s');
                        $where_values[] = $filter_value;
                        break;
                    default:
                        break;
                }
            }
        }

        $query = $wpdb->prepare(
            "SELECT *
            FROM $this->table
            WHERE " . implode(" AND ", $where) . " 
            ORDER BY node_name ASC",
            $where_values
        );
        $results = $wpdb->get_results(
            $query,
            ARRAY_A
        );
        return $results;
    }

    function node($id) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT *
            FROM $this->table
            WHERE node_ID=%d",$id
        );

        return $wpdb->get_row(
            $query,
            ARRAY_A
        );
    }

    function getParentID($node_id) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT node_parent_ID
            FROM $this->table nodes
            WHERE nodes.node_ID=%d;",
            $node_id
        );
        return $wpdb->get_var($query);
    }


    /**
     * Returns array of node row records, first is node_id, last is main parent
     * Does not include node 0 of course, you gotta add that yourself
     * @param  $node_id
     * @return array parent nodes, including self or emtpy array() if node_id 0
     */
    function getPath($node_id) {
        $result = array();
        if ($node_id != 0) {
            $node = $this->node($node_id);
            $result[] = $node;
            $tmp_results = $this->getPath($node['node_parent_ID']);
            foreach($tmp_results as $tmp_result) {
                $result[] = $tmp_result;
            }
            return $result;
        } else {
            return $result;
        }
    }
    /**
     * @param  array $data
     * @return bool
     */
    function save($data) {
        global $wpdb;
        $data['modified'] = date('Y-m-d H:i:s');
        if (isset($data['node_ID'])) {
            $where = array('node_ID'=>$data['node_ID']);
            unset($data['node_ID']);
            $wpdb->update($this->table,$data,$where);
        } else {
            $data['created'] = date('Y-m-d H:i:s');
            $wpdb->insert($this->table,$data);
        }
    }

    function ID() {
        global $wpdb;

        return $wpdb->insert_id;
    }

		function rename($id,$newname) {
			global $wpdb;
			$node = $this->node($id);
			if (empty($node)) {
				return false;
			}

			$query = $wpdb->prepare(
				"UPDATE FROM $this->table
				SET node_name='%s'
				WHERE node_id=%d",
				$newname,$id
			);

			$wpdb->query($query);

			return $true;
		}

    function del($id) {
        global $wpdb;
        $node = $this->node($id);
        if (empty($node)) {
            return false;
        }

        if ($node['node_type'] == 'file' ) {
            $paths = get_option('osf_paths');
            $fullPath = sprintf("%s/%d/%s",$paths['storage'],$node['node_parent_ID'],$node['node_uri']);
            @unlink($fullPath);
            $query = $wpdb->prepare(
                "DELETE FROM $this->table
                WHERE node_ID=%d",
                $id
            );
            $wpdb->query($query);
        } elseif ($node['node_type'] == 'directory' ) {
            // delete children first, recursive...
            $nodes = $this->get($id);
            foreach($nodes as $node) {
                $this->del($node['node_ID']);
            }
            $query = $wpdb->prepare(
                "DELETE FROM $this->table
                WHERE node_ID=%d",
                $id
            );
            $wpdb->query($query);
        }
			return true;
    }
}
