(function($){
	$(document).ready(function(){

		var deactivation_started = false;
		var clear_all_log_started = false;

    $(document).on('click','.button-uat-deactivate-licence, .uat_deactivate_license_key',function(e){
      if ( ! deactivation_started ) {
        e.preventDefault();
        $('.uat-admin-popup.uat-admin-popup-deactivate').fadeIn(200);
      } else {
        $(this).closest('form').submit();
      }
    });

    $(document).on('click','.uat-et-trigger-box-actions-f.uat-et-help-example-f h4',function(e){
  		e.preventDefault();
  		$(this).closest('.uat-et-help-example-f').toggleClass('open');
  		$(this).closest('.uat-et-help-example-f').find('.trigger-collapse-example').slideToggle(300);
  	});

    if(window.location.hash) {
      var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
      if ( hash.indexOf('moove-accordion-') === 0 && $('#'+hash).length > 0 ) {
      	var item = $('a.moove-accordion-section-title[href=\\#'+hash+']');
      	// Add active class to section title
				item.addClass('active');
				// Open up the hidden content panel
				$('.moove-accordion #' + hash).slideDown(200).addClass('open');

      	$('body,html').animate({
          scrollTop: $('#'+hash).offset().top - 100
        }, 300);
      }
    }

    var reset_settings_confirmed = false;
    $(document).on('submit','.uat-reset-settings-form',function(e){
    	if ( ! reset_settings_confirmed ) {
	    	e.preventDefault();
	    	$('.uat-admin-popup.uat-admin-popup-reset-settings').fadeIn(200);
	    	return false;
    	}
    });

    $(document).on('click','.uat-admin-popup-reset-settings .button-reset-settings-confirm-confirm', function(){
    	reset_settings_confirmed = true;
    	$('.uat-reset-settings-form').submit();
    	$('.uat-admin-popup.uat-admin-popup-reset-settings').fadeOut(200);
    });

    $(document).on('click', '#uat-settings-cnt .nav-tab-collapse', function(){
    	if ( $('#uat-settings-cnt').hasClass('uat-collapsed') ) {
    		// Uncollapse
    		$('#uat-settings-cnt').removeClass('uat-collapsed');
    		
    		var page_url_update = $('#post-query-submit').attr('data-pageurl');
    		page_url_update = page_url_update  && page_url_update.indexOf("&collapsed") !== -1 ? page_url_update.replace('&collapsed','') : page_url_update;
    		$('#search-submit').attr('data-pageurl',page_url_update);
    		$('#post-query-submit').attr('data-pageurl',page_url_update);
    		$(document).find('th.manage-column.sortable').each(function(){
    			var anchor = $(this).find('a');
    			var link = anchor.attr('href');
    			link = link.indexOf("&collapsed") !== -1 ? link.replace('&collapsed','') : link;
    			anchor.attr('href',link);
    		});

    	} else {
    		// Collapse
    		$('#uat-settings-cnt').addClass('uat-collapsed');
    		
    		var page_url_update = $('#post-query-submit').attr('data-pageurl');
    		page_url_update = page_url_update && page_url_update.indexOf("&collapsed") !== -1 ? page_url_update : page_url_update + '&collapsed';
    		$('#search-submit').attr('data-pageurl',page_url_update);
    		$('#post-query-submit').attr('data-pageurl',page_url_update);
    		$(document).find('th.manage-column.sortable').each(function(){
    			var anchor = $(this).find('a');
    			var link = anchor.attr('href');
    			link = link.indexOf("&collapsed") !== -1 ? link : link + '&collapsed';
    			anchor.attr('href',link);
    		});

    	}
    	
    });

    $(document).on('click','.uat-admin-popup .uat-popup-overlay, .uat-admin-popup .uat-popup-close',function(e){
      e.preventDefault();
      $(this).closest('.uat-admin-popup').fadeOut(200);
    });

    $(document).on('click','.uat-admin-popup.uat-admin-popup-deactivate .button-deactivate-confirm',function(e){
      e.preventDefault();
      deactivation_started = true;
      $("<input type='hidden' value='1' />")
       .attr("id", "uat_deactivate_license")
       .attr("name", "uat_deactivate_license")
       .appendTo("#moove_uat_license_settings");
      $('#moove_uat_license_settings').submit();
      $(this).closest('.uat-admin-popup').fadeOut(200);
    });

    $(document).on('click','.uat-cookie-alert .uat-dismiss', function(e){
      e.preventDefault();
      $(this).closest('.uat-cookie-alert').slideUp(400);
      var ajax_url = $(this).attr('data-adminajax');
      var user_id = $(this).attr('data-uid');

      jQuery.post(
        ajax_url,
        {
          action: 'moove_hide_language_notice',
          user_id: user_id
        },
        function( msg ) {

        }
      );
    });


		if( $( '.moove-activity-log-report .moove-form-container select' ).length > 0 ) {
			$( '.moove-form-container select' ).select2();
			$( document.body ).on( "click", function() {
				$( '.moove-form-container select' ).select2();
			});
		}

		$('.moove-activity-screen-meta .moove-activity-columns-tog').on('change',function(){
			var classname = '.column-' + $(this).val();
			if ( $(this).is(':checked') ) {
				$('.moove-form-container').find(classname).removeClass('hidden');
			} else {
				$('.moove-form-container').find(classname).addClass('hidden');
			}
			save_user_options( false );
		});
		function save_user_options( page_reload ) {
			var form_data = $('#adv-settings').serialize();
			$.post(
				ajaxurl,
				{
					action: "moove_activity_save_user_options",
					form_data : form_data,
					nonce : $('#uat_screen_settings_ajax_nonce').val()
				},
				function( msg ) {
					// console.warn(msg);
					if ( page_reload ) {
						location.reload();
					}
				}
				);
		}
		$(document).on('click','#moove-activity-screen-options-apply',function(e){
			e.preventDefault();
			save_user_options( true );

		});

		var individual_box_confirmed = false;
		// delete backlink
		$('body').on('change', '.ma-checkbox', function (e) {
			if ( $(this).is(':checked') ) {
				$('.uat-admin-popup-clear-log-confirm').fadeIn(200);
				$('.ma-checkbox').prop('checked',false);
				return false;
			}
			
		});

		$(document).on('click','.button-disable-tracking-individual-post', function(e){
			e.preventDefault();
			individual_box_confirmed = true;
			$('.ma-checkbox').prop('checked',true);
			$('.uat-admin-popup-clear-log-confirm').fadeOut(200);
		});

		$(document).on('change','input[name="moove-activity-dtf"]',function(){
			console.log($(this).val());
			if ( $(this).val() === 'c' ) {
				$('#screen-options-wrap .moove-activity-screen-ctm').removeClass('moove-hidden');
			} else {
				$('#screen-options-wrap .moove-activity-screen-ctm').addClass('moove-hidden');
			}
		});
		function moove_check_screen_options() {
			if ( $('.moove-activity-screen-meta #screen-meta').length > 0 ) {
				$('.moove-activity-screen-meta #screen-meta .moove-activity-columns-tog').each(function(){
					var classname = '.column-' + $(this).val();
					if ( $(this).is(':checked') ) {
						$('.moove-activity-log-report .load-more-container').find(classname).removeClass('hidden');
					} else {
						$('.moove-activity-log-report .load-more-container').find(classname).addClass('hidden');
					}
				});
			}
		}
		// ACCORDION SETTINGS
		function close_accordion_section() {
			$('.moove-accordion .moove-accordion-section-title').removeClass('active');
			$('.moove-accordion .moove-accordion-section-content').slideUp(300).removeClass('open');
		}

		$('.moove-accordion-section-title').click(function(e) {
			// Grab current anchor value
			var currentAttrValue = $(this).attr('href');

			if($(e.target).is('.active')) {
				close_accordion_section();
			}else {
				close_accordion_section();

					// Add active class to section title
					$(this).addClass('active');
					// Open up the hidden content panel
					$('.moove-accordion ' + currentAttrValue).slideDown(200).addClass('open');
				}

				e.preventDefault();
			});

		// LOAD MORE BUTTONS
		$('.moove-activity-log-report').on('click', '.uat-button.load-more', function(e) {
			e.preventDefault();

			var accordion = $(this).closest('.moove-accordion-section-content').find('table');
			accordion = accordion.length > 0 ? accordion : $('#moove-activity-log-table-global');

			var id = '#' + accordion.attr('id')+' tbody',
			offset = parseInt($(this).attr('data-offset'))+1,
			link = $(this).attr('href')+'&offset='+offset;
			var $element = $(id);
			if ( $(this).closest('.moove-accordion-section-content').length > 0 ) {
				$element = $(this).closest('.moove-accordion-section-content').find('tbody');
			}

			$('.moove-activity-log-report .load-more-container').load(link +' '+id+' tr', function(){
				moove_check_screen_options();				

				$element.append($('.moove-activity-log-report .load-more-container').html());
			});

			if ( offset == parseInt( $(this).attr('data-max') ) ) {
				$(this).hide();
			} else {
				$(this).attr('data-offset',offset);
			}
		});

		$('.moove-form-container').on('click', '#post-query-submit', function(e){
			e.preventDefault();
			var page_url = $(this).attr('data-pageurl'),
			selected_date = $('#filter-by-date option:selected').val(),
			selected_post_type = $('#post_types option:selected').val(),
			user_selected = $('#uid option:selected').val(),
			role_selected = $('#user_role option:selected').val(),
			searched = $('#post-search-input').val();
			if ( $('#uid').length > 0 ) {
				var new_url = page_url + '&m=' + selected_date + '&cat=' + selected_post_type + '&uid=' + user_selected + '&user_role=' + role_selected + '&s=' + searched;
			} else {
				var new_url = page_url + '&m=' + selected_date + '&cat=' + selected_post_type + '&s=' + searched;
			}
			window.location.replace( new_url );
		});

		// CONFIRM ON DISABLE/ENABLE logging

		$('select.moove-activity-log-settings').on('change', function() {
			if ($(this).val() == '0' && parseInt($(this).attr('data-postcount'))) {
				if (!confirm('Are you sure? \nYou have '+$(this).attr('data-postcount')+' posts, where are log data!')) {
						$(this).val('1'); //set back
						return;
					}
				}
			});

		$('.moove-form-container .all-logs-header').on('click', '#search-submit', function(e){
			e.preventDefault();
			var page_url = $(this).attr('data-pageurl'),
			selected_date = $('#filter-by-date option:selected').val(),
			selected_post_type = $('#post_types option:selected').val(),
			user_selected = $('#uid option:selected').val(),
			role_selected = $('#user_role option:selected').val(),
			searched = $('#post-search-input').val();
			if ( $('#uid').length > 0 ) {
				var new_url = page_url + '&m=' + selected_date + '&cat=' + selected_post_type + '&uid=' + user_selected + '&user_role=' + role_selected + '&s=' + searched;
			} else {
				var new_url = page_url + '&m=' + selected_date + '&cat=' + selected_post_type + '&s=' + searched;
			}

			window.location.replace( new_url );
		});

		//CLEAR LOGS BUTTON
		var toggle_log_button = false;
		var clear_type = '';
		$(document).on('click','.moove-activity-log-report .clear-all-logs',function(e){
      if ( ! clear_all_log_started ) {
        e.preventDefault();
        toggle_log_button = $(this);
        clear_type = 'all';
        $('.uat-admin-popup-clear-log-confirm').fadeIn(200);
      }
    });

    $(document).on('click','.moove-activity-log-report .clear-log',function(e){
      if ( ! clear_all_log_started ) {
        e.preventDefault();
        toggle_log_button = $(this);
        clear_type = 'single';
        $('.uat-admin-popup-clear-log-confirm').fadeIn(200);
      }
    });

    $(document).on('click','.moove-activity-log-report .clear-log-user',function(e){
      if ( ! clear_all_log_started ) {
        e.preventDefault();
        toggle_log_button = $(this);
        clear_type = 'user';
        $('.uat-admin-popup-clear-session-log-confirm').fadeIn(200);
      }
    });

    $(document).on('click','.uat-admin-popup-clear-session-log-confirm .button-primary.clear-session-logs',function(e){
    	e.preventDefault();
      clear_all_log_started = true;
      toggle_log_button.hide();
			var id = '#'+toggle_log_button.parent().closest('table').attr('class')+' tbody',
			link = toggle_log_button.attr('href')+'&clear-session-log='+ toggle_log_button.attr('data-uid'),
			accordion_id = '#'+toggle_log_button.closest('.moove-accordion-section-content').attr('id'),
			$post_title = $('.moove-accordion-section-title[href="' + accordion_id + '"');
			console.warn($(this).closest('.moove-accordion-section-content'));
			$('.moove-activity-log-report .load-more-container').load(link +' '+id+' tr', function(){
				$('#moove-activity-message-cnt').empty().html('<div id="message" class="error notice notice-error is-dismissible"><p>Activity Logs for <strong>' + $post_title.text() + '</strong> removed.</p></div>');
				$(accordion_id).slideToggle( 100, function(){
					$post_title.hide();
				});				
			});
			clear_all_log_started = false;
			$(this).closest('.uat-admin-popup').fadeOut(200);
    });
    

    $(document).on('click','.uat-admin-popup-clear-log-confirm .button-primary',function(e){
      e.preventDefault();
      clear_all_log_started = true;
      if ( clear_type === 'all' ) {
	      var id = '.'+toggle_log_button.closest('table').attr('class')+' tbody',
				link = toggle_log_button.attr('href')+'&clear-all-logs=1';
				$('.moove-activity-log-report .load-more-container').load(link +' '+id+' tr', function(){
					$('#moove-activity-message-cnt').empty().html('<div id="message" class="error notice notice-error is-dismissible"><p>Activity Logs removed.</p></div>');
					toggle_log_button.closest('.moove-form-container').find('table tbody').empty().html('<tr class="no-items"><td class="colspanchange" colspan="7">No posts found.</td></tr>');
					$('#moove-activity-buttons-container').empty();
					$('.moove-activity-log-report .tablenav .displaying-num').hide();
					toggle_log_button.hide();
				});
			} else {
				toggle_log_button.hide();
				var id = '#'+toggle_log_button.parent().closest('table').attr('class')+' tbody',
				link = toggle_log_button.attr('href')+'&clear-log='+ toggle_log_button.attr('data-pid'),
				accordion_id = "#moove-accordion-" + toggle_log_button.attr('data-pid'),
				$post_title = $('.moove-accordion-section-title[href="' + accordion_id + '"');

				$('.moove-activity-log-report .load-more-container').load(link +' '+id+' tr', function(){
					$('#moove-activity-message-cnt').empty().html('<div id="message" class="error notice notice-error is-dismissible"><p>Activity Logs for <strong>' + $post_title.text() + '</strong> removed.</p></div>');
					$(accordion_id).slideToggle( 100, function(){
						$post_title.hide();
					});
				});
			}
			clear_all_log_started = false;
      $(this).closest('.uat-admin-popup').fadeOut(200);
    });



}); // end document ready



})(jQuery);
