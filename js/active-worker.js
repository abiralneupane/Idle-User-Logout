jQuery(document).ready(function(){
	if( typeof(iul) != 'undefined' ){
		console.log(jQuery.data(document,'idleTimer'));
		var disable_admin = iul.actions.disable_admin;
		if(!disable_admin){
			jQuery(document).bind("active.idleTimer", function(){
				jQuery.ajax({
			        type: 'POST',
			        url: iul.ajaxurl,
			        data: {action: 'update_user_time' },
			        error: function(MLHttpRequest, textStatus, errorThrown){ console.log(errorThrown); },
			        success: function(response){
			            console.log(response);
			        }
			    });
			});
		}
	}
});