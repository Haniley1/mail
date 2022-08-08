humhub.module('mail.ConversationEntry.reaction', function(module, require, $) {
    const client = require('client');
    const selectors = {
        reactionsContainer: '.message-reactions',
        conversationContainer: '.mail-conversation-entry'
    };

    const getReactionsContainer = function (conversationEntryId) {
        const $entryContainer = $(`${selectors.conversationContainer}[data-entry-id=${conversationEntryId}]`);
        if (!$entryContainer) {
            throw Error(`Message entry container not found for entry ${conversationEntryId}`);
        }

        return $entryContainer.find(selectors.reactionsContainer);
    }

    const clearReactions = function (conversationEntryId) {
        const $reactionsContainer = getReactionsContainer(conversationEntryId);
        $reactionsContainer.html('');
    };

    const reInitReactions = function (conversationEntryId, reactions) {
        clearReactions(conversationEntryId);

        const $reactionsContainer = getReactionsContainer(conversationEntryId);

        for (const reaction of reactions) {
            if (!module.config['reactions'].hasOwnProperty(reaction)) {
                continue;
            }

            const $block = $('<span/>');
            $block.html(module.config['reactions'][reaction]);
            $reactionsContainer.append($block);
        }
    };

    const react = function ($evt) {
        const messageEntryId = $evt.params['messageEntryId'];
        const type = $evt.params['type'];
        const actionUrl = `${module.config['messageReactionUrl']}?messageEntryId=${messageEntryId}&type=${type}`;
        client.get(actionUrl).then(function (response) {
            if (!response.result) {
                module.log.error(response.error, true);
                return;
            }

            reInitReactions(messageEntryId, response.data.data.MessageEntry.reactions);
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    module.export({
        react: react,
        reInitReactions: reInitReactions
    });
});
