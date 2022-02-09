<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'WordpressMigrate',
    'migrate',
    [
        \SvenLie\WordpressMigrate\Controller\WordpressMigrateController::class => 'index, migrate'
    ],
    [],
);