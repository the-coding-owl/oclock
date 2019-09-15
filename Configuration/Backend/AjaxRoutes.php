<?php
return [
    'oclock/reminder_add' => [
        'path' => '/oclock/reminder/add',
        'target' => TheCodingOwl\Oclock\Controller\ReminderController::class . '->addAction'
    ],
    'oclock/reminder_edit' => [
        'path' => '/oclock/reminder/edit',
        'target' => TheCodingOwl\Oclock\Controller\ReminderController::class . '->editAction'
    ],
    'oclock/reminder_delete' => [
        'path' => '/oclock/reminder/delete',
        'target' => TheCodingOwl\Oclock\Controller\ReminderController::class . '->deleteAction'
    ],
    'oclock/reminder_list' => [
        'path' => '/oclock/reminder/list',
        'target' => TheCodingOwl\Oclock\Controller\ReminderController::class . '->listAction'
    ],
];
