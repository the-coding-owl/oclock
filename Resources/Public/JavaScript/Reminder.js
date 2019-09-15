define(['jquery', 'TYPO3/CMS/Backend/Modal', 'TYPO3/CMS/Backend/Notification', 'TYPO3/CMS/Backend/Severity'],
    function ($, Modal, Notification, Severity) {
        let Reminder = {
            containerSelector: '.tx_oclock',
            selectorAdd: '.reminder-add',
            selectorEdit: '.reminder-edit',
            init: function() {
                let reminder = this;
                $(this.containerSelector).each(function() {
                    let container = $(this);
                    container.find(reminder.selectorAdd).each(function () {
                        let addButton = $(this);
                        addButton.on('click', function(event) {
                            event.preventDefault();
                            Modal.show(
                                TYPO3.lang['reminder.add.title'],
                                '<form method="POST" action="' + TYPO3.settings.ajaxUrls['oclock/reminder_add'] + '">'
                                    + '<p class="form-group">'
                                        + '<label for="tx-oclock-message">'
                                            + TYPO3.lang['reminder.message']
                                        + '</label>'
                                        + '<br />'
                                        + '<textarea name="message" class="form-control" id="tx-oclock-message" cols="40" rows="12" />'
                                    + '</p>'
                                    + '<p>'
                                        + '<label for="tx-oclock-datetime">'
                                            + TYPO3.lang['reminder.datetime']
                                        + '</label>'
                                        + '<br />'
                                        + '<input type="text" name="datetime" class="form-control" id="tx-oclock-datetime" />'
                                    + '</p>'
                                + '</form>',
                                Severity.info,
                                [
                                    {
                                        text: TYPO3.lang['button.cancel'],
                                        active: true,
                                        btnClass: 'btn-default',
                                        trigger: function () {
                                            Modal.dismiss();
                                        }
                                    }, {
                                        text: TYPO3.lang['button.save'],
                                        btnClass: 'btn-warning',
                                        name: 'save',
                                        trigger: function () {
                                            $.post({
                                                data: Modal.currentModal.find('form').serializeArray(),
                                                url: TYPO3.settings.ajaxUrls['oclock/reminder_add']
                                            }).done(function(response) {
                                                if (response.success) {
                                                    Notification.success(
                                                        TYPO3.lang['reminder.add.successfull'],
                                                        response.message
                                                    );
                                                } else {
                                                    Notification.error(
                                                        TYPO3.lang['reminder.add.error'],
                                                        response.message
                                                    );
                                                }
                                            }).fail(function( jqXHR, textStatus) {
                                                Notification.error(
                                                    TYPO3.lang['reminder.add.error'],
                                                    textStatus
                                                );
                                            });
                                            Modal.dismiss();
                                        }
                                    }
                                ]
                            );
                        });
                    });
                });
            }
        };
        Reminder.init();
        return Reminder;

    }
);
