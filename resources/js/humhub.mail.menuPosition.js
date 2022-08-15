humhub.module('mail.menuPosition', function(module, require, $) {
    const selector = {
        entriesList: '.conversation-entry-list',
        entryClassName: 'mail-conversation-entry',
        entry: '.mail-conversation-entry',
        menu: '.conversation-menu',
        messageContent: '.content .message-frame',
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

        const observer = new MutationObserver(handleMutations);
        observer.observe($messageContainer[0], {childList: true});

        window.addEventListener('resize', initAllMenu);
        initAllMenu();
    };

    module.export({
        initialize: init,
    });
});
