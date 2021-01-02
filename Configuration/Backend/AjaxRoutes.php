<?php
return [
    'oclock/reminder_show_add_form' => [
        'path' => '/oclock/reminder/showAddForm',
        'target' => TheCodingOwl\Oclock\Controller\ReminderController::class . '::showAddFormAction'
    ],
    'oclock/reminder_show_edit_form' => [
        'path' => '/oclock/reminder/showEditForm',
        'target' => TheCodingOwl\Oclock\Controller\ReminderController::class . '::showEditFormAction'
    ],
    'oclock/reminder_add' => [
        'path' => '/oclock/reminder/add',
        'target' => TheCodingOwl\Oclock\Controller\ReminderController::class . '::addAction'
    ],
    'oclock/reminder_edit' => [
        'path' => '/oclock/reminder/edit',
        'target' => TheCodingOwl\Oclock\Controller\ReminderController::class . '::editAction'
    ],
    'oclock/reminder_delete' => [
        'path' => '/oclock/reminder/delete',
        'target' => TheCodingOwl\Oclock\Controller\ReminderController::class . '::deleteAction'
    ]
];
