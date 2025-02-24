<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Reset Scheduler',
    'description' => 'Reset Scheduler Tasks',
    'category' => 'plugin',
    'author' => 'Stefan Froemken',
    'author_email' => 'projects@jweiland.net',
    'author_company' => 'jweiland.net',
    'state' => 'stable',
    'version' => '1.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.25-13.4.99',
            'scheduler' => '12.4.25-13.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
