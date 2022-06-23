humhub.module('mail.UserList', function(module, require, $) {
    const selectors = {
        form: '#chat-user-list-form',
        filter: 'input[name="UserFilter[filter]"]'
    };
    const modal = require('ui.modal');
    const client = require('client');

    const filter = function () {
        const $form = $(selectors.form);
        let formData = {};
        if ($form) {
            formData = $form.serializeArray();
        }

        modal.global.post(module.config['userListUrl'], { data: formData });
    }

    const clear = function () {
        $(selectors.filter).val('');
        modal.global.post(module.config['userListUrl'], { data: {} });
    }

    const remove = function (evt) {
        client
            .get(module.config['removeParticipantUrl'] + `?id=${evt.params.conversationId}&userId=${evt.params.userId}`)
            .then(() => {filter()});
    }

    module.export({
        filter: filter,
        clear: clear,
        remove: remove
    });
})
