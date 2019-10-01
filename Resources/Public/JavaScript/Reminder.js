define(['jquery', 'TYPO3/CMS/Backend/Modal', 'TYPO3/CMS/Backend/Notification', 'TYPO3/CMS/Backend/Severity', 'twbs/bootstrap-datetimepicker', 'TYPO3/CMS/Backend/DateTimePicker'],
    function ($, Modal, Notification, Severity, twbsDateTime, DateTimePicker) {
        let Reminder = {
            containerSelector: '.tx_oclock',
            selectorAdd: '.reminder-add',
            selectorList: '.reminder-list',
            selectorDelete: '.reminder-delete',
            selectorEdit: '.reminder-edit',
            addModal: null,
            listModal: null,
            deleteModal: null,
            editModal: null,
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
                            reminder.openAddModal();
                        });
                    });
                    /**
                     * Register the list button event
                     */
                    container.find(reminder.selectorList).each(function() {
                        let listButton = $(this);
                        listButton.on('click', function(event) {
                            event.preventDefault();
                            reminder.openListModal();
                        });
                    });
                });
            },
            form: '<form class="tx_oclock_form">'
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
            + '</form>',
            openAddModal: function() {
                let reminder = this;
                reminder.addModal = Modal.advanced({
                    title: TYPO3.lang['oclock/reminder.add.title'],
                    content: $(reminder.form),
                    severity: Severity.info,
                    buttons: [
                        {
                            text: TYPO3.lang['button.cancel'],
                            active: true,
                            btnClass: 'btn-default',
                            trigger: function () {
                                reminder.addModal.modal('hide');
                            }
                        }, {
                            text: TYPO3.lang['oclock/reminder.button.save'],
                            btnClass: 'btn-warning',
                            name: 'save',
                            trigger: function () {
                                $.post({
                                    data: reminder.addModal.find('form').serializeArray(),
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
                                    reminder.addModal.modal('hide');
                                });
                            }
                        }
                    ],
                    callback: function(currentModal) {
                        DateTimePicker.initializeField(currentModal.find('.tx_oclock_form .t3js-datetimepicker'));
                    }
                });
            },
            openListModal: function() {
                let reminder = this;
                reminder.listModal = Modal.advanced({
                    title: TYPO3.lang['oclock/reminder.list.title'],
                    type: Modal.types.ajax,
                    severity: Severity.notice,
                    buttons: [
                        {
                            text: TYPO3.lang['button.close'],
                            active: true,
                            btnClass: 'btn-default',
                            trigger: function () {
                                reminder.listModal.modal('hide');
                            }
                        }
                    ],
                    content: TYPO3.settings.ajaxUrls['oclock/reminder_list'],
                    ajaxCallback: function() {
                        reminder.listModal.find(reminder.selectorDelete).on('click', function(event) {
                            event.preventDefault();
                            let deleteButton = $(event.currentTarget);
                            reminder.openDeleteConfirmModal(deleteButton.data('uid'));
                        });
                        reminder.listModal.find(reminder.selectorEdit).on('click', function(event) {
                            event.preventDefault();
                            let editButton = $(event.currentTarget);
                            reminder.openEditModal(editButton.data('uid'));
                        });
                    }
                });
            },
            openDeleteConfirmModal: function(reminderId) {
                let reminder = this;
                reminder.deleteModal = Modal.confirm(
                    TYPO3.lang['oclock/reminder.delete.title'],
                    TYPO3.lang['oclock/reminder.delete.message'],
                    Severity.warning,
                    [
                        {
                            text: TYPO3.lang['button.cancel'],
                            active: true,
                            btnClass: 'btn-default',
                            trigger: function () {
                                reminder.deleteModal.modal('hide');
                            }
                        },{
                            text: TYPO3.lang['oclock/reminder.delete.button'],
                            btnClass: 'btn-warning',
                            name: 'delete',
                            trigger: function () {
                                $.ajax({
                                    method: 'DELETE',
                                    data: {
                                        'reminder': reminderId
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
                                    reminder.deleteModal.modal('hide');
                                    reminder.listModal.find('[data-reminder="' + reminderId + '"]').remove();
                                });
                            }
                        }
                    ]
                );
            },
            openEditModal: function(reminderId) {
                let reminder = this;
                reminder.editModal = Modal.advanced({
                    title: TYPO3.lang['oclock/reminder.edit.title'],
                    content: $(reminder.form),
                    severity: Severity.notice,
                    buttons: [
                        {
                            text: TYPO3.lang['button.cancel'],
                            active: true,
                            btnClass: 'btn-default',
                            trigger: function () {
                                reminder.editModal.modal('hide');
                            }
                        },
                        {
                            text: TYPO3.lang['oclock/reminder.button.save'],
                            btnClass: 'btn-warning',
                            name: 'save',
                            trigger: function () {
                                $.post({
                                    data: reminder.editModal.find('form').serializeArray(),
                                    url: TYPO3.settings.ajaxUrls['oclock/reminder_edit']
                                }).done(function(response) {
                                    if (response.success) {
                                        Notification.success(
                                            TYPO3.lang['oclock/reminder.edit.successfull'],
                                            response.message
                                        );
                                    } else {
                                        Notification.error(
                                            TYPO3.lang['oclock/reminder.edit.error'],
                                            response.message
                                        );
                                    }
                                }).fail(function( jqXHR, textStatus ) {
                                    Notification.error(
                                        TYPO3.lang['oclock/reminder.edit.error'],
                                        textStatus
                                    );
                                }).always(function() {
                                    reminder.editModal.modal('hide');
                                });
                            }
                        }
                    ],
                    callback: function(currentModal) {
                        $.get({
                            url: TYPO3.settings.ajaxUrls['oclock/reminder_get'],
                            data: {
                                reminder: reminderId
                            }
                        }).done(function(response) {
                            if (response.success) {
                                currentModal.find('[name="message"]').val(response.reminder.message);
                                currentModal.find('[name="datetime"]').val(response.reminder.datetime);
                                currentModal.find('form').append('<input type="hidden" name="reminder" value="' + response.reminder.uid + '" />');
                            } else {
                                Notification.error(
                                    TYPO3.lang['oclock/reminder.edit.error'],
                                    response.message
                                );
                            }
                        }).fail(function( jqXHR, textStatus ) {
                            reminder.editModal.modal('hide');
                            Notification.error(
                                TYPO3.lang['oclock/reminder.edit.error'],
                                textStatus
                            );
                        });
                        DateTimePicker.initializeField(currentModal.find('.tx_oclock_form .t3js-datetimepicker'));
                    }
                });
            }
        };
        Reminder.init();
        return Reminder;

    }
);
