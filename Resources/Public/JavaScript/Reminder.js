define(['jquery', 'TYPO3/CMS/Backend/Modal', 'TYPO3/CMS/Backend/Notification', 'TYPO3/CMS/Backend/Severity', 'twbs/bootstrap-datetimepicker', 'TYPO3/CMS/Backend/DateTimePicker'],
    function ($, Modal, Notification, Severity, twbsDateTime, DateTimePicker) {
        let Reminder = {
            containerSelector: '.tx_oclock',
            selectorAdd: '.reminder-add',
            selectorDelete: '.reminder-delete',
            selectorEdit: '.reminder-edit',
            selectorList: '.reminder-list',
            addModal: null,
            deleteModal: null,
            editModal: null,
            list: {
                items: [],
                remove: function(reminderId) {
                    this.items[reminderId].remove();
                }
            },
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
                    container.find(reminder.selectorEdit).each(function() {
                        let editButton = $(this);
                        editButton.on('click', function(event) {
                            event.preventDefault();
                            reminder.openEditModal(editButton.data('reminder'), editButton.data('editUrl'));
                        });
                        reminder.list.items[editButton.data('reminder')] = editButton.closest('li');
                    });
                });
            },
            openAddModal: function() {
                let reminder = this;
                reminder.addModal = Modal.loadUrl(
                    TYPO3.lang['oclock/reminder.add.title'],
                    Severity.info,
                    [
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
                    TYPO3.settings.ajaxUrls['oclock/reminder_show_add_form'],
                    function() {
                        DateTimePicker.initializeField(Modal.currentModal.find('.tx_oclock_form .t3js-datetimepicker'));
                    }
                );
            },
            openEditModal: function(reminderId, editUrl) {
                let reminder = this;
                reminder.editModal = Modal.loadUrl(
                    TYPO3.lang['oclock/reminder.edit.title'],
                    Severity.info,
                    [
                        {
                            text: TYPO3.lang['button.cancel'],
                            active: true,
                            btnClass: 'btn-default',
                            trigger: function () {
                                reminder.editModal.modal('hide');
                            }
                        },
                        {
                            text: TYPO3.lang['button.delete'],
                            btnClass: 'btn-danger',
                            name: 'delete',
                            trigger: function() {
                                reminder.editModal.modal('hide');
                                reminder.openDeleteConfirmModal(reminderId);
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
                    editUrl,
                    function() {
                        DateTimePicker.initializeField(Modal.currentModal.find('.tx_oclock_form .t3js-datetimepicker'));
                    }
                );
            },
            openDeleteConfirmModal: function(reminderId) {
                let reminder = this;
                reminder.deleteModal = Modal.confirm(
                    TYPO3.lang['oclock/reminder.delete.title'],
                    TYPO3.lang['oclock/reminder.delete.message'],
                    Severity.danger,
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
                            btnClass: 'btn-danger',
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
                                    reminder.list.remove(reminderId);
                                });
                            }
                        }
                    ]
                );
            }
        };
        Reminder.init();
        return Reminder;

    }
);
