humhub.module('mail.ConversationView', function (module, require, $) {

    var Widget = require('ui.widget').Widget;
    var loader = require('ui.loader');
    var client = require('client');
    var additions = require('ui.additions');
    var object = require('util.object');
    var mail = require('mail.notification');
    var view = require('ui.view');
    var mailMobile = require('mail.mobile');

    var ConversationView = Widget.extend();

    ConversationView.prototype.init = function () {
        additions.observe(this.$);

        var that = this;
        window.onresize = function (evt) {
            that.updateSize(true);
        };

        if (!view.isSmall() && !view.isMedium()) {
            this.reload();
        }

        this.$.on('mouseenter', '.mail-conversation-entry', function () {
            $(this).find('.conversation-menu').show();
        }).on('mouseleave', '.mail-conversation-entry', function () {
            $(this).find('.conversation-menu').hide();
        });

        this.detectOpenedDialog();
    };

    ConversationView.prototype.loader = function (load) {
        if (load !== false) {
            loader.set(this.$);
        } else {
            loader.reset(this.$);
        }
    };

    ConversationView.prototype.markSeen = function (id) {
        client.post(this.options.markSeenUrl, {data: {id: id}}).then(function (response) {
            if (object.isDefined(response.messageCount)) {
                mail.setMailMessageCount(response.messageCount);
            }
        }).catch(function (e) {
            module.log.error(e);
        });
    };

    ConversationView.prototype.loadUpdate = function () {
        var $lastEntry = this.$.find('.mail-conversation-entry:not(.own):last');
        var lastEntryId = $lastEntry.data('entry-id');
        var data = {id: this.getActiveMessageId(), from: lastEntryId};

        var that = this;
        client.get(this.options.loadUpdateUrl, {data: data}).then(function (response) {
            if (response.html) {
                $(response.html).each(function () {
                    that.appendEntry($(this));
                });
            }
        })
    };

    ConversationView.prototype.reply = function (evt) {
        var that = this;
        client.submit(evt).then(function (response) {
            if (response.success) {
                that.appendEntry(response.content).then(function() {
                    that.$.find(".time").timeago(); // somehow this is not triggered after reply
                    var richtext = that.getReplyRichtext();
                    if (richtext) {
                        richtext.$.trigger('clear');
                    }
                    that.scrollToBottom();
                    if(!view.isSmall() && !view.isMedium()) { // prevent autofocus on mobile
                        that.focus();
                    }
                    Widget.instance('#inbox').updateEntries([that.getActiveMessageId()]);
                    Widget.instance('.reply-container').detachReply();
                    that.setLivePollInterval();
                });
            } else {
                module.log.error(response, true);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        }).finally(function (e) {
            loader.reset($('.reply-button'));
            evt.finish();
        });
    };

    ConversationView.prototype.setLivePollInterval = function () {
        require('live').setDelay(5);
    };

    ConversationView.prototype.getReplyRichtext = function () {
        return Widget.instance(this.$.find('.ProsemirrorEditor'));
    };


    ConversationView.prototype.focus = function (evt) {
        if (view.isSmall() || view.isMedium()) {
            return Promise.resolve();
        }
        var replyRichtext = this.getReplyRichtext();
        if (replyRichtext) {
            replyRichtext.focus();
        }
    };

    ConversationView.prototype.canLoadMore = function () {
        return !this.options.isLast;
    };

    ConversationView.prototype.reload = function () {
        if (this.getActiveMessageId()) {
            this.loadMessage(this.getActiveMessageId());
        }
    };

    ConversationView.prototype.addUser = function (evt) {
        var that = this;

        client.submit(evt).then(function (response) {
            if (response.result) {
                that.$.find('#mail-conversation-header').html(response.result);
            } else if (response.error) {
                module.log.error(response, true);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    ConversationView.prototype.appendEntry = function (html) {
        var that = this;
        var $html = $(html);

        if (that.$.find('[data-entry-id="' + $html.data('entryId') + '"]').length) {
            return Promise.resolve();
        }

        // Filter out all script/links and text nodes
        var $elements = $html.not('script, link').filter(function () {
            return this.nodeType === 1; // filter out text nodes
        });

        // We use opacity because some additions require the actual size of the elements.
        $elements.css('opacity', 0);

        // call insert callback
        this.getListNode().append($html);

        return new Promise(function(resolve, reject) {
            $elements.css('opacity', 1).fadeIn('fast', function () {
                that.onUpdate();
                setTimeout(function() {that.scrollToBottom()}, 100);
                resolve();
            });
        })
    };

    ConversationView.prototype.loadMessage = function (evt) {
        (view.isSmall() || view.isMedium()) && $('.messages').addClass('shown');
        var messageId = object.isNumber(evt) ? evt : evt.$trigger.data('message-id');
        var that = this;
        this.loader();
        client.get(this.options.loadMessageUrl, {data: {id: messageId}}).then(function (response) {
            that.setActiveMessageId(messageId);
            that.options.isLast = false;

            var inbox = Widget.instance('#inbox');
            inbox.updateActiveItem();

            // Replace history state only if triggered by message preview item
            if (evt.$trigger && history && history.replaceState) {
                var url = evt.$trigger.data('action-url');
                if (url) {
                    history.replaceState(null, null, url);
                }
            }

            that.$.css('visibility', 'hidden');
            return that.updateContent(response.html);
        }).then(function () {
            return that.initScroll();
        }).catch(function (e) {
            module.log.error(e, true);
        }).finally(function () {
            that.scrollToBottom()
            that.loader(false);
            that.$.css('visibility', 'visible');
            that.initReplyRichText();

            const $chatTitleWrap = $('.chat-title-wrap')
            const $textTitle = $chatTitleWrap.children('span')
            that.makeScrollable($chatTitleWrap, $textTitle)

            const $occupationWrap = $('.chat-occupation-wrap')
            const $occupationText = $occupationWrap.children('.rocketcore-user-occupation')
            $occupationWrap.on('mouseenter', function() {
                that.makeScrollable($occupationWrap, $occupationText, false)
            })
            $occupationWrap.on('mouseleave').on('mouseleave', function() {
                $occupationWrap.animate({scrollLeft: 0}, 3500)
            })
        });
    };

    ConversationView.prototype.makeScrollable = function ($wrap, $textNode, looped = true, scrollDelay = 1500, scrollDuration = 3500) {
        if ($wrap.innerWidth() < $textNode.innerWidth()) {
            const offsetLeft = $wrap.offset().left

            const scrollLoopTitle = () => {
                setTimeout(() => {
                    $wrap.animate({scrollLeft: offsetLeft}, scrollDuration, () => {
                        setTimeout(() => $wrap.animate({scrollLeft: 0}, scrollDuration, function() {
                            if (looped) {
                                scrollLoopTitle()
                            }
                        }), scrollDelay)
                    })
                }, scrollDelay)
            }
            scrollLoopTitle()
        }
    }

    ConversationView.prototype.initReplyRichText = function () {
        var that = this;

        if(window.ResizeObserver) {
            var resizeObserver = new ResizeObserver(function(entries) {
                that.updateSize(that.isScrolledToBottom(100));
            });

            var replyRichtext = that.getReplyRichtext();
            if (replyRichtext) {
                resizeObserver.observe(replyRichtext.$[0]);
            }
        }

        that.focus();

    };

    ConversationView.prototype.isScrolledToBottom = function (tolerance) {
        var $list = this.getListNode();

        if(!$list.length) {
            return false;
        }

        tolerance = tolerance || 0;
        var list = this.getListNode()[0];
        return list.scrollHeight - list.offsetHeight - list.scrollTop <= tolerance;
    };

    ConversationView.prototype.initScroll = function () {
        if (window.IntersectionObserver) {
            var $entryList = this.$.find('.conversation-entry-list');
            var $streamEnd = $('<div class="conversation-stream-end"></div>');
            $entryList.prepend($streamEnd);

            var that = this;
            var observer = new IntersectionObserver(function (entries) {
                if (that.preventScrollLoading()) {
                    return;
                }

                if (entries.length && entries[0].isIntersecting) {
                    loader.prepend($entryList);
                    that.loadMore().finally(function () {
                        loader.reset($entryList);
                        that.scrollToBottom()
                    });
                }

            }, {root: $entryList[0], rootMargin: "50px"});

            // Assure the conversation list is scrollable by loading more entries until overflow
            return this.assureScroll().then(function () {
                observer.observe($streamEnd[0]);
                if(view.isLarge()) {
                    that.getListNode().niceScroll({
                        cursorwidth: "7",
                        cursorborder: "",
                        cursorcolor: "#555",
                        cursoropacitymax: "0.2",
                        nativeparentscrolling: false,
                        railpadding: {top: 0, right: 0, left: 0, bottom: 0}
                    });
                }
            });
        }
    };

    ConversationView.prototype.loadMore = function () {
        var that = this;

        var data = {
            id: this.getActiveMessageId(),
            from: this.$.find('.mail-conversation-entry:first').data('entryId')
        };

        return client.get(this.options.loadMoreUrl, {data: data}).then(function (response) {
            if (response.result) {
                var $result = $(response.result).hide();
                that.$.find('.conversation-entry-list').find('.conversation-stream-end').after($result);
                $result.fadeIn();
            }

            that.options.isLast = !response.result || response.isLast;
        }).catch(function (err) {
            module.log.error(err, true);
        });
    };

    ConversationView.prototype.preventScrollLoading = function () {
        return this.scrollLock || !this.canLoadMore();
    };

    ConversationView.prototype.canLoadMore = function () {
        return !this.options.isLast;
    };

    ConversationView.prototype.assureScroll = function () {
        var that = this;
        var $entryList = this.$.find('.conversation-entry-list');
        if ($entryList[0].offsetHeight >= $entryList[0].scrollHeight && this.canLoadMore()) {
            return this.loadMore().then(function () {
                return that.assureScroll();
            }).catch(function () {
                return Promise.resolve();
            })
        }

        return that.scrollToBottom();
    };

    ConversationView.prototype.updateContent = function (html) {
        var that = this;
        return new Promise(function (resolve) {
            that.$.html(html);
            resolve();
        });
    };


    ConversationView.prototype.getActiveMessageId = function () {
        return this.options.messageId;
    };

    ConversationView.prototype.setActiveMessageId = function (id) {
        this.options.messageId = id;
    };

    ConversationView.prototype.scrollToBottom = function () {
        var that = this;

        return new Promise(function (resolve) {
            setTimeout(function() {
                that.$.imagesLoaded(function() {
                    var $list = that.getListNode();
                    if(!$list.length) {
                        return;
                    }

                    that.updateSize(false).then(function () {
                        $list.scrollTop($list[0].scrollHeight)
                        setTimeout(() => {
                            if (!that.isScrolledToBottom(100)) {
                                return that.scrollToBottom()
                            }
                        }, 100)
                        resolve()
                    });
                })
            });
        });
    };

    ConversationView.prototype.updateSize = function (scrollToButtom) {
        var that = this;
        return new Promise(function (resolve) {
            setTimeout(function () {
                var $entryContainer = that.$.find('.conversation-entry-list');

                if (!$entryContainer.length) {
                    return;
                }

                var replyRichtext = that.getReplyRichtext();
                var formHeight = replyRichtext ? replyRichtext.$.innerHeight() : 0;
                $entryContainer.css('margin-bottom' , formHeight + 5 + 'px');

                var offsetTop = that.$.find('.conversation-entry-list').offset().top;
                var max_height = (window.innerHeight - offsetTop - formHeight - ((view.isSmall() || view.isMedium()) ? 20 : 30)) + 'px';
                $entryContainer.css('height', max_height);
                $entryContainer.css('max-height', max_height);

                if(scrollToButtom !== false) {
                    that.scrollToBottom();
                }
                resolve();
            }, 100);
        })

    };

    ConversationView.prototype.getListNode = function () {
        return this.$.find('.conversation-entry-list');
    };

    ConversationView.prototype.onUpdate = function () {
        if(view.isLarge()) {
            this.getListNode().getNiceScroll().resize();
        }
    };

    ConversationView.prototype.isLastMessageMine = function () {
        return this.$.find('.mail-conversation-entry').last().hasClass('own');
    }

    const removeIdFromUrl = function () {
        const url = new URL(window.location);
        url.searchParams.delete('id');
        window.history.pushState({}, '', url);
    }

    ConversationView.prototype.close = function () {
        this.setActiveMessageId(null);
        Widget.instance('#inbox').updateActiveItem();
        this.$.html('');
        removeIdFromUrl();

        if (view.isSmall() || view.isMedium()) {  // is mobile
            mailMobile.closeConversation();
        }
    }

    ConversationView.prototype.detectOpenedDialog = function() {
        if (view.isSmall() || view.isMedium()) {
            const queryParams = new URLSearchParams(window.location.search)
            if (queryParams.has('id')) {
                const dialogId = queryParams.get('id')
                $('.messages').addClass('shown');
                this.loadMessage(parseInt(dialogId))
            }
        }
    }

    module.export = ConversationView;
});
