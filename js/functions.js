jQuery(document).ready(function() {
	jQuery('[id^=faculty_staff_content]').dialog({
		width: "auto",
		draggable: false,
		resizable: false,
		autoOpen: false,
		modal: true,
		position: { my: "center", at: "center", of: window },
		create: function(event, ui) {
			jQuery(this).css ("maxWidth", "900px");
		}
	});
	jQuery('.faculty_staff_entry > .whitebox_open').click(function(){
		jQuery('#'+jQuery(this).data("id")).dialog( "open" );
		return false;
	});
});