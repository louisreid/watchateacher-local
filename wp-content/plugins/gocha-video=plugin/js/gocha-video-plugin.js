(function($) {
    "use strict";

    var necessaryAPIs = [];
    var loadedAPIs = [];
    var loadTries = 0;
    var APIs = {
        'youtube': '//www.youtube.com/iframe_api',
        'vimeo': '//player.vimeo.com/api/player.js',
        'dailymotion': 'https://api.dmcdn.net/all.js',
        'fb': '//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6'
    };

    // Load scripts on DOMContentLoaded
    $(function() {
        if($('.gocha-video-youtube').length) {
            necessaryAPIs.push('youtube');
        }

        if($('.gocha-video-vimeo').length) {
            necessaryAPIs.push('vimeo');
        }

        if($('.gocha-video-dailymotion').length) {
            necessaryAPIs.push('dailymotion');
        }

        if($('.gocha-video-fb').length) {
            necessaryAPIs.push('fb');
        }

        for(var i = 0; i < necessaryAPIs.length; i++) {
            loadAsyncAPI(necessaryAPIs[i]);
        }
    });

    // Load APIs scripts asynchronously
    function loadAsyncAPI(api) {
        var tag = document.createElement('script');
        var firstScriptTag = document.getElementsByTagName('script')[0];

        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        tag.onload = function() {
            loadedAPIs.push(api);
        };
        tag.src = APIs[api];
    }

    // Check if all APIs scripts was loaded
    function checkAPIs() {
        if(necessaryAPIs.length === loadedAPIs.length) {
            initialize();
            return;
        }

        // If the load is longer than 10s - try to initialize anyway.
        if(loadTries > 50) {
            initialize();
            return;
        }

        loadTries++;
        setTimeout(function(checkAPIs) {
            checkAPIs;
        }, 200);
    }

    // Make sure that we can initialize our script
    $(window).on('load', function(){
        checkAPIs();
    });

    function initialize() {
        $('.gocha-video-commentarea').each(function(i, videoElement) {
            videoElement = $(videoElement);

            if(videoElement.data('gocha_video') === undefined) {
                var plugin = new GochaVideo(videoElement, gochavideoplugin_var);
                videoElement.data('gocha_video', plugin);
            }
        });
    }

    /*
     * Main plugin class
     */
    var GochaVideo = function(element, options) {
        this.wrapper = $(element);
        this.currentVideo = this.wrapper.attr('data-gocha-video');
        this.playerID = null;
        this.player = null;
        this.timeout = null;
        this.currentTime = 0;
        this.totalTime = 0;
        this.start = 0;
        this.stop = 0;
        this.stopPlay = null;
        this.playerState = null;
        this.commentReplyId = null;
        this.resizeTimeout = undefined;
        this.timeIsDragging = false;
        this.options = options;
        this.options.mintimediff = this.wrapper.attr('data-gocha-mintimediff');
        this.options.commentopen = this.wrapper.attr('data-gocha-commentopen');
        this.options.commentdisplay = this.wrapper.attr('data-gocha-commentdisplay');
        this.options.commentdisplaymode = this.wrapper.attr('data-gocha-commentdisplaymode');
        this.mode = '';
        // We need custom timer as FB Videos does not support
        // timeupdate event propagation
        this.fbTimer = false;
        //Player methods
        this.playerMethods = {
            play: 		function(){},
            pause: 		function(){},
            comment: 	function(startVideo) {},
            current: 	function(){}
        };
        //container elements
        this.ui = {
            controls: this.wrapper.find('.gocha-video-controls'),
            startBtn: this.wrapper.find('.gocha-video-start'),
            addBtn: this.wrapper.find('.gocha-video-add'),
            stopBtn: this.wrapper.find('.gocha-video-stop'),
            showBtn: this.wrapper.find('.gocha-video-show'),
            seeker: this.wrapper.find('.gocha-dynamic-seeker'),
            bar: this.wrapper.find('.gocha-bar'),
            barTime: this.wrapper.find('.gocha-bar-time'),
            commentBox: this.wrapper.find('.gocha-video-commentbox'),
            commentFormBox: this.wrapper.find('.gocha-video-commentform'),
            commentForm: this.wrapper.find('.gocha-video-comment-form'),
            formVideoInput: this.wrapper.find('.gocha-video-input'),
            formCommentParent: this.wrapper.find('.gocha-comment-parent'),
            formCancelReply: this.wrapper.find('.gocha-cancel-reply'),
            formH2: this.wrapper.find('.gocha-form-haader'),
            formStatus: this.wrapper.find('.gocha-form-status'),
        };

        this.mode = this.ui.controls.attr('data-mode');

        // Run the plugin
        this.init();
    };

    /*
     * Initialized plugin logic
     */
    GochaVideo.prototype.init = function(){
        var self = this;

        if('0' !== this.options.commentdisplay){
          this.ui.commentBox.addClass('gocha-dynamic');
        }

        //detect player type
        if(this.wrapper.hasClass('type-youtube')){
            this.initYoutube();
        }

        if(this.wrapper.hasClass('type-vimeo')){
            this.initVimeo();
        }

        if(this.wrapper.hasClass('type-dailymotion')){
            this.initDailyMotion();
        }

        if(this.wrapper.hasClass('type-mediaelement')){
            this.initMediaElement();
        }

        if(this.wrapper.hasClass('type-fb')) {
            this.initFB();
        }

        // add arrow
        if(this.mode !== 'point') {
            this.ui.addBtn.prop( "disabled", true ).addClass('gocha-start-button');
        }

        if(1 == this.options.commentopen || "true" == this.options.commentopen) {
            this.openComments();
        }

        this.initEvents();
        this.initAjaxForm();
    };

    /*
     * Initialize events
     */
    GochaVideo.prototype.initEvents = function() {
        var self = this;

        //Start timer button trigger
        this.wrapper.on( "click", ".gocha-video-start", function(){
            self.stopPlay = undefined;
            self.playerMethods.play();
            self.start = self.currentTime + 0.001;
            self.wrapper.find('.gtimenumber').text(self.toHHMMSS(self.start));

            if(parseInt(self.stop) > parseInt(self.start) ){
                self.ui.addBtn.prop( "disabled", false )
                              .addClass('g-active, gocha-start-button')
                              .text(self.options.txt_bb_form_show);
            } else {
                self.closeForm();
                self.ui.addBtn.prop( "disabled", true )
                              .removeClass('g-active, gocha-start-button')
                              .text(self.options.txt_error_time)
                              .addClass('gocha-stop-button');
                self.stop = 0;
            }
        });

        //Stop timer button trigger
        this.wrapper.on( "click", ".gocha-video-stop", function(){
            clearInterval(self.timeout);
            self.stopPlay = undefined;
            self.playerMethods.pause();
            self.stop = self.currentTime;
            self.wrapper.find('.gtimenumber').eq(2).text(self.toHHMMSS(self.stop));

            if(parseInt(self.stop) > parseInt(self.start) ){
                self.ui.addBtn.prop('disabled', false)
                              .addClass('g-active')
                              .text(self.options.txt_bb_form_show);
            } else {
                self.start = self.stop;
                self.ui.startBtn.find('.gtimenumber').text(self.toHHMMSS(self.start));
                self.closeForm();
                self.ui.addBtn.prop('disabled', false)
                              .addClass('g-active')
                              .text(self.options.txt_bb_form_show);
            }

            var inputArray = JSON.stringify([self.currentVideo, self.start, self.stop]);
            self.ui.formVideoInput.val(inputArray);
            self.ui.formCommentParent.val('');
            self.openForm();
            self.timeDifference(self.start, self.stop);
        });

        //Commentbox comment anchor link trigger
        this.wrapper.on( "click", ".gocha-video-comment-start", function(e){
            var commentLink = $(e.target);
            if(self.stopPlay !== undefined){
                self.stopPlay = undefined;
            }

            var startVideo = commentLink.parent().parent().parent().attr('data-gocha-start');
            self.stopPlay = commentLink.parent().parent().parent().attr('data-gocha-stop');
            self.scrollToComment("#" + self.wrapper.attr('id'), 500);
            self.playerMethods.comment(startVideo);
        });

        this.wrapper.on( "click", ".gocha-marker", function(){
            var marker = $(this);

            if(self.stopPlay !== undefined){
                self.stopPlay = undefined;
            }

            var startVideo = marker.attr('data-gvc-start');
            self.stopPlay = marker.attr('data-gvc-stop');
            self.scrollToComment("#" + self.wrapper.attr('id'), 500);
            self.playerMethods.comment(startVideo);
            self.openComments();
        });

        //commentbox toggle trigger
        this.wrapper.on( "click", ".gocha-video-show", function(){
            self.toggleComments();
        });

        this.wrapper.on( "click", ".gocha-video-add", function(){
            if($(this).text() === self.options.txt_bb_form_hide){
                self.start = 0;
                self.stop = 0;
                self.clearCommentReplay();
            } else {
                if(self.mode !== "point") {
                    self.ui.addBtn.prop( "disabled", true );
                    return;
                }

                if(self.type === 'dm' && !self.dmInitialized[self.currentVideo]) {
                    alert(self.options.txt_dm_alert_stop);
                    return;
                }

                self.playerMethods.pause();
                self.start = self.currentTime;
                self.stop = self.currentTime;
                var inputArray = JSON.stringify([self.currentVideo, self.start, self.stop]);
                self.ui.formVideoInput.val(inputArray);
                self.ui.formCommentParent.val('');
                self.openForm();
                self.timeDifference(self.start, self.stop);
            }
        });

        //Reply comment trigger
        this.wrapper.on( "click", ".gocha-video-replay-link" , function(){
            var commentID = $(this).attr('data-comment-id');
            self.commentReplay(commentID);
        });

        //Cancel reply comment - form trigger
        this.wrapper.on( "click", ".gocha-cancel-reply", function(){
            var commentID = self.ui.formCommentParent.val();
            self.commentReplay(commentID);
        });

        //timeline item hover
        this.wrapper.on({
            mouseenter: function(){
                if(self.mode !== 'point' && (self.options.commentdisplay === "true" || self.options.commentdisplay === "1")) {
                    return;
                }

                if(self.options.commentdisplaymode === 'hide') {
                    self.ui.commentBox.find('.gocha-video-comment')
                                      .not('[data-gocha-start="' + $(this).attr('data-gvc-start') + '"], [data-gocha-stop="' + $(this).attr('data-gvc-stop') + '"]')
                                      .attr('style', 'display: none!important;');
                    self.wrapper.find('.gocha-marker')
                                .not(this)
                                .css('opacity', 0.5);
                } else {
                    self.ui.commentBox.find('.gocha-video-comment')
                                      .not('[data-gocha-start="' + $(this).attr('data-gvc-start') + '"], [data-gocha-stop="' + $(this).attr('data-gvc-stop') + '"]')
                                      .css('opacity', 0.5);
                    self.wrapper.find('.gocha-marker')
                                .not(this)
                                .css('opacity', 0.5);
                }
            },
            mouseleave: function(){
                if(self.mode !== 'point' && (self.options.commentdisplay === "true" || self.options.commentdisplay === "1")) {
                    return;
                }

                if(self.options.commentdisplaymode === 'hide') {
                    self.ui.commentBox.find('.gocha-video-comment').attr('style', 'display: block!important;');
                    self.wrapper.find('.gocha-marker').css('opacity', 1);
                } else {
                    self.ui.commentBox.find('.gocha-video-comment').css('opacity', 1);
                    self.wrapper.find('.gocha-marker').css('opacity', 1);
                }
            }
        }, ".gocha-marker");

        this.wrapper.on({
            mouseenter: function(){
                self.wrapper.find('.gocha-marker')
                            .not('[data-gvc-start="' + $(this).attr('data-gocha-start') + '"], [data-gvc-stop="' + $(this).attr('data-gocha-stop') + '"]')
                            .css('opacity', 0.5);
            },
            mouseleave: function(){
                self.wrapper.find('.gocha-marker').css('opacity', 1);
            }
        }, ".gocha-video-commentbox .gocha-video-comment");

        //draggable seeker
        this.ui.seeker.on('mousedown', function(e) {
            self.timeIsDragging = true;
            self.updatebar(e.pageX);
        });

        $(document).on('mouseup', function(e) {
            if(self.timeIsDragging) {
                self.timeIsDragging = false;
                self.updatebar(e.pageX);
            }
        });

        $(document).on('mousemove', function(e) {
            if(self.timeIsDragging) {
                self.updatebar(e.pageX);
            }
        });

        //add smooth scroll to comments outside of container
        $(document).on('click', '.gocha-video-comment-anchor[data-gocha-video-id="' + this.currentVideo + '"]', function(){
            var startVideo = $(this).attr('data-gocha-start');
            var stopVideo = $(this).attr('data-gocha-stop');

            if(self.stopPlay !== undefined){
                self.stopPlay = undefined;
            }

            self.stopPlay = stopVideo;
            self.scrollToComment("#" + self.wrapper.attr('id'), 500);
            self.playerMethods.comment(startVideo);
        });
    };

    /*
     * Initialize AJAX form
     */
    GochaVideo.prototype.initAjaxForm = function() {
        var self = this;
        //ajax form
        this.ui.commentForm.on('submit', function(e){
            e.preventDefault();

            var formdata = $(this).serialize();
            self.ui.formStatus.fadeIn().html('<p>' + self.options.txt_ajax_wait + '</p>');
            var formurl = $(this).attr('action');
            var commentparentId = self.ui.formCommentParent.val();
            var commentinput = JSON.parse(self.ui.formVideoInput.val());
            var validate_author = $(this).find('#author').val();
            var validate_email = $(this).find('#email').val();
            var validate_url = $(this).find('#url').val();
            var validate_comment = $(this).find('#comment').val();
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            var validate_email = regex.test(validate_email);

            if(
                (validate_author === undefined && validate_comment.length > 1) ||
                (validate_author !== undefined && validate_author.length > 1 && validate_email === true && validate_comment.length > 1)
            ) {
                $.ajax({
                    type: 'post',
                    url: formurl,
                    data: formdata,

                    error: function(xhr, textStatus, errorThrown){
                        var errorMessage = xhr.responseText.match(/<body id="error-page">[^]*?<\/body>/gmi);

                        if(errorMessage[0]) {
                            errorMessage = errorMessage[0];
                            errorMessage = errorMessage.replace('<body id="error-page">', '');
                            errorMessage = errorMessage.replace('</body>', '');
                            errorMessage = errorMessage.replace('<p>', '');
                            errorMessage = errorMessage.replace('</p>', '');
                        }

                        if(!errorMessage) {
                            errorMessage = this.options.txt_ajax_delay;
                        }

                        self.ui.formStatus.html('<p class="ajax-error">'+errorMessage+'</p>');
                        //hide status area
                        setTimeout(function(){
                            self.ui.formStatus.fadeOut();
                        }, 8000);
                    },
                    success: function(data, textStatus) {
                        if(data.indexOf('id="error-page"') > -1) {
                            var errorMessage = self.options.txt_ajax_fillform;
                            self.ui.formStatus.html('<p class="ajax-error">'+errorMessage+'</p>');
                            //hide status area
                            setTimeout(function(){
                                self.ui.formStatus.fadeOut();
                            }, 8000);

                            return;
                        }

                        // set status message
                        if(textStatus === "success"){
                            self.ui.formStatus.fadeIn().html('<p class="ajax-success">' + self.options.txt_ajax_success + '</p>');

                            // hide status area
                            setTimeout(function(){
                                self.ui.formStatus.fadeOut();
                            }, 8000);

                            //append element
                            if(commentparentId !== undefined && commentparentId > 0) {
                                self.clearCommentReplay();
                                //get comment with ID from parent input
                                var commentparent = self.ui.commentBox.find('#gocha-div-comment-' + commentparentId);
                                //find children ul
                                if(commentparent.find('.children').first().length) {
                                    // ul found, add element
                                    commentparent.find('.children').first().prepend(data);
                                } else {
                                    //no ul found, add ul and element
                                    commentparent.append('<ul class="children">'+data+'</ul>');
                                }
                            } else {
                                //prepend new comment
                                self.ui.commentBox.find('ul').first().prepend(data);
                                self.ui.commentBox.find('.gocha-video-placeholder').hide();
                            }

                            self.clearcommentFormBox();
                            self.closeForm();
                            self.openComments();
                            self.regenerateCommentBars(commentinput[1], commentinput[2]);
                        } else {
                            self.ui.formStatus.fadeIn().html('<p class="ajax-error">' + self.options.txt_ajax_delay + '</p>');
                        }
                    }
                });
            } else {
                self.ui.formStatus.html('<p class="ajax-error">' + self.options.txt_ajax_fillform + '</p>');

                setTimeout(function(){
                    self.ui.formStatus.fadeOut();
                }, 8000);
            }
        });
    };

    /*
     * Update status bar position
     */
    GochaVideo.prototype.updatebar = function(positionX) {
       var position = positionX - this.ui.seeker.offset().left;
       var percentage = 100 * position / this.ui.seeker.width();

       if(percentage > 100) {
          percentage = 100;
       }

       if(percentage < 0) {
          percentage = 0;
       }

       this.ui.bar.css('width', percentage + '%');
       this.playerMethods.comment(this.totalTime * percentage / 100);
    };

    /*
     * Show dynamic comments based on player time
     */
    GochaVideo.prototype.dynamicComments = function(flowid,time){
        var self = this;
        var bar = ((this.currentTime * 100) / this.totalTime) + '%';
        this.ui.bar.css('width', bar);

        if(this.ui.controls.attr('data-mode') === 'point') {
            return;
        }

        if(0 === this.start){
            this.ui.startBtn.find('.gtimenumber').text(this.toHHMMSS(this.currentTime));
        }

        if(0 === this.stop){
            this.ui.stopBtn.find('.gtimenumber').text(this.toHHMMSS(this.currentTime));
        }

        // check if dynamic comment displaying is activated in options
        if(this.ui.commentBox.hasClass('gocha-dynamic')){
            this.wrapper.find('.gocha-video-comment').each(function(i, comment) {
                comment = $(comment);
                //get comment video start and stop time
                var videoStart = comment.attr('data-gocha-start');
                var videoStop = comment.attr('data-gocha-stop');
                //hide or show comment
                if(time >= videoStart && time < videoStop){
                    comment.addClass('gvc-show');
                } else {
                    comment.removeClass('gvc-show');
                }
            });

            if(this.wrapper.find('.gvc-show').length){
                this.wrapper.find('.gocha-video-placeholder').hide();
            } else {
                this.wrapper.find('.gocha-video-placeholder').show();
            }
        }

        //count for current active comment timelines
        var activecount = 1;

        //loop thru markers
        this.ui.seeker.find( '.gocha-marker' ).each(function(i, marker) {
            marker = $(marker);
            var videoStart = marker.attr('data-gvc-start');
            var videoStop = marker.attr('data-gvc-stop');
            var width = marker.attr('data-gvc-width');

            if(time >= videoStart && time < videoStop){
                marker.addClass('g-active').css({
                    'width': width,
                    "bottom": activecount * ((activecount === 1) ? 11 : 12)
                });

                self.ui.seeker.css('margin-top', activecount * 15);
                activecount++;
            } else {
                $(this).removeClass('g-active').css({
                    'width': 24,
                    'bottom': -24
                });
            }
        });
    };

     /*
      * Show all comments on player pause
      */
     GochaVideo.prototype.showAllComments = function(){
         clearInterval(this.timeout);

         this.wrapper.find('.gocha-video-comment').each(function( index ) {
             $(this).addClass('gvc-show');
         });

         this.wrapper.find('.gocha-video-placeholder').hide();
     };

     /*
      * Generate timeline bars
      */
     GochaVideo.prototype.generateCommentBars = function(){
        var self = this;

        // Make sure that even after regenerating comment bars we will start
        // counting comments from 0
        this.ui.seeker.find('.gocha-marker').each(function(i, marker) {
            marker = $(marker);
            marker.text(0);
        });

        this.wrapper.find('.gocha-video-comment').each(function( index ) {
            var videoStart = $(this).attr('data-gocha-start');
            var videoStop = $(this).attr('data-gocha-stop');
            var position = ((videoStart * 100) / self.totalTime) + '%';
            var width = (((videoStop - videoStart) * 100) / self.totalTime) + '%';
            var marker = self.ui.seeker.find( '.gocha-marker[data-gvc-start="'+videoStart+'"]' );

            if(marker.length){
                marker.find('span').text(parseInt(marker.text()) + 1);
            } else {
                self.ui.seeker.append('<div class="gocha-marker" data-gcv-marker="'+self.currentVideo+'" data-gvc-start="'+ (Math.round(videoStart * 100) / 100).toFixed(2) +'" data-gvc-stop="'+(Math.round(videoStop * 100) / 100).toFixed(2) +'" data-gvc-width="'+width+'" style="left:'+position+';"><span>1</span></div>');
            }
        });
    };

    /*
     * Add comment timeline on ajax comment
     */
    GochaVideo.prototype.regenerateCommentBars = function(videoStart, videoStop){
        var position = ((videoStart * 100) / this.totalTime) + '%';
        var width = (((videoStop - videoStart) * 100) / this.totalTime) + '%';
        var marker = this.ui.seeker.find('.gocha-marker[data-gvc-start="' + videoStart + '"]');

        if(marker.length){
            marker.find('span').text(parseInt(marker.text()) + 1);
        } else {
            this.ui.seeker.append('<div class="gocha-marker" data-gcv-marker="'+this.currentVideo+'" data-gvc-start="'+ (Math.round(videoStart * 100) / 100).toFixed(2) +'" data-gvc-stop="'+ (Math.round(videoStop * 100) / 100).toFixed(2) +'" data-gvc-width="'+width+'" style="left:'+position+';"><span>1</span></div>');
        }
    };

    /*
     * Resize window with delay
     */
    GochaVideo.prototype.resizeWindow = function(){
        var self = this;
        clearInterval(this.resizeTimeout);

        this.resizeTimeout = setInterval(function () {
            clearInterval(self.resizeTimeout);
            $(window).resize();
        }, 500);
    };

     /*
      * Smooth scroll to video
      */
     GochaVideo.prototype.scrollToComment = function(target , speed) {
         speed = speed || 1000;

         if(!$(target).length) {
             return false;
         }

         $('html:not(:animated),body:not(:animated)').animate({
             scrollTop: $(target).offset().top - 50 // Apply 50px offset
         }, speed);

         return false;
     };

     /*
      * Close comments
      */
     GochaVideo.prototype.closeComments = function(){
         this.ui.commentBox.removeClass('gocha-open');
         this.ui.showBtn.text(this.options.txt_bb_comments_show);
         this.resizeWindow();
     };

     /*
      * Open comments
      */
     GochaVideo.prototype.openComments = function(){
         if(this.ui.commentBox.find('ul').children().length){
             this.ui.commentBox.addClass('gocha-open');
             this.ui.showBtn.text(this.options.txt_bb_comments_hide);
             this.resizeWindow();
         }
     };

     /*
      * Toggle comments
      */
     GochaVideo.prototype.toggleComments = function(){
         if(this.ui.commentBox.hasClass('gocha-open')){
             this.closeComments();
         } else {
             this.openComments();
         }
     };

     /*
      * Opens comment form
      */
     GochaVideo.prototype.openForm = function(){
         if(this.mode !== 'point') {
             this.ui.addBtn.prop( "disabled", false );
         }

         this.ui.commentFormBox.addClass('gocha-open');
         this.ui.addBtn.text(this.options.txt_bb_form_hide);
         this.resizeWindow();
     };

     /*
      * Closes comment form
      */
     GochaVideo.prototype.closeForm = function(){
         if(this.mode !== 'point') {
             this.ui.addBtn.prop( "disabled", true );
         }

         this.ui.commentFormBox.removeClass('gocha-open');

         if(this.mode === 'range') {
             this.ui.addBtn.text(this.options.txt_bb_form_add).addClass('gocha-start-button').removeClass('gocha-stop-button');
         } else {
             this.ui.addBtn.text(this.options.txt_bb_form_add_point);
         }

         this.ui.commentFormBox.find('input#gocha_video_input').val('');
         this.resizeWindow();
     };

     /*
      * Toggle comments form
      */
     GochaVideo.prototype.toggleForm = function(){
         if(this.ui.commentFormBox.hasClass('gocha-open')){
             this.closeForm();
         } else {
             this.openForm();
         }
     };

     /*
      * Clear comment fields
      */
     GochaVideo.prototype.clearcommentFormBox = function(){
         this.ui.commentFormBox.find('input:not([type="submit"],[name="comment_post_ID"]), textarea').val('');
         this.start = 0;
         this.stop = 0;
     };

    /*
     * Cancel comment reply
     */
    GochaVideo.prototype.clearCommentReplay = function(){
        this.ui.formCommentParent.val('');
        this.ui.formVideoInput.val('');
        this.ui.formCancelReply.css('display', 'none');
        this.ui.commentFormBox.find( "#gocha-div-comment-" + this.commentReplyId + " .gocha-replay-link" ).text(this.options.txt_reply);
        this.ui.formH2.text(this.options.txt_h2_default);
        this.ui.commentBox.find('.gocha-video-replay-link').text(this.options.txt_reply);
        this.start = 0;
        this.stop = 0;
        this.closeForm();
    };

    /*
     * Replay to comment custom method
     */
    GochaVideo.prototype.commentReplay = function(commentID){
        //get all variables
        var comment = this.ui.commentBox.find('#gocha-div-comment-'+commentID);
        var commentbutton = comment.find('.gocha-video-replay-link').first();
        var videoStart = comment.attr('data-gocha-start');
        var videoStop = comment.attr('data-gocha-stop');
        var inputArray = JSON.stringify([this.currentVideo, videoStart, videoStop]);

        //check if reply is already activated
        if(commentbutton.text() === this.options.txt_reply) {
            this.commentReplyId = this.ui.formCommentParent.val();

            if(this.commentReplyId !== ''){
                this.clearCommentReplay();
            }

            commentbutton.text(this.options.txt_cancel_reply);
            this.ui.formH2.text(this.options.txt_h2_reply);
            this.ui.formCommentParent.val(commentID);
            this.ui.formVideoInput.val(inputArray);
            this.ui.formCancelReply.css({'display':'block'});
            this.openForm();
            this.closeComments();
        } else {
            commentbutton.text(this.options.txt_reply);
            this.clearCommentReplay();
            this.openComments();
        }
    };

    /*
     * Added logic for finding comented range. Check add new comment or reply to existing
     */
    GochaVideo.prototype.timeDifference = function(videoStart, videoStop){
         var self = this;

         this.ui.commentBox.find('.gocha-video-comment').each(function(i, comment) {
             comment = $(comment);

             if(!comment.parent().hasClass('children')){
                 var commentID = comment.attr('data-comment-id');
                 var newVideoStart = comment.attr('data-gocha-start');
                 var newVideoStop = comment.attr('data-gocha-stop');
                 var absStart = Math.abs(videoStart - newVideoStart);
                 var absStop = Math.abs(videoStop - newVideoStop);
                 // time difference is smaller than mintimediff,
                 // replay to comment instead of adding new
                 if(absStart <= self.options.mintimediff && absStop <= self.options.mintimediff){
                     self.commentReplay(commentID);
                 }
             }
         });
    };

    /*
     * Converts seconds to time in the HH:MM:SS format
     */
    GochaVideo.prototype.toHHMMSS = function (sec) {
        sec = parseInt(sec, 10);
        var hours = Math.floor(sec / 3600);
        var minutes = Math.floor((sec - (hours * 3600)) / 60);
        var seconds = sec - (hours * 3600) - (minutes * 60);

        hours = (hours < 10 ? '0' : '') + hours;
        minutes = (minutes < 10 ? '0' : '') + minutes;
        seconds = (seconds < 10 ? '0' : '') + seconds;

        var time = minutes + ':' + seconds;

        if(parseInt(hours) > 0){
            time = hours + ':' + minutes + ':' + seconds;
        }

        return time;
    };

    /*
     * Initialized Youtube player
     */
    GochaVideo.prototype.initYoutube = function(){
        var self = this;
        this.type = 'yt';
        this.playerID = $('#YT-'+this.currentVideo+'-player');
        this.player = new YT.Player('YT-'+this.currentVideo+'-player', {
            origin: window.location.href,
            videoId: this.currentVideo,
            events: {
                onStateChange: function(e) {
                    if(e.data === YT.PlayerState.PLAYING) {
                        self.totalTime = self.player.getDuration();
                        self.ui.seeker.attr('data-video-duration', self.totalTime);
                        clearInterval(self.timeout);

                        self.timeout = setInterval(function () {
                            self.currentTime = self.player.getCurrentTime();
                            self.dynamicComments(self.currentVideo, self.currentTime);
                            var bar = ((self.currentTime * 100) / self.totalTime) + '%';
                            self.ui.bar.css('width', bar);

                            if(self.stopPlay !== undefined && self.stopPlay <= self.currentTime){
                                if(self.currentTime > 0.33) {
                                    self.player.pauseVideo();
                                }

                                self.stopPlay = undefined;
                            }
                        }, 100);
                    }

                    if(e.data === YT.PlayerState.PAUSED) {
                        self.showAllComments();
                    }

                    if(e.data === YT.PlayerState.ENDED) {
                        self.showAllComments();
                    }
                },
                onReady: function() {
                    // Calculate ratio
                    var videoWrapper = self.wrapper.find('.gocha-video-player-wrapper');
                    var videoIframe = videoWrapper.find('iframe');
                    var ratio = parseInt(videoIframe.attr('height')) / parseInt(videoIframe.attr('width'));

                    videoWrapper.css('padding-top', (ratio * 100) + "%");
                    // Init the UI
                    self.totalTime = self.player.getDuration();
                    self.generateCommentBars(self.currentVideo);
                }
            }
        });

        this.playerMethods = {
            play: function(){
                self.player.playVideo();
            },
            pause: function(){
                self.player.pauseVideo();
            },
            comment: function(startVideo) {
                self.player.seekTo(Math.round(startVideo * 100) / 100);
                self.player.playVideo();
            },
            current: function(){
                return self.player.getCurrentTime();
            }
        };
    };

    /*
     * Initialized Vimeo player
     */
    GochaVideo.prototype.initVimeo = function(){
        var self = this;
        this.type = 'vm';

        this.player = new Vimeo.Player('VM-' + this.currentVideo + '-player', {
            id: this.currentVideo
        });

        this.player.on('loaded', function() {
            self.player.getDuration().then(function(duration) {
                // Calculate ratio
                var videoWrapper = self.wrapper.find('.gocha-video-player-wrapper');
                var videoIframe = videoWrapper.find('iframe');
                var ratio = parseInt(videoIframe.attr('height')) / parseInt(videoIframe.attr('width'));
                videoWrapper.css('padding-top', (ratio * 100) + "%");
                // Init the UI
                self.totalTime = duration;
                self.ui.seeker.attr('data-video-duration', self.totalTime);
                self.generateCommentBars(self.currentVideo);
            });
        });

        this.player.on('pause', function() {
            self.showAllComments();
        });

        this.player.on('ended', function() {
            self.showAllComments();
        });

        this.player.on('timeupdate', function(data) {
            self.currentTime = data.seconds;

            if(self.stopPlay !== undefined && self.stopPlay <= self.currentTime){
                if(self.currentTime > 0.33) {
                    self.player.pause();
                }

                self.stopPlay = undefined;
            }

            self.dynamicComments(self.currentVideo, self.currentTime);
        });

        this.playerMethods = {
            play: function() {
                self.player.play();
            },
            pause: function() {
                self.player.pause();
            },
            comment: function(startVideo) {
                self.player.setCurrentTime(startVideo).then(function(seconds) {
                    self.player.play();
                });
            },
            current: function(){
                return self.player.getCurrentTime().then(function(seconds) {
                    return seconds;
                });
            }
        };
    };

    /*
     * Initialized dailymotion player
     */
    GochaVideo.prototype.initDailyMotion = function(){
        var self = this;
        this.type = 'dm';
        // Storage for the initialized DM videos in order to avoid issues
        // with the ad playback and wrong time configuration
        this.dmInitialized = {}
        this.dmInitialized[this.currentVideo] = false;

        // Calculate ratio
        var videoWrapper = self.wrapper.find('.gocha-video-player-wrapper');
        var videoIframe = videoWrapper.find('iframe');
        var ratio = parseInt(videoIframe.attr('height')) / parseInt(videoIframe.attr('width'));
        videoWrapper.css('padding-top', (ratio * 100) + "%");

        this.player = new DM.player(document.getElementById('DM-' + this.currentVideo + '-player'), {
            video: this.currentVideo
        });

        this.player.addEventListener('video_start', function() {
            if(self.dmInitialized[self.currentVideo]) {
                return;
            }
            // Calculate ratio
            var videoWrapper = self.wrapper.find('.gocha-video-player-wrapper');
            var videoIframe = videoWrapper.find('iframe');
            var ratio = parseInt(videoIframe.attr('height')) / parseInt(videoIframe.attr('width'));
            videoWrapper.css('padding-top', (ratio * 100) + "%");

            self.player.addEventListener('durationchange', function() {
                if(self.dmInitialized[self.currentVideo]) {
                    return;
                }

                // Calculate ratio
                var videoWrapper = self.wrapper.find('.gocha-video-player-wrapper');
                var videoIframe = videoWrapper.find('iframe');
                var ratio = parseInt(videoIframe.attr('height')) / parseInt(videoIframe.attr('width'));
                videoWrapper.css('padding-top', (ratio * 100) + "%");
                // Init the UI
                self.totalTime = self.player.duration;
                self.ui.seeker.attr('data-video-duration', self.totalTime);
                self.generateCommentBars(self.currentVideo);
                self.dmInitialized[self.currentVideo] = true;
            });
        });

        this.player.addEventListener('ad_end', function() {
            if(self.dmInitialized[self.currentVideo]) {
                return;
            }

            self.player.addEventListener('durationchange', function() {
                if(self.dmInitialized[self.currentVideo]) {
                    return;
                }

                self.totalTime = self.player.duration;
                self.ui.seeker.attr('data-video-duration', self.totalTime);
                self.generateCommentBars(self.currentVideo);
                self.dmInitialized[self.currentVideo] = true;
            });
        });

        this.player.addEventListener('pause', function() {
            self.showAllComments();
        });

        this.player.addEventListener('video_end', function() {
            self.showAllComments();
        });

        this.player.addEventListener('timeupdate', function() {
            self.currentTime = self.player.currentTime;

            if(self.stopPlay !== undefined && self.stopPlay <= self.currentTime){
                if(self.currentTime > 0.33) {
                    self.player.pause();
                }

                self.stopPlay = undefined;
            }

            self.dynamicComments(self.currentVideo, self.currentTime);
        });

        this.playerMethods = {
            play: function() {
                self.player.play();
            },
            pause: function() {
                self.player.pause();
            },
            comment: function(startVideo) {
                self.player.seek(Math.round(startVideo * 100) / 100);
                self.player.play();
            },
            current: function(){
                return self.player.currentTime;
            }
        };
    };

    /*
     * Initialized FB player
     */
    GochaVideo.prototype.initFB = function(){
        var self = this;
        this.type = 'fb';

        FB.Event.subscribe('xfbml.ready', function(msg) {
            if (msg.type === 'video' && msg.id === self.wrapper.attr('id') + '-player') {
                self.player = msg.instance;

                setTimeout(function() {
                    self.totalTime = self.player.getDuration();
                    self.ui.seeker.attr('data-video-duration', self.totalTime);
                    self.generateCommentBars(self.currentVideo);
                }, 1000);

                self.player.subscribe('startedPlaying', function() {
                    self.fbTimer = setInterval(function() {
                        self.fbTimeUpdate();
                    }, 333);
                });

                self.player.subscribe('paused', function() {
                    self.showAllComments();
                    //clearInterval(self.fbTimer);
                });

                self.player.subscribe('finishedPlaying', function() {
                    self.showAllComments();
                    clearInterval(self.fbTimer);
                });
            }
        });

        this.playerMethods = {
            play: function() {
                self.player.play();
            },
            pause: function() {
                self.player.pause();
            },
            comment: function(startVideo) {
                self.player.seek(Math.round(startVideo * 100) / 100);

                setTimeout(function() {
                    self.player.play();
                }, 500);
            },
            current: function(){
                return self.player.getCurrentPosition();
            }
        };
    };

    GochaVideo.prototype.fbTimeUpdate = function() {
        this.currentTime = this.player.getCurrentPosition();

        if(this.stopPlay !== undefined && this.stopPlay <= this.currentTime){
            if(this.currentTime > 0.33) {
                this.player.pause();
            }

            this.stopPlay = undefined;
        }

        this.dynamicComments(this.currentVideo, this.currentTime);
    }

    /*
     * Initialized Media Element player
     */
    GochaVideo.prototype.initMediaElement = function(){
        var self = this;
        this.type = 'media';
        this.playerID = $('#'+this.wrapper.find( "video.wp-video-shortcode" ).attr('id'));
        this.player = this.playerID[0];

        if(this.player.duration) {
            self.totalTime = self.player.duration;
            self.ui.seeker.attr('data-video-duration', self.totalTime);
            self.generateCommentBars();
        } else {
            var loadedDataListener = function(){
                self.player.removeEventListener('canplay', loadedDataListener, false);
                self.totalTime = self.player.duration;
                self.ui.seeker.attr('data-video-duration', self.totalTime);
                self.generateCommentBars();
            }

            this.player.addEventListener('canplay', loadedDataListener, false);
        }

        this.player.addEventListener('timeupdate', function(e) {
            self.currentTime = self.player.currentTime;

            if(self.stopPlay !== undefined && self.stopPlay <= self.currentTime){
                if(self.currentTime > 0.33) {
                    self.player.pause();
                }

                self.stopPlay = undefined;
            }

            self.dynamicComments(self.currentVideo, self.currentTime);
        }, false);

        this.player.addEventListener('play', function(e) {
            self.playerState = 'play';
        }, false);

        this.player.addEventListener('pause', function(e) {
            self.playerState = 'stop';
            self.showAllComments();
        }, false);

        this.player.addEventListener('ended', function(e) {
            self.playerState = 'stop';
            self.showAllComments();
        }, false);

        this.playerMethods = {
            play: function() {
                if(self.playerState !== 'play'){
                    self.player.play();
                }
            },
            pause: function() {
                self.player.pause();
            },
            comment: function(startVideo) {
                self.player.currentTime = startVideo;
                self.player.play();
            },
            current: function() {
                return self.player.currentTime;
            }
        };
    };
})(jQuery);
