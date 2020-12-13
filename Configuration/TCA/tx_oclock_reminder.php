<?php

return [
    'ctrl' => [
        'title' => 'Reminder',
        'hideTable' => TRUE,
        'rootLevel' => TRUE,
        'cruser_id' => 'user'
    ],
    'interface' => '',
    'types' => [
        [ 'showitems' => '' ]
    ],
    'columns' => [
        'user' => [
            'label' => 'User',
            'exclude' => FALSE,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'be_users'
            ]
        ],
        'message' => [
            'label' => 'Message',
            'exclude' => FALSE,
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 4
            ]
        ],
        'datetime' => [
            'label' => 'Datetime',
            'exclude' => FALSE,
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'default' => (new \DateTime())->format('Y-m-d H:i:s')
            ]
        ]
    ]
];
