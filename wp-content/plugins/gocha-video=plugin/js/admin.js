/*
 * GOCHA Video Plugin - back-end JS code
 */

var last_save = 0;
var last_change = 0;

(function($) {
	var main_wrapper = $('#gocha-video-plugin');

	if(!main_wrapper.length) {
		return;
	}

	/*
	 * Run the instance in DOMContentLoaded events
	 */
	$(function() {
		init_ajax_settings_save();
		init_ajax_restore_defaults();
		init_options_binding();
		init_settings_save_alert();
        init_color_pickers();
	});

	/*
	 * Function used for saving settings with AJAX
	 */
	var init_ajax_settings_save = function() {
		var submit_buttons = main_wrapper.find('.button-submit');

		submit_buttons.each(function(i, button) {
			button = $(button);

			button.on('click', function() {
				var setting_fields = main_wrapper.find('.settings-field');
				var settings = {};

				for(var j = 0; j < setting_fields.length; j++) {
					var field = $(setting_fields[j]);

					if(field.hasClass('settings-checkbox')) {
						if(field.hasClass('settings-multiple-checkbox')) {
							if(settings[field.attr('name')] && settings[field.attr('name')] !== '') {
								settings[field.attr('name')] += field.prop('checked') ? ',' + field.val() : '';
							} else {
								settings[field.attr('name')] = field.prop('checked') ? field.val() : '';
							}
						} else {
							settings[field.attr('name')] = field.prop('checked') ? 1 : 0;
						}

						continue;
					} else if(field.hasClass('settings-radio')) {
						settings[field.attr('name')] = field.parents('td').find('input[name="'+field.attr('name')+'"]:checked').val();
						continue;
					} else {
						field.val(sanitize_field(field.val()));
					}

					settings[field.attr('name')] = field.val();
				}

				var request_data = {
					action: 'gocha_video_settings_save',
					nonce: ajax_gocha_video_var.nonce_settings_save,
					settings: settings
				};

				var error_message = button.parent().find('.error-message');
				var spinner = button.parent().find('.spinner');
				spinner.addClass('is-active');
				button.prop('disabled', true);
				error_message.addClass('hidden');
				last_save = new Date().getTime();

				$.ajax({
					cache: false,
					data: request_data,
					method: 'post',
					url: ajaxurl
				}).done(function(response) {
					// noop
				}).fail(function() {
					error_message.removeClass('hidden');
				}).always(function() {
					spinner.removeClass('is-active');
					button.prop('disabled', false);
				});
			});
		});
	};

	/*
	 * Function used to restore the default settings
	 */
	var init_ajax_restore_defaults = function() {
		var button = $('#settings-main-restore-defaults');

		button.on('click', function(e) {
			e.preventDefault();

			var user_reply = confirm(ajax_gocha_video_var.restore_alert);

			if(!user_reply) {
				return false;
			}

			var request_data = {
				action: 'gocha_video_restore_defaults',
				nonce: ajax_gocha_video_var.nonce_restore_defaults
			};

			$.ajax({
				cache: false,
				data: request_data,
				method: 'post',
				url: ajaxurl
			}).done(function(response) {
				window.location.reload();
			}).fail(function() {
				alert(ajax_gocha_video_var.restore_alert);
			});
		});
	};

	/*
	 * Function used to display options only if setting have a specific value
	 */
	var init_options_binding = function() {
		$('*[data-show-option]').each(function(i, field_to_show) {
			field_to_show = $(field_to_show);
			var trigger_fields = field_to_show.attr('data-show-option').split(';');

			$.each(trigger_fields, function(i, trigger_field) {
				trigger_field = $('#' + trigger_field);
				show_options_depends_on_binding(field_to_show, trigger_fields);

				trigger_field.on('change', function() {
					show_options_depends_on_binding(field_to_show, trigger_fields);
				});
			});
		});

		$('#settings-main-processing-step').on('change', function() {
			processing_helper.processing_step = parseInt($(this).val(), 10);
		});
	};

    /*
	 * Function used to create color pickers
	 */
	var init_color_pickers = function() {
		$('.settings-color-picker').wpColorPicker({
			change: function(event, ui) {
				// triggering event without minimal timeout blocks the change event
				setTimeout(function() {
					$(event.target).trigger('change');
				}, 0);
			}
		});
	};

	var show_options_depends_on_binding = function(field_to_show, trigger_fields) {
		// check all conditions for the field to show
		var show_field = true;
		var show_values = field_to_show.attr('data-show-values').split(';').map(function(values) {
			return values.split(',');
		});

		for(var i = 0; i < trigger_fields.length; i++) {
			var trigger_field = $('#' + trigger_fields[i]);

			if(show_values[i][0] === '!:checked') {
				if(trigger_field.prop('checked')) {
					show_field = false;
					break;
				}
			}

			if(show_values[i][0] === ':checked') {
				if(!trigger_field.prop('checked')) {
					show_field = false;
					break;
				}
			}

			if(
				show_values[i][0] !== ':checked' &&
				show_values[i][0] !== '!:checked' &&
				show_values[i].indexOf(trigger_field.val()) === -1
			) {
				show_field = false;
				break;
			}
		}

		if(show_field) {
			field_to_show.parents('tr').removeClass('hidden');
		} else {
			field_to_show.parents('tr').addClass('hidden');
		}
	};

	/*
	 * Function used to display the settings change alert
	 */
	var init_settings_save_alert = function() {
		$('.settings-field, .preset-field').each(function(i, field) {
			field = $(field);

			field.on('change', function() {
				last_change = new Date().getTime();
			});
		});
		// Popup displayed before window unload
		$(window).on('beforeunload', function() {
      		if(last_change > last_save) {
				return ajax_gocha_video_var.unsaved_notice;
			} else {
				return;
			}
		});
	};

	/*
	 * Function to sanitize form during save
	 */
	sanitize_field = function(input) {
	  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
	  var comments = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;

	  return input.replace(tags, '').replace(comments, '');
  	};
})(jQuery);
