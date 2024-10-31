/** Secure Files Plugin */

(function($) {
	$.fn.secure_files = function(options) {
		var opts = $.extend({},$.fn.secure_files.defaults,options);	

		return this.each(function(index,c) {
			// tree binding
			bind_tree($(c).find('#' + opts.tree));
		});
	};

	function bind_tree(root) {
		$(root).find('a.secure_node .directory').live('click',directory_click);
        $(root).find('a.secure_node .file').live('click',file_click);
		update_tree('0');
	}

	function directory_click(event) {
		var parent_id = 0;
        
	}

    function file_click(event) {
        var file_id = 0;
    }

	function update_tree(parent_id) {
		$.ajax({
			complete: update_tree_complete,
			success: update_tree_success,
			data: {
				action: 'secure_files',
				'do': 'directory_listing',
				'parent_id' : parent_id
			},
			dataType: 'html',
			error: update_tree_error,
			type: 'POST',
			url: ajaxurl
		});
	}

	function update_tree_complete(XMLHttpRequest, textStatus) {
	}

	function update_tree_success(data, textStatus, XMLHttpRequest) {
		$('#secure-files-tree').html(data);
	}

	function update_tree_error(XMLHttpRequest, textStatus, errorThrown) {
	}

	$.fn.secure_files.defaults = {
		tree: 'secure-files-tree',
		listing: 'secure-files-listing',
		operations: 'secure-files-operations'
	};

})(jQuery);
