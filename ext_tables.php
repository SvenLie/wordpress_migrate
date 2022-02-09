<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'WordpressMigrate',
    'tools',
    'migrate',
    '',
    [
        \SvenLie\WordpressMigrate\Controller\WordpressMigrateController::class => 'index,migrate'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:wordpress_migrate/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:wordpress_migrate/Resources/Private/Language/Backend/locallang_mod.xlf'
    ]
);