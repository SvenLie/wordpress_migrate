<?php

namespace SvenLie\WordpressMigrate\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\Repository;

class CategoryRepository extends Repository
{
    public function initializeObject()
    {
        /** @var $defaultQuerySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
        $defaultQuerySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $defaultQuerySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($defaultQuerySettings);
    }
}