humhub.module('mail.reply', function(module, require, $) {
    var selector = {
        messagesRoot: '#mail-conversation-root',
        replyButton: '.rocketmailreply-btn',
        convEntry: '.mail-conversation-entry',
        convEntryContent: '.mail-conversation-entry .content',
        convEntriesList: '.conversation-entry-list',
        mailAddonRoot: '.rocketcore-mail-addon-container',
        mailAddonRootEntry: '.rocketcore-mail-addon-entry',
        replyBtn: '.rocketmailreply-btn',
        editor: '.ProsemirrorEditor',
        messageDom: '[data-ui-richtext]',
    };

    var REPLY_MAX_LENGTH = 256;

    var Widget = require('ui.widget').Widget;
    var RichText = require('ui.richtext');
    var url = require('util').url;
    var MailReplyButton = Widget.extend();

    MailReplyButton.prototype.init = function() {
        this.api = PMApi;
        this.editor = this._getEditor();
        this.domParser = this.api.model.DOMParser.fromSchema(this.editor.view.state.schema);
    };

    MailReplyButton.prototype.handle = function() {
        this.clearEditor();
        this.pasteEditor(this.getReplyCut());
        this.fixEmptyBlock();
        this.focusEditor();
    };

    MailReplyButton.prototype.pasteEditor = function(content, pos = 1) {
        var $tr = this.editor.view.state.tr.insert(pos, content);
        this.editor.view.dispatch($tr);
    }

    MailReplyButton.prototype.fixEmptyBlock = function() {
        var view = this.editor.view;
        var doc = view.state.doc;
        view.dispatch(
            view.state.tr.setSelection(
                this.api.state.TextSelection.near(doc.resolve(doc.content.size - 4))
            )
        );
        this.api.commands.joinBackward(view.state, view.dispatch, view);
        // this.api.commands.liftEmptyBlock(view.state, view.dispatch);
    };

    MailReplyButton.prototype.clearEditor = function() {
        this.editor.clear();
        var $tr = this.editor.view.state.tr.insert(
            0,
            this.editor.parser.parse('>')
        );
        this.editor.view.dispatch($tr);
    };

    MailReplyButton.prototype.focusEditor = function() {
        var selection = this.api.state.Selection.atEnd(this.editor.view.state.doc);
        var $tr = this.editor.view.state.tr.setSelection(selection);
        this.editor.view.focus();
        this.editor.view.dispatch($tr.scrollIntoView());
    };

    MailReplyButton.prototype.getNodesContent = function() {
        return this.domParser.parse(this._getDomNode());
    };

    MailReplyButton.prototype.getReplyCut = function() {
        var node = this.stripBlockquoteFromBeginning(this.getNodesContent());
        if (node.nodeSize < REPLY_MAX_LENGTH) {
            return node;
        }
        var nodeCut = node.cut(0, REPLY_MAX_LENGTH);
        var schema = this.editor.view.state.schema;
        var ellipsisNode = schema.node('paragraph', null, [schema.text('...')]);
        return nodeCut.content.addToEnd(ellipsisNode);
    };

    MailReplyButton.prototype.stripBlockquoteFromBeginning = function(node) {
        if (node.content.size && node.content.content[0].type.name === 'blockquote') {
            return node.cut(node.content.content[0].nodeSize);
        }
        return node;
    };

    MailReplyButton.prototype._getEditor = function() {
        return Widget.instance($(selector.messagesRoot).find(selector.editor)).editor;
    };

    MailReplyButton.prototype._getDomNode = function() {
        return this.$.closest(selector.convEntryContent).find(selector.messageDom)[0];
    };

    var MailReply = Widget.extend();
    MailReply.prototype.init = function () {
        this.originalMessageId = this._findOriginalMessageId();
    };

    MailReply.prototype.scrollToOriginalMessage = function () {
    };

    MailReply.prototype._findOriginalMessageId = function () {
    };

    var PMApi;
    var mutationObserver;
    var initialized = false;
    var $messagesRoot;
    var init = function() {
        module.log.debug("Trying to initialize");
        if (!isValidPage()) {
            if (initialized) {
                module.log.debug("Module was initialized before, but the current page is not managed");
                return cleanUp();
            }
            module.log.debug("Can't initialize - the current page is not managed");
            return false;
        }
        if (initialized) {
            module.log.debug("Already initialized");
            return true;
        }
        PMApi = RichText.prosemirror.api;
        $messagesRoot = $(selector.messagesRoot);
        if (!mutationObserver) {
            mutationObserver = new MutationObserver(initReplyButton)
        }

        mutationObserver.observe($messagesRoot[0], { childList: true, subtree: true });
        $(document).on('click', selector.replyButton, handleReplyBtnClicks);
        initialized = true;
        module.log.debug("Module initialized");
    };

    var cleanUp = function () {
        mutationObserver.disconnect();
        initialized = false;
        $(document).off('click', selector.replyButton, handleReplyBtnClicks);
        module.log.debug("Module disconnected");
    };

    var isValidPage = function() {
        var requestParam = url.getUrlParameter('r');
        return (requestParam && decodeURIComponent(requestParam).indexOf('mail/mail') > -1) ||
            location.pathname.indexOf('mail/mail') > -1;
    };

    var initReplyButton = function(mutations = []) {
        if (mutations.length <= 2) return false;
        ($messagesRoot || $(selector.messagesRoot)).find(selector.convEntryContent).each(function(idx, el) {
            var $el = $(el);
            const isBlocked = !!$el.closest('.mail-conversation-entry').find('.profile-disable').length;
            if (isBlocked) {
                return false;
            }
            if ($el.find(selector.mailAddonRoot).length) return true;
            var mailAddonRootEl = createMailAddonRoot();
            var replyButtonEl = createReplyBtn();
            mailAddonRootEl.appendChild(replyButtonEl);
            $el.append(mailAddonRootEl);
        });
    };

    var handleReplyBtnClicks = function(ev) {
        ev.preventDefault();
        var widget = Widget.instance(this);
        widget.handle();
    };

    var createReplyBtn = function() {
        var holder = document.createElement('div');
        var button = document.createElement('button');
        var label = document.createElement('span');
        var labelText =  getReplyLabel();
        label.innerText = labelText;
        holder.classList.add(selector.mailAddonRootEntry.replace('.', ''));
        button.classList.add(selector.replyButton.replace('.', ''));
        button.dataset.uiWidget = 'mail.reply.MailReplyButton';
        button.title = labelText;
        button.appendChild(label);
        holder.appendChild(button);
        return holder;
    };

    var createMailAddonRoot = function() {
        var rootEl = document.createElement('div');
        rootEl.classList.add(selector.mailAddonRoot.replace('.', ''));
        return rootEl;
    };

    var getReplyLabel = function() {
        return module.text('reply') || 'Reply';
    };

    module.export({
        initOnPjaxLoad: true,
        init: init,
        initReplyButton: initReplyButton,
        MailReply: MailReply,
        MailReplyButton: MailReplyButton,
    });
});
