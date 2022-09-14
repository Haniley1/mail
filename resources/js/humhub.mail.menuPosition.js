humhub.module('mail.menuPosition', function(module, require, $) {
    const selector = {
        entriesList: '.conversation-entry-list',
        entryClassName: 'mail-conversation-entry',
        entry: '.mail-conversation-entry',
        menuContainer: '.dropdown',
        menu: '.conversation-menu',
        messageContent: '.content .message-frame',
        messageContainer: '.conversation-entry-content',
        conversationSettingsButton: '#conversationSettingsButton'
    };

    const view = require('ui.view');

    let init = function() {
        const $messageContainer = $(selector.entriesList);
        if (!$messageContainer.length) {
            module.log.debug("Message container not found");
            return;
        }

        const initMenuPosition = function (messageNode) {
            const containerWidth = $(selector.entriesList).width();
            const messageWidth = $(messageNode).find(selector.messageContent).width();
            const isOwn = $(messageNode).hasClass('own');

            let addClass;
            let removeClass;
            const isMobile = view.isSmall();
            if (isMobile && isOwn && containerWidth - messageWidth > 120) {
                addClass = 'dropdown-left';
                removeClass = 'dropdown-right';
            } else if (isMobile && !isOwn && containerWidth - messageWidth > 120) {
                addClass = 'dropdown-right';
                removeClass = 'dropdown-left';
            } else if (!isMobile && isOwn && containerWidth - 100 - messageWidth < 120) {
                addClass = 'dropdown-left';
                removeClass = 'dropdown-right';
            } else if (!isMobile && !isOwn && containerWidth - 100 - messageWidth < 120) {
                addClass = 'dropdown-right';
                removeClass = 'dropdown-left';
            }

            if (addClass && removeClass) {
                $(messageNode).find(selector.menu).removeClass(removeClass).addClass(addClass);
            }
        }

        const handleMutations = function (mutations) {
            for (const mutation of mutations) {
                for (const addedNode of mutation.addedNodes) {
                    if (!addedNode.classList || !addedNode.classList.contains(selector.entryClassName)) {
                        continue;
                    }

                    initMenuPosition(addedNode)
                }
            }
        }

        const initAllMenu = function () {
            for (const $entry of $(selector.entry)) {
                initMenuPosition($entry);
            }
        }

        const addMobileMenuHandler = function () {
            if (view.isSmall()) {
                const $messageContainer = $(selector.entry).find(selector.messageContainer)
                $messageContainer.off('click', handleMessageClick).on('click', handleMessageClick)
            }
        }

        const handleMessageClick = function ($evt) {
            $evt.stopPropagation()
            const $messageGenericContainer = $($evt.currentTarget.parentElement.parentElement)
            let $dropdown = $messageGenericContainer.siblings().find(selector.menuContainer)

            if (!$dropdown.length) {
                $dropdown = $messageGenericContainer.find(selector.menuContainer)
            }

            $dropdown.children(selector.conversationSettingsButton).dropdown('toggle')
        }

        const observer = new MutationObserver(handleMutations);
        observer.observe($messageContainer[0], {childList: true});

        window.addEventListener('resize', initAllMenu);
        initAllMenu();
        addMobileMenuHandler()
    };

    module.export({
        initialize: init,
    });
});
