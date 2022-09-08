humhub.module('mail.draft', function(module, require, $) {
    var selector = {
        messagesRoot: '#mail-conversation-root',
        editor: '.ProsemirrorEditor',
        selectedConversation: '.messagePreviewEntry.selected',
        submitButton: '.reply-button'
    };

    var EVENT_DRAFT_CHANGED = 'mail:draft:changed';
    var EVENT_REPLY_CHANGED = 'mail:reply:changed';

    const CONVERSATION_DRAFTS_KEY = 'mail:conversation:drafts';
    const CONVERSATION_REPLY_KEY = 'mail:conversation:reply';

    var Widget = require('ui.widget').Widget;
    var url = require('util').url;
    var RichText = require('ui.richtext');

    function throttle(fn, timeout) {
        var timer = null;
        return function () {
            if (!timer) {
                timer = setTimeout(function() {
                    fn();
                    timer = null;
                }, timeout);
            }
        };
    }

    const Storage = function(key) {
        this.key = key;
        this.internalStorage = window.localStorage || window.sessionStorage;
        if (this.internalStorage.getItem(this.key) === null) {
            this._saveList({});
        }
    };

    Storage.prototype.get = function(convId) {
        var list = this._getList();
        return list[convId] || null;
    };

    Storage.prototype.set = function(convId, draft) {
        var list = this._getList();
        $(document).trigger(EVENT_DRAFT_CHANGED, [convId, draft, list]);
        list[convId] = draft;
        this._saveList(list);
    };

    Storage.prototype.unset = function(convId) {
        var list = this._getList();
        if (typeof list[convId] !== undefined) {
            delete list[convId];
            this._saveList(list);
        }
    };

    Storage.prototype._getList = function() {
        return JSON.parse(this.internalStorage.getItem(this.key));
    };

    Storage.prototype._saveList = function(list) {
        this.internalStorage.setItem(this.key, JSON.stringify(list));
    };

    var getEditorWidget = function() {
        return Widget.instance($(selector.messagesRoot).find(selector.editor));
    };

    var isValidPage = function() {
        var requestParam = url.getUrlParameter('r');
        return (requestParam && decodeURIComponent(requestParam).indexOf('mail/mail') > -1) ||
            location.pathname.indexOf('mail/mail') > -1;
    };

    var getSelectedConversationId = function() {
        return Widget.instance(selector.messagesRoot).getActiveMessageId();
    };

    var shouldLoadDraft = function(mutations) {
        var addedNodes = [];
        var globalReload = false;
        $(mutations).each(function(idx, mutation) {
            if (mutation.addedNodes.length) {
                addedNodes = addedNodes.concat(Array.from(mutation.addedNodes));
            }
        });
        if (addedNodes.length) {
            $(addedNodes).each(function(idx, node) {
                if ($(node).is('.mail-conversation') || $(node).is('.mail-aside')) {
                    return globalReload = true;
                }
            });
        }

        return globalReload && getEditorWidget();
    }

    var loadDraft = function(mutations) {
        if (!shouldLoadDraft(mutations)) return;
        var editor = getEditorWidget().editor;
        var view = editor.view;
        var convId = getSelectedConversationId();
        var draft = getConversationDraft(convId);
        var replyId = getConversationReply(convId);
        if (draft && editor.isEmpty()) {
            var $tr = editor.view.state.tr.insert(
                0,
                editor.parser.parse(draft)
            );
            editor.view.dispatch($tr);
            PMApi.commands.joinBackward(view.state, view.dispatch, view);
        }

        if (replyId) {
            setTimeout(function () {
                Widget.instance('.reply-container').attachReply(replyId);
            }, 500);
        }

        editor.on('keyup mouseup', throttle(function () {
            var draft = editor.serializer.serialize(editor.view.state.doc);
            draftStorage.set(convId, draft);
        }, 1000));

        editor.$.on('clear', function() {
            draftStorage.unset(convId);
        });
    };

    var draftStorage;
    var replyStorage;
    var mutationObserver;
    var draftsObserver;
    var replyObserver;
    var PMApi;
    var initialized = false;
    var init = function() {
        if (!isValidPage()) {
            if (initialized) {
                return cleanUp();
            }
            return false;
        }
        if (initialized) {
            cleanUp();
        }
        var $messagesRoot = $(selector.messagesRoot);
        if (!mutationObserver) {
            mutationObserver = new MutationObserver(loadDraft)
        }

        const convId = getSelectedConversationId();
        PMApi = RichText.prosemirror.api;
        draftStorage = new Storage(CONVERSATION_DRAFTS_KEY);
        replyStorage = new Storage(CONVERSATION_REPLY_KEY);
        mutationObserver.observe($messagesRoot[0], { childList: true, subtree: true });
        draftsObserver = function(ev, convId) {
            module.log.debug("Draft #" + convId + " just changed");
        };
        $(document).on(EVENT_DRAFT_CHANGED, draftsObserver);

        replyObserver = function (_ev, id, author, text) {
            if (author === '' && text === '') {
                replyStorage.unset(convId);
                return;
            }

            replyStorage.set(convId, id);
        };
        $(document).on(EVENT_REPLY_CHANGED, replyObserver);

        initialized = true;
    };

    var cleanUp = function() {
        if (mutationObserver) {
            mutationObserver.disconnect();
        }
        if (draftsObserver) {
            $(document).off(EVENT_DRAFT_CHANGED, draftsObserver);
        }
        if (replyObserver) {
            $(document).off(EVENT_REPLY_CHANGED, replyObserver);
        }
        initialized = false;
    };

    var getConversationDraft = function (convId) {
        return draftStorage.get(convId);
    };

    var getConversationReply = function (convId) {
        return replyStorage.get(convId);
    };

    module.export({
        initOnPjaxLoad: true,
        init: init,
        DraftsStorage: draftStorage,
        ReplyStorage: replyStorage
    });
});
