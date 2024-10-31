<?php
wp_localize_script('omni-secure-files-core','OmniSecureFilesAjax',array('ajaxurl'=>admin_url('admin-ajax.php')));

$parent_id = 0;
if (isset($_GET['parent_id'])) {
    $parent_id = $_GET['parent_id'];
}
?>
<div class="wrap">

  <h2>Secure Files</h2>
  <?php if ( WP_DEBUG != false ): ?>
  <p style="color:red;">WP_DEBUG is currently enabled. You may experience problems with downloads and other features with this enabled. <br/>
    Read here for information on disabling WP_DEBUG
    <a href="http://codex.wordpress.org/Editing_wp-config.php#Debug" alt="Wordpress Codex wp-config">Editing wp-config.php</a>
  </p>
    <?php endif; ?>
  <ul class="subsubsub stuffbox">
        <li><a href="<?php printf("%s&parent_id=%s",$base_url,0); ?>">Secure Files</a></li>
        <?php
        $crumbs = $this->Node->getPath($parent_id);
        $crumbs = array_reverse($crumbs);
        $count=0;
        foreach($crumbs as $crumb) {
            $count++;
            ?>
            <?php if ($count == count($crumbs)) : ?>
        <li class="last"><?php echo $crumb['node_name']; ?></li>
      <?php else : ?>
              <li><a href="<?php printf("%s&parent_id=%s",$base_url,$crumb['node_ID']); ?>"><?php echo $crumb['node_name']; ?></a></li>
      <?php endif; ?>
            <?php
        }
        ?>
  </ul>

  <div class="secure-files-wrap">
  
    <div class="postbox-container" style="width:30%">
    
      <div class="metabox-holder">
  
        <div class="meta-box-sortables ui-sortable">
  
          <div class="postbox">
            
            <div class="handlediv" title="Click to toggle"><br /></div><!-- /.handlediv -->
            <h3 class="hndle"><span>Folders</span></h3>          
            
            <div id="secure-files-tree" class="inside">
              <?php
              $nodes = $this->Node->get($parent_id,array('node_type'=>'directory'));
              $parent_node_ID = $this->Node->getParentID($parent_id);
              ?>
              <ul>
                <?php
                if ($parent_node_ID !== NULL) {
                ?>
                <li>
                  <a class="back-to-parent" href="<?php printf("%s&parent_id=%s",$base_url,$parent_node_ID); ?>">Back to Parent</a>
                </li>
                <?php
                }
                ?>
                <?php
                  $i=0;
                  foreach($nodes as $node):
                  $class = 'file';
                  $url_delete = sprintf("%s&action=delete&id=%s",$base_url,$node['node_ID']);
                  if ($node['node_type'] == 'directory') $class = 'directory';
                ?>
                <li <?php if($i%2==0) echo "class='alt'"; ?>>
                  <a href="<?php printf("%s&parent_id=%s",$base_url,$node['node_ID']); ?>" class="secure_node <?php echo $class; ?>" node_ID="<?php echo $node['node_ID']; ?>">
                    <?php echo (strlen(trim($node['node_name'])) == 0 ? 'Untitled' : $node['node_name']); ?>
                  </a>
                  <div class="row-actions">
                    <?php if (current_user_can('osf_delete')): ?>
                    <a class="delete-directory" href="<?php echo $url_delete; ?>" onclick="javascript:return confirm('Are you sure?');">Delete</a>
                    <?php endif; ?>
                    <?php if (current_user_can('osf_directory')): ?>
                    <a class="rename-directory" href="#" >Rename</a>
                    <?php endif; ?>
                    <!--
                    <a class="acl-directory" href="#">ACL</a>
                    -->
                  </div><!-- /.row-actions -->
                </li>
                <?php 
                  $i++;
                  endforeach; 
                ?>
              </ul>
            </div><!-- /#secure-files-tree.inside -->
        
          </div><!-- /.postbox -->
  
        </div><!-- /.metabox-holder -->
  
      </div><!-- /."meta-box-sortables ui-sortable -->
      
    </div><!-- /.postbox-container -->
  
  
    <div class="postbox-container" style="width:60%">
    
      <div class="metabox-holder">
  
        <div class="meta-box-sortables ui-sortable">
  
          <div class="postbox">
            
            <div class="handlediv" title="Click to toggle"><br /></div><!-- /.handlediv -->
            <h3 class="hndle"><span>Files</span></h3>    
      
            <div id="secure-files-listing">
              <?php
          
              $nodes = $this->Node->get($parent_id,array('node_type'=>'file'));
              ?>
              <ul>
                <?php
                $i=0;
                foreach($nodes as $node):
                  $url_download = sprintf("%s?action=secure_files&do=download&id=%s",admin_url('admin-ajax.php'),$node['node_ID']);
                  $url_delete = sprintf("%s&action=delete&id=%s",$base_url,$node['node_ID']);
                ?>
                <li <?php if($i%2==0) echo "class='alt'"; ?>>
                  <div class="modified-date">
                    <?php echo $node['modified']; ?>
                  </div><!-- /.modified -->
                  <!--
                  <div class="secure-file-image">
                    [ ]
                  </div>--><!-- /.secure-file-image -->
                  
                  <div class="secure-file-details">
                                        <?php if (current_user_can('osf_download')): ?>
                    <a href="<?php echo $url_download; ?>" class="secure_node file" title="<?php echo $node['node_name']; ?>" node_ID="<?php echo $node['node_ID']; ?>">
                      <?php echo $node['node_name']; ?>
                     </a>
                                         <?php else: ?>
                                            <?php echo $node['node_name']; ?>
                                         <?php endif; ?>
                     <div class="row-actions">
                                             <?php if (current_user_can('osf_delete')): ?>
                      <span><a href="<?php echo $url_delete; ?>" onclick="javascript:return confirm('Are you sure?');">Delete</a></span>
                                            <?php endif; ?>
                      <?php if (current_user_can('osf_upload')): ?>
                        <span><a href="#" class="rename-file">Rename</a></span>
                      <?php endif; ?>
                      <!--
                      <span><a href="#" class="acl-file">ACL</a></span>
                      -->
                    </div>
                  </div><!-- /.secure-file-image-details -->
                  
                </li>
                <?php 
                  $i++;
                  endforeach; 
                ?>
              </ul>
            </div><!-- /#secure-files-listing -->
        
          </div><!-- /.postbox -->
  
        </div><!-- /.metabox-holder -->
  
      </div><!-- /."meta-box-sortables ui-sortable -->
      
    </div><!-- /.postbox-container -->  
  
    <div class="clear"><!-- --></div>
  
    <div id="secure-files-operations" class="metabox-holder">
  
      <div id="secure-files-tabs">
        <ul>
          <?php if (current_user_can('osf_upload')): ?>
                    <li><a class="nav-tab" href="#secure-files-upload"><span>Upload File</span></a></li>
          <?php endif; ?>
          <?php if (current_user_can('osf_directory')): ?>
                    <li><a class="nav-tab" href="#secure-files-new-directory"><span>New Directory</span></a></li>
          <?php endif; ?>
        </ul>
    
      <?php if (current_user_can('osf_upload')): ?>
      <div id="secure-files-upload" class="postbox">
        <div class="secure-menu-inside">
          <form action="<?php echo $base_url; ?>" method="POST" enctype="multipart/form-data">
            <div id="secure-files-upload-queue">
              Flash or an HTML 5 compatible browser is required to upload files. 
            </div><!-- /#secure-files-upload-queue -->
            <input type="hidden" name="node_parent_ID" value="<?php echo $parent_id;?>" />
            <input type="hidden" name="action" value="new_file"/>
            <input type="hidden" name="page" value="omni-secure-files-menu-root" />
            <input type="submit" value="Upload"/>
          </form>
        </div><!-- /.secure-menu-inside -->
      </div><!-- /#secure-files-upload -->
      <?php endif; ?>

      <?php if (current_user_can('osf_directory')): ?>
      <div id="secure-files-new-directory" class="postbox">
        <div class="secure-menu-inside">
          <form action="<?php echo $base_url; ?>" method="POST">
            <input type="text" name="node_name"/>
            <input type="hidden" name="node_parent_ID" value="<?php echo $parent_id;?>" />
            <input type="hidden" name="action" value="new_folder"/>
            <input type="hidden" name="page" value="omni-secure-files-menu-root" />
            <input type="submit" value="New Folder"/>
          </form>
        </div><!-- /.secure-menu-inside -->
      </div><!-- /#secure-files-new-directory -->
      <?php endif; ?>
      </div><!-- /#secure-files-tabs -->
    </div><!-- /#secure-files-operations -->

    <div id="secure-files-rename" style="display: none; ">
      <h3>Rename</h3>
        <form action="<?php echo $base_url; ?>" method="POST">
          <input type="hidden" name="action" value="rename" />
          <input type="hidden" name="node_id" value="" />
          <input type="text"  name="node_name" value="" />
          <input type="submit" value="Rename" />
        </form>
    </div>  
  </div><!-- /#secure-files-wrap -->
  
     <div class="clear"><!-- --></div>
</div><!-- /.wrap -->
                  <?php
// load settings
settings_fields('omni-secure-files-settings-group');
$osf_settings = array_merge(get_option('osf_settings'),array('ajax_url'=> admin_url('admin-ajax.php'),'plugin_url' => $this->plugin_url));
?>

<script type="text/javascript">
<!--
  window.osf_settings = <?php echo json_encode($osf_settings); ?>;
-->
</script>
