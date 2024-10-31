<?php

$parent_id = 0;
if (isset($_POST['parent_id'])) {
    $parent_id = $_POST['parent_id'];
}

$nodes = $this->Node->get($parent_id);

?>
<ul>
	<?php foreach($nodes as $node):
        $class = 'file';
        if ($node['file_type'] == 'directory') $class = 'directory';
        ?>
	<li>
        <a href="" class="secure_node <?php echo $class; ?>" node_ID="<?php echo $node['node_ID']; ?>">
            <?php echo strlen(trim($node['node_name'])) == 0 ? 'Untitled' : $node['node_name']; ?>
        </a>
    </li>
	<?php endforeach; ?>
</ul>

<?php

$response_type = 'html';
