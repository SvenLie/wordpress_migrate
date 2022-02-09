<?php

declare(strict_types = 1);

return [
    \SvenLie\WordpressMigrate\Domain\Model\Page::class => [
        'tableName' => 'pages',
    ],
    \SvenLie\WordpressMigrate\Domain\Model\Content::class => [
        'tableName' => 'tt_content',
        'properties' => [
            'ctype' => [
                'fieldName' => 'CType'
            ]
        ]
    ],
];