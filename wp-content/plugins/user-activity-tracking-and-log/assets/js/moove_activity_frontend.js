(function($){
	var log_id = 0;
	$(document).ready(function() {

		$.post(
			moove_frontend_activity_scripts.ajaxurl,
			{
				action: "moove_activity_track_pageview",
				post_id: moove_frontend_activity_scripts.post_id,
				is_single: moove_frontend_activity_scripts.is_single,
				is_page: moove_frontend_activity_scripts.is_page,
				user_id: moove_frontend_activity_scripts.current_user,
				referrer: moove_frontend_activity_scripts.referrer,
				extras: moove_frontend_activity_scripts.extras,
			},
			function( msg ) {				
				try	{
					var response = msg ? JSON.parse(msg) : false;
					if ( typeof response === 'object' && response.id ) {
						log_id = response.id;
						try {
							if ( typeof moove_frontend_activity_scripts.extras !== 'undefined' ) {
								var extras_obj = JSON.parse( moove_frontend_activity_scripts.extras );
								if ( typeof extras_obj.ts_status !== 'undefined' && extras_obj.ts_status === '1' ) {
									window.addEventListener('beforeunload', function(event) {
										console.warn(log_id);
										if ( typeof log_id !== 'undefined' ) {
											$.post(
												moove_frontend_activity_scripts.ajaxurl,
												{
													action: "moove_activity_track_unload",
													log_id: log_id,
												},
												function( msg ) {
													// console.warn(msg);
												}
											);
										}
									});
								}
							}
						} catch(e) {
							console.error(e);
						}
					}
				} catch(e){
					console.error(e);
				}
			}
		);
	});

})(jQuery);

