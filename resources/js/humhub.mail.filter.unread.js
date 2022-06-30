humhub.module('mail.filter.unread', function(module, require, $) {

    var Widget = module.require('ui.widget').Widget;
    var Filter = require('ui.filter').Filter;

    var MyFilter = Filter.extend();

    MyFilter.prototype.init = function() {
        this.cosmeticCheckbox = this.$.find('#rocketmailfilter-unread-toggle');
        this.hiddenInput = this.$.find('[name=unread]');
        this.inputContainer = this.cosmeticCheckbox.closest('#rocketmailfilter-root');
        this.mailFilterForm = Widget.instance('#mail-filter-root').$.find('form');
        this.placeUnreadCheckbox();
        this.attachListeners();
    };

    MyFilter.prototype.placeUnreadCheckbox = function() {
        this.mailFilterForm.prepend(this.$.detach());
        this.$.removeClass('hidden');
    }

    MyFilter.prototype.attachListeners = function() {
        var self = this;
        this.cosmeticCheckbox.on('change', function() {
            self.toggleInputValue();
            if (self.isChecked()) {
                self.activateHiddenInput();
            } else {
                self.deactivateHiddenInput();
            }
            Widget.instance('#mail-filter-root').triggerChange();
            Widget.instance('#mail-conversation-root').close();
        });
    };

    MyFilter.prototype.toggleInputValue = function() {
        this.hiddenInput.val(this.isChecked() ? '1' : '0');
    }

    MyFilter.prototype.isChecked = function () {
        return this.cosmeticCheckbox.prop('checked');
    }

    MyFilter.prototype.activateHiddenInput = function () {
        this.inputContainer.prepend(this.hiddenInput);
    }

    MyFilter.prototype.deactivateHiddenInput = function () {
        this.hiddenInput.remove();
    }

    module.export = MyFilter;
});
