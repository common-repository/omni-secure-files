<?php
/**
 * Created by PhpStorm.
 * User: jsapara
 * Date: Nov 5, 2010
 * Time: 4:18:53 PM
 * To change this template use File | Settings | File Templates.
 */
if(!isset($_GET['id'])) {
    die('File not specified.');
}

// check user permissions
if (!current_user_can('osf_download')) {
    die('Access denied.');
}

$node_ID = $_GET['id'];

$node = $this->Node->node($node_ID);
if (empty($node) ) {
  die('Record of file not found.');
}

$fileName = $node['node_name'];
$splitFileName = explode('.',$fileName);
if (count($splitFileName) == 1) $fileName = sprintf("%s.%s",$fileName,$node['node_file_ext']);

$paths = get_option('osf_paths');
$fullPath = sprintf("%s/%d/%s",$paths['storage'],$node['node_parent_ID'],$node['node_uri']);
if ( !file_exists($fullPath) ) {
  die('File is missing from server. Please check your settings.');
}

if ( !is_readable($fullPath) ) {
  die('File exists, but cannot be read. Check the permissions of the directories in your settings.');
}

header('Pragma: public');
header('Content-Type: application/octet-stream');
header(sprintf('Content-Disposition: Attachment;filename="%s"',$fileName));
header('Content-length: ' . filesize($fullPath));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$lHandle = fopen($fullPath,'rb');
$lChunk = 1024 * 1024;
if ($lHandle !== false) {
    while(!feof($lHandle)){
        // Prevent script from timing out for large files.
        set_time_limit(0);
        $lBuffer = fread($lHandle,$lChunk);
        echo $lBuffer;
        ob_flush();
        flush();
    }
}
fclose($lHandle);
