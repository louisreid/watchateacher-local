;(function($) {
    var Gocha_Video_Popup = function() {
        this.init();
    };

    Gocha_Video_Popup.prototype.init = function() {
        var self = this;
        this.popup = $('#gocha-video-tinymce-popup');
        this.close_button = this.popup.find('.gocha-video-popup-close');
        this.cancel_button = this.popup.find('.gocha-video-popup-cancel');
        this.save_button = this.popup.find('.gocha-video-popup-save');
        this.popup_header = this.popup.find('.gocha-video-popup-header');

        this.close_button.on('click', function(e) {
            e.preventDefault();
            self.close();
        });

        this.cancel_button.on('click', function(e) {
            e.preventDefault();
            self.close();
        });

        this.save_button.on('click', function(e) {
            e.preventDefault();
            var result = self.generate_shortcode();

            // Replace or insert the shortcode regarding the mode
            if(self.popup.attr('data-shortcode') !== '') {
                var new_content = self.editor.getContent().replace(self.popup.attr('data-shortcode').replace(/&/g, '&amp;'), result);
                self.editor.setContent(new_content);
            } else {
                self.editor.insertContent(result);
            }

            self.close();
        });

        this.popup.find('.settings-field[data-show-option], .content-field[data-show-option]').each(function(i, field_to_show) {
            field_to_show = $(field_to_show);
            var trigger_fields = field_to_show.attr('data-show-option').split(';');

            $.each(trigger_fields, function(i, trigger_field) {
                trigger_field = $('#' + trigger_field);
                self.show_options_depends_on_binding(field_to_show, trigger_fields);

                trigger_field.on('change', function() {
                    self.show_options_depends_on_binding(field_to_show, trigger_fields);
                });
            });
        });

        this.init_accordion();
        this.init_color_pickers();
    };

    Gocha_Video_Popup.prototype.init_color_pickers = function() {
        this.popup.find('.settings-color-picker').wpColorPicker({
            change: function(event, ui) {
                setTimeout(function() {
                    $(event.target).trigger('change');
                }, 0);
            }
        });
    };

    Gocha_Video_Popup.prototype.open = function(editor) {
        $('.gocha-video-popup').addClass('hidden');
        this.popup.removeClass('hidden');
        this.popup_header.text(this.popup_header.attr('data-new'));
        this.save_button.text(this.save_button.attr('data-new'));
        this.editor = editor;
        this.popup.attr('data-shortcode', '');
        this.set_default_values();
    };

    Gocha_Video_Popup.prototype.edit = function(editor, shortcode) {
        $('.gocha-video-popup').addClass('hidden');
        this.popup.removeClass('hidden');
        this.popup_header.text(this.popup_header.attr('data-edit'));
        this.save_button.text(this.save_button.attr('data-edit'));
        this.editor = editor;
        this.parse_shortcode(shortcode);
        this.popup.attr('data-shortcode', shortcode);
    };

    Gocha_Video_Popup.prototype.close = function() {
        this.popup.addClass('hidden');
    };

    Gocha_Video_Popup.prototype.init_accordion = function() {
        var titles = this.popup.find('.gocha-video-popup-accordion-title');
        var contents = this.popup.find('.gocha-video-popup-accordion-content');

        titles.each(function(i, title) {
            $(title).on('click', function() {
                for(var j = 0; j < titles.length; j++) {
                    $(titles[j]).attr('aria-expanded', j == i ? 'true' : 'false');
                    $(contents[j]).attr('aria-expanded', j == i ? 'true' : 'false');
                }
            });
        })
    };

    Gocha_Video_Popup.prototype.show_options_depends_on_binding = function(field_to_show, trigger_fields) {
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
            field_to_show.parents('.gocha-video-popup-control').removeClass('hidden');
        } else {
            field_to_show.parents('.gocha-video-popup-control').addClass('hidden');
        }
    };

    Gocha_Video_Popup.prototype.set_default_values = function() {
        var settings_fields = this.popup.find('.settings-field');
        var self = this;

        settings_fields.each(function(i, field) {
            field = $(field);

            if(field.hasClass('settings-checkbox')) {
                field.trigger('change');
                field.prop('checked', field.attr('data-default') == 1);
            } else if(field.hasClass('settings-radio')) {
                field.trigger('change');
                field.parents('div').find('input').each(function() {
                    field.prop('checked', field.attr('data-default') == field.val());
                });
            } else {
                field.val(field.attr('data-default'));
                field.trigger('change');
            }
        });
    };

    Gocha_Video_Popup.prototype.sanitize_field = function(input) {
        if(!input) {
            return '';
        }

        var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
        var comments = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;

        return input.replace(tags, '').replace(comments, '');
      };

    Gocha_Video_Popup.prototype.generate_attributes = function() {
        var self = this;
        var settings = {};
        var settings_fields = this.popup.find('.settings-field');

        settings_fields.each(function(i, field) {
            field = $(field);
            var name = field.attr('name').replace('popup-', '');
            var field_value = '';

            if(field.hasClass('settings-checkbox')) {
                if(field.hasClass('settings-multiple-checkbox')) {
                    if(settings[name] && settings[name] !== '') {
                        field_value += field.prop('checked') ? ',' + field.val() : '';
                    } else {
                        field_value = field.prop('checked') ? field.val() : '';
                    }
                } else {
                    field_value = field.prop('checked') ? '1' : '0';
                }
            } else if(field.hasClass('settings-radio')) {
                field_value = field.parents('div').find('input[name="'+field.attr('name')+'"]:checked').first().val();
            } else {
                field.val(self.sanitize_field(field.val()));
                field_value = field.val();
            }

            if(
                (field.attr('data-default') || field.attr('data-default') === "") &&
                field_value === field.attr('data-default') &&
                field.attr('data-popup-only') !== 'true'
            ) {
                return;
            }

            settings[name] = field_value;
        });

        return settings;
    };

    Gocha_Video_Popup.prototype.parse_attributes = function(shortcode_data) {
        var self = this;
        var settings_fields = this.popup.find('.settings-field');

        settings_fields.each(function(i, field) {
            field = $(field);
            var simplified_name = field.attr('name').replace('popup-', '');
            var new_value = shortcode_data[simplified_name];

            if(!new_value) {
                new_value = field.attr('data-default');
            }

            if(field.hasClass('settings-checkbox')) {
                field.trigger('change');
                field.prop('checked', new_value == 1);
            } else if(field.hasClass('settings-radio')) {
                field.trigger('change');
                field.parents('div').find('input[value="'+new_value+'"]').prop('checked', true);
            } else {
                new_value = self.sanitize_field(new_value);
                field.val(new_value);
                field.trigger('change');
            }
        });
    };

    Gocha_Video_Popup.prototype.generate_shortcode = function() {
        // Generate attributes
        var settings = this.generate_attributes();

        // Final output
        var shortcode_output = '[gocha_video ';
        shortcode_output += Object.keys(settings).map(function(key) {
            return key + '="' + settings[key] + '"';
        }).join(' ');

        if(shortcode_output == '[gocha_video ') {
            shortcode_output = '[gocha_video';
        }

        shortcode_output += ']';
        // Put/replace the code in the editor
        return shortcode_output;
    };

    Gocha_Video_Popup.prototype.parse_shortcode = function(shortcode) {
        var shortcode_data = {};
        var shortcode_temp = shortcode.replace('[gocha_video ', '').replace(']', '');
        shortcode_temp = shortcode_temp.split(' ');

        for(var i = 0; i < shortcode_temp.length; i++) {
            var pair = shortcode_temp[i].split('="');

            if(pair.length === 2) {
                var pair_key = pair[0];
                var pair_value = pair[1].replace(/"/g, '');
                shortcode_data[pair_key] = pair_value;
            }
        }

        // Generate attributes
        this.parse_attributes(shortcode_data);
    };

    /*
     * Debounce function helper
     */
    var debounce = function(func, wait, immediate) {
        var timeout;

        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };

            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);

            if (callNow) {
                func.apply(context, args);
            }
        };
    };

    // TinyMCE helper tooltip template
    var gocha_video_tinymce_tooltip_template = '<div id="gocha_video_tinymce_tooltip" class="mce-toolbar-grp mce-inline-toolbar-grp mce-container mce-panel"><div class="mce-container-body mce-stack-layout"><div class="mce-container mce-toolbar mce-stack-layout-item mce-first mce-last"><div class="mce-container-body mce-flow-layout"><div class="mce-container mce-flow-layout-item mce-first mce-last mce-btn-group"><div><div class="mce-widget mce-btn"><button id="gocha_video_tinymce_tooltip_edit"><i class="mce-ico mce-i-dashicon dashicons-edit"></i></button></div><div class="mce-widget mce-btn mce-last"><button id="gocha_video_tinymce_tooltip_delete"><i class="mce-ico mce-i-dashicon dashicons-no"></i></button></div></div></div></div></div></div></div>';

    function gocha_video_init_inline_edit(editor) {
        var iframe_element = $('#content_ifr');
        var iframe = iframe_element.contents();
        var body = iframe.find('#tinymce');
        // We need to append element inside the dashboard not the editor
        var document_body = $(document.body);
        document_body.append($(gocha_video_tinymce_tooltip_template));
        var tooltip = document_body.find('#gocha_video_tinymce_tooltip');
        var tooltip_edit = document_body.find('#gocha_video_tinymce_tooltip_edit');
        var tooltip_delete = document_body.find('#gocha_video_tinymce_tooltip_delete');
        tooltip.css('display', 'none');
        tooltip.css('position', 'absolute');
        tooltip.css('z-index', '1000');
        var selected = null;
        var selected_text = '';
        var whole_text = '';

        $('#content-html').on('click', function() {
            tooltip.css('display', 'none');
        });

        tooltip_edit.on('click', function() {
            tooltip.css('display', 'none');
            var shortcode = selected_text.match(/\[gocha_video.*?\]/gmi)[0];

            if(shortcode.indexOf('[gocha_video') !== -1) {
                popup.edit(editor, shortcode);
            }
        });

        tooltip_delete.on('click', function() {
            body.html(whole_text.replace(selected_text.match(/\[gocha_video.*\]/mi)[0], ''));
            tooltip.css('display', 'none');
        });

        $(iframe_element.contents()[0]).on('mouseup', function() {
            selected = iframe[0].getSelection();
            selected_text = selected.toString().replace(/^\s+/g, '').replace(/\s+$/g, '');
            whole_text = body.html();

            if(
                selected_text !== '' &&
                whole_text.indexOf('[gocha_video') !== -1 &&
                new RegExp(/\[gocha_video.*?\]/mi).test(selected_text) &&
                selected_text.match(/\[gocha_video.*?\]/gmi).length === 1
            ) {
                oRange = selected.getRangeAt(0);
                oRect = oRange.getBoundingClientRect();

                tooltip.css('display', 'block');
                // calculate the tooltip position in relation to the editor iframe position
                tooltip.css('top',  iframe_element.offset().top + ((oRect.top + (oRect.height / 2)) - 16) + "px");
                tooltip.css('left', iframe_element.offset().left +  ((oRect.left + (oRect.width / 2)) - 50) + "px");

                return;
            } else {
                tooltip.css('display', 'none');
            }
        });
    }

    // Popup instances
    var popup = false;

    if($('#gocha-video-tinymce-popup').length) {
        popup = new Gocha_Video_Popup();
    }

    // TinyMCE Button
    tinymce.PluginManager.add('gocha_video_button', function( editor, url ) {
        /*
         * Add buttons for the shortcode inline edit
         */
        setTimeout(function() {
            gocha_video_init_inline_edit(editor);
        }, 1000);

        editor.addButton( 'gocha_video_button', {
            title: ajax_gocha_video_var.tinymce_button_label,
            type: 'button',
            icon: 'icon dashicons-video-alt3',
            onclick: function() {
                popup.open(editor);
            }
        });
    });
})(jQuery);
