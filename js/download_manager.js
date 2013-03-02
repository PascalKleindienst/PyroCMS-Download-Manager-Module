jQuery.noConflict(); 
jQuery(document).ready(function() {
	// generate a slug when the user types a title in
	pyro.generate_slug('input[name="download_name"]', 'input[name="download_slug"]');
	
	jQuery('form.download_manager').on('change', 'input[name="download_type"]', function(e) {	
		jQuery('.filetype.file').toggleClass('hidden');
		jQuery('.filetype.url').toggleClass('hidden');
	});
});