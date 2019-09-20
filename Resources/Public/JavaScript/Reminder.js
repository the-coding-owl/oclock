define(['jquery', 'TYPO3/CMS/Backend/Modal', 'TYPO3/CMS/Backend/Notification', 'TYPO3/CMS/Backend/Severity', 'twbs/bootstrap-datetimepicker', 'TYPO3/CMS/Backend/DateTimePicker'],
    function ($, Modal, Notification, Severity, twbsDateTime, DateTimePicker) {
        let Reminder = {
            containerSelector: '.tx_oclock',
            selectorAdd: '.reminder-add',
            selectorList: '.reminder-list',
            selectorDelete: '.reminder-delete',
            selectorEdit: '.reminder-edit',
            init: function() {
                let reminder = this;
                $(this.containerSelector).each(function() {
                    let container = $(this);
                    /**
                     * Register the add button event
                     */
                    container.find(reminder.selectorAdd).each(function () {
                        let addButton = $(this);
                        addButton.on('click', function(event) {
                            event.preventDefault();
                            Modal.advanced({
                                title: TYPO3.lang['oclock/reminder.add.title'],
                                content: $('<form class="tx_oclock_form" method="POST" action="' + TYPO3.settings.ajaxUrls['oclock/reminder_add'] + '">'
                                    + '<p class="form-group">'
                                        + '<label for="tx-oclock-message">'
                                            + TYPO3.lang['oclock/reminder.message']
                                        + '</label>'
                                        + '<br />'
                                        + '<textarea name="message" class="form-control" id="tx-oclock-message" cols="40" rows="12" />'
                                    + '</p>'
                                    + '<p>'
                                        + '<label for="tx-oclock-datetime">'
                                            + TYPO3.lang['oclock/reminder.datetime']
                                        + '</label>'
                                        + '<br />'
                                        + '<input type="datetime-local" name="datetime" class="t3js-datetimepicker form-control" id="tx-oclock-datetime" />'
                                    + '</p>'
                                + '</form>'),
                                severity: Severity.info,
                                buttons: [
                                    {
                                        text: TYPO3.lang['button.cancel'],
                                        active: true,
                                        btnClass: 'btn-default',
                                        trigger: function () {
                                            Modal.dismiss();
                                        }
                                    }, {
                                        text: TYPO3.lang['oclock/reminder.button.save'],
                                        btnClass: 'btn-warning',
                                        name: 'save',
                                        trigger: function () {
                                            $.post({
                                                data: Modal.currentModal.find('form').serializeArray(),
                                                url: TYPO3.settings.ajaxUrls['oclock/reminder_add']
                                            }).done(function(response) {
                                                if (response.success) {
                                                    Notification.success(
                                                        TYPO3.lang['oclock/reminder.add.successfull'],
                                                        response.message
                                                    );
                                                } else {
                                                    Notification.error(
                                                        TYPO3.lang['oclock/reminder.add.error'],
                                                        response.message
                                                    );
                                                }
                                            }).fail(function( jqXHR, textStatus) {
                                                Notification.error(
                                                    TYPO3.lang['oclock/reminder.add.error'],
                                                    textStatus
                                                );
                                            }).always(function() {
                                                Modal.dismiss();
                                            });
                                        }
                                    }
                                ],
                                callback: function(currentModal) {
                                    DateTimePicker.initializeField(currentModal.find('.tx_oclock_form .t3js-datetimepicker'));
                                }
                            });
                        });
                    });
                    /**
                     * Register the list button event
                     */
                    container.find(reminder.selectorList).each(function() {
                        let listButton = $(this);
                        listButton.on('click', function(event) {
                            event.preventDefault();
                            Modal.advanced({
                                title: TYPO3.lang['oclock/reminder.list.title'],
                                type: Modal.types.ajax,
                                severity: Severity.notice,
                                buttons: [
                                    {
                                        text: TYPO3.lang['button.close'],
                                        active: true,
                                        btnClass: 'btn-default',
                                        trigger: function () {
                                            Modal.dismiss();
                                        }
                                    }
                                ],
                                content: TYPO3.settings.ajaxUrls['oclock/reminder_list'],
                                ajaxCallback: function() {
                                    Modal.currentModal.find(reminder.selectorDelete).on('click', function(event) {
                                        event.preventDefault();
                                        let deleteButton = $(event.currentTarget);
                                        Modal.confirm(
                                            TYPO3.lang['oclock/reminder.delete.title'],
                                            TYPO3.lang['oclock/reminder.delete.message'],
                                            Severity.warning,
                                            [
                                                {
                                                    text: TYPO3.lang['button.cancel'],
                                                    active: true,
                                                    btnClass: 'btn-default',
                                                    trigger: function () {
                                                        Modal.dismiss();
                                                    }
                                                },{
                                                    text: TYPO3.lang['oclock/reminder.delete.button'],
                                                    btnClass: 'btn-warning',
                                                    name: 'delete',
                                                    trigger: function () {
                                                        $.ajax({
                                                            method: 'DELETE',
                                                            data: {
                                                                'reminder': deleteButton.data('uid')
                                                            },
                                                            url: TYPO3.settings.ajaxUrls['oclock/reminder_delete']
                                                        }).done(function(response) {
                                                            if (response.success) {
                                                                Notification.success(
                                                                    TYPO3.lang['oclock/reminder.delete.successfull'],
                                                                    response.message
                                                                );
                                                            } else {
                                                                Notification.error(
                                                                    TYPO3.lang['oclock/reminder.delete.error'],
                                                                    response.message
                                                                );
                                                            }
                                                        }).fail(function( jqXHR, textStatus) {
                                                            Notification.error(
                                                                TYPO3.lang['oclock/reminder.delete.error'],
                                                                textStatus
                                                            );
                                                        }).always(function() {
                                                            Modal.dismiss();
                                                        });
                                                    }
                                                }
                                            ]
                                        );
                                    });
                                    Modal.currentModal.find(reminder.selectorEdit).on('click', function(event) {
                                        event.preventDefault();
                                        let reminder = $(event.target);
                                    });
                                }
                            });
                        });
                    });
                });
            }
        };
        Reminder.init();
        return Reminder;

    }
);
