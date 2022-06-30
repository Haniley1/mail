humhub.module('mail.mobile', function (module, require, $) {
    var ID_MAIL_BREADCRUMBS = 'mail-breadcrumbs';

    var Widget = require('ui.widget').Widget;
    var view = require('ui.view');
    var url = require('util').url;

    var MailBreadcrumbs = Widget.extend();

    var closeConversation = function (evt) {
        if (evt) {
            evt.preventDefault();
        }
        $('.messages').removeClass('shown');
        MailBreadcrumbs.prototype.hideBackButton();
    }

    var closeConversationMobile = function (evt) {
        Widget.instance('#mail-conversation-root').close(evt);
    }

    MailBreadcrumbs.prototype.getButton = function () {
        return $(this.anchor);
    };

    MailBreadcrumbs.prototype.hideBackButton = function () {
        this.getButton().hide();
    };

    var injectMailBreadcrumbs = function () {
        var $parent = $('.mails-header');
        if (!$parent.length || $('#' + ID_MAIL_BREADCRUMBS).length) return;
        var divEl = document.createElement('div');
        divEl.id = ID_MAIL_BREADCRUMBS;
        divEl.dataset.uiWidget = "mail.mobile.MailBreadcrumbs";
        $parent.append(divEl);
        Widget.instance(divEl);
    };

    var fixBodyHeight = function() {
        if (isValidPage()) {
            $(window).on('resize', resizeHandler);
            resizeHandler();
        } else {
            $(window).off('resize', resizeHandler);
        }
    };

    var resizeHandler = function() {
        $(document.body).toggleClass('rocket-mobile-body', isMobileView() && isValidPage());
    };

    var isValidPage = function() {
        var requestParam = url.getUrlParameter('r');
        return (requestParam && decodeURIComponent(requestParam).indexOf('mail/mail') > -1) ||
            location.pathname.indexOf('mail/mail') > -1;
    };

    var isMobileView = function () {
        return view.isSmall();
    };

    var init = function () {
        injectMailBreadcrumbs();
        fixBodyHeight();
    };

    module.export({
        init: init,
        MailBreadcrumbs: MailBreadcrumbs,
        closeConversation: closeConversation,
        closeConversationMobile: closeConversationMobile
    });
});
