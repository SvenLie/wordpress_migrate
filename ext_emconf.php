<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Wordpress Migrate',
    'description' => 'This TYPO3 extension can migrate an existing wordpress instance to TYPO3',
    'category' => 'plugin',
    'author' => 'Sven Liebert',
    'author_email' => 'mail@sven-liebert.de',
    'state' => 'alpha',
    'clearCacheOnLoad' => true,
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '11.0.0-11.99.99',
        ]
    ],
    'autoload' => [
        'psr-4' => [
            'SvenLie\\WordpressMigrate\\' => 'Classes'
        ]
    ],
];