  jQuery(document).ready(function() {
    
    // rename dialog
    jQuery('#secure-files-rename').dialog({
      autoOpen: false,
      title: 'Rename Folder',
      modal: true,
      position: 'center'
    });

    jQuery('.row-actions .rename-directory').click(function() {
      var id = jQuery(this).parents('li').find('a').attr('node_ID');
      var name = jQuery(this).parents('li').find('a').html().trim();
      jQuery('#secure-files-rename input[name="node_name"]').val(name);
      jQuery('#secure-files-rename input[name="node_id"]').val(id);
      jQuery('#secure-files-rename').dialog('open');
      return false;
    });
  
    jQuery('.row-actions a.rename-file').click(function() {
      var id = jQuery(this).parents('li').find('a').attr('node_id');
      var name = jQuery(this).parents('li').find('a').html().trim();
      jQuery('#secure-files-rename input[name="node_name"]').val(name);
      jQuery('#secure-files-rename input[name="node_id"]').val(id);
      jQuery('#secure-files-rename').dialog('open');
      return false;
    });

    jQuery('.row-actions .acl-directory').click(function() {
      var acl_dialog = jQuery("#acl-dialog");
      if ( acl_dialog.length == 0 ) {
        acl_dialog = jQuery('<div id="acl-dialog" class="osf-acl-dialog" style="display: none;"></div>').appendTo('body');
      }
      var node_id = jQuery(this).parents('li').children('a').attr('node_ID');
      var node_name = jQuery(this).parents('li').children('a').html().trim();
      jQuery.ajax({
        url: window.osf_settings.ajax_url,
        data: { action: 'secure_files', 'do':'get_acl', node_ID: node_id },
        dataType: 'html',
        success: function(responseText,textStatus,XMLHttpRequest) {
          jQuery(acl_dialog).html(responseText);
          acl_dialog.dialog({
            modal: true,
            title: 'ACL for: ' + node_name
          });
        }
      });
    });

    jQuery('.row-actions a.acl-file').click(function() {
      alert('ACL File');
    });

    // uploader
    jQuery("#secure-files-upload-queue").plupload({
      runtimes: 'flash,html5,html4',
      url: window.osf_settings.ajax_url + '?action=secure_files&do=file_upload',
      max_file_size: window.osf_settings.max_upload_size + 'mb',
      chunk_size: '256kb',
      unique_names: true,

      // Flash settings
      flash_swf_url: window.osf_settings.plugin_url + 'plupload/js/plupload.flash.swf',
  
    });
    function uploaderStateChanged() {
      var uploader = jQuery('#secure-files-upload-queue').plupload('getUploader');
      if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
        jQuery('#secure-files-upload-queue').parent('form').submit();
      }
    }

    jQuery('#secure-files-upload-queue').parent('form').submit(function(e) {
        var uploader = jQuery('#secure-files-upload-queue').plupload('getUploader');

        // Submit is success, else upload Files in queue upload them
        if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
          uploader.unbind('StateChanged',uploaderStateChanged);
          return true;
        } else if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', uploaderStateChanged);
            uploader.start();
        } else {
            alert('You must at least upload one file.');
        }
        return false;
    });
   
    /* tabs */
    jQuery('div#secure-files-tabs').tabs({
    });

  });
