<div class="wrap">
	<h2>Settings</h2>
  <?php if ( WP_DEBUG != false ): ?>
  <p style="color:red;">WP_DEBUG is currently enabled. You may experience problems with downloads and other features with this enabled.<br/>
    Read here for information on disabling WP_DEBUG
  <a href="http://codex.wordpress.org/Editing_wp-config.php#Debug" alt="Wordpress Codex wp-config">Editing wp-config.php</a>
  </p>
  <?php endif; ?>
	<form method="post" action="options.php">
	<?php
    settings_fields('omni-secure-files-settings-group');
    $options = get_option('osf_paths');
    ?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Path to save files:</th>
			<td>
				<input type="text" name="osf_paths[storage]" value="<?php echo $options['storage']; ?>" style="width:300px;" />
				<?php if ( !file_exists($options['storage']) ): ?>
				<p style="color:red;">This path does not exist, please create it.</p>
				<?php endif; ?>
                <?php if ( file_exists($options['storage']) && !is_readable($options['storage']) ): ?>
                <p style="color:red;">This path is not writable. Check your permissions on the directory</p>
                <?php endif; ?>
                <?php if ( file_exists($options['storage']) && !is_writable($options['storage']) ): ?>
                <p style="color:red;">This path is not writable. Check your permissions on the directory</p>
                <?php endif; ?>
			</td>
		</tr>
    <tr valign="top">
			<th scope="row">Temporary Path to save files:</th>
			<td>
			<input type="text" name="osf_paths[tmp]" value="<?php echo $options['tmp']; ?>" style="width:300px;" />
			<?php if ( !file_exists($options['tmp']) ): ?>
			<p style="color:red;">This path does not exist, please create it.</p>
			<?php endif; ?>
        <?php if ( file_exists($options['tmp']) && !is_readable($options['tmp']) ): ?>
        <p style="color:red;">This path is not writable. Check your permissions on the directory</p>
        <?php endif; ?>
        <?php if ( file_exists($options['tmp']) && !is_writable($options['tmp']) ): ?>
        <p style="color:red;">This path is not writable. Check your permissions on the directory</p>
        <?php endif; ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td>
				For most installations, these paths can be the same<br/>
				Wordpress is currently installed in: <?php echo ABSPATH; ?><br/>
				Set the above directories to something like: <?php echo sprintf("%s%ssecure_files",realpath(sprintf("%s..",ABSPATH)),DS); ?><br/>
				This path is only a recommendation. Please make sure this path is not accessible publicly through your website or to other uses hosted on your server.
			</td>
		</tr>
     <tr valign="top">
			<th scope="row">Max file size (in mb):</th>
			<td>
        <?php
        $options = get_option('osf_settings');
        ?>
			<input type="text" name="osf_settings[max_upload_size]" value="<?php echo isset($options['max_upload_size']) ? $options['max_upload_size'] : '1024'; ?>" style="width:75px;" /> mb
      <p>Enter size in "megabytes". Files larger than this size will not be allowed to be uploaded. Some hosting may limit the file size to ~ 2048 megabytes (as a technical limitation). If you experience problems with files larger than 2048mb, that is likely the cause.</p>
			</td>
		</tr>

	</table>
    <?php
    $_defaults = array(
        'view' => array(),
        'upload' => array(),
        'directory' => array(),
        'download' => array(),
        'delete' => array()
    );
    $options = get_option('osf_roles');
    $options = array_merge($_defaults,is_array($options) ? $options : array());

    global $wp_roles;
    $roles = $wp_roles->get_names();
    ?>
    <table class="form-table">
        <tr valign="top">
			<th scope="row">Roles that can view:</th>
			<td>
                <?php
                foreach($roles as $role_key => $role_name) {
                    $selected = "";
                    if (in_array($role_key,$options['view'])) $selected='checked="checked"';
                    ?>
                    <input type="checkbox" name="osf_roles[view][]"
                           value="<?php echo $role_key; ?>" <?php echo $selected; ?> ><?php echo $role_name; ?><br/>
                    <?php
                }
                ?>
            </td>
		</tr>
		<tr valign="top">
			<th scope="row">Roles that can upload:</th>
			<td>
                <?php
                foreach($roles as $role_key => $role_name) {
                    $selected = "";
                    if (in_array($role_key,$options['upload'])) $selected='checked="checked"';
                    ?>
                    <input type="checkbox" name="osf_roles[upload][]"
                           value="<?php echo $role_key; ?>" <?php echo $selected; ?> ><?php echo $role_name; ?><br/>
                    <?php
                }
                ?>
            </td>
		</tr>
        <tr valign="top">
			<th scope="row">Roles that can create directories:</th>
			<td>
                <?php
                foreach($roles as $role_key => $role_name) {
                    $selected = "";
                    if (in_array($role_key,$options['directory'])) $selected='checked="checked"';
                    ?>
                    <input type="checkbox" name="osf_roles[directory][]"
                           value="<?php echo $role_key; ?>" <?php echo $selected; ?> ><?php echo $role_name; ?><br/>
                    <?php
                }
                ?>
            </td>
		</tr>
        <tr valign="top">
			<th scope="row">Roles that can download files:</th>
            <td>
			 <?php
                foreach($roles as $role_key => $role_name) {
                    $selected = "";
                    if (in_array($role_key,$options['download'])) $selected='checked="checked"';
                    ?>
                    <input type="checkbox" name="osf_roles[download][]"
                           value="<?php echo $role_key; ?>" <?php echo $selected; ?> ><?php echo $role_name; ?><br/>
                    <?php
                }
                ?>
            </td>
		</tr>
        <tr valign="top">
			<th scope="row">Roles that can delete files/directories:</th>
            <td>
			 <?php
                foreach($roles as $role_key => $role_name) {
                    $selected = "";
                    if (in_array($role_key,$options['delete'])) $selected='checked="checked"';
                    ?>
                    <input type="checkbox" name="osf_roles[delete][]"
                           value="<?php echo $role_key; ?>" <?php echo $selected; ?> ><?php echo $role_name; ?><br/>
                    <?php
                }
                ?>
            </td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>"/>
	</p>
	</form>
</div>
