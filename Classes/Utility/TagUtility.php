<?php

namespace SvenLie\WordpressMigrate\Utility;

use GeorgRinger\News\Domain\Model\Tag;
use GeorgRinger\News\Domain\Repository\TagRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class TagUtility
{
    public function __construct(PersistenceManager $persistenceManager)
    {
        $loadedExtensions = ExtensionManagementUtility::getLoadedExtensionListArray();
        $this->isNewsExtensionLoaded = in_array("news",$loadedExtensions);
        $this->persistenceManager = $persistenceManager;
    }

    public function insertTags(array $tags, int $pid)
    {
        if (!$this->isNewsExtensionLoaded) {
            return false;
        }

        $insertedTagObjects = [];
        /** @var TagRepository $tagRepository */
        $tagRepository = GeneralUtility::makeInstance(TagRepository::class);
        $tagRepository->injectPersistenceManager($this->persistenceManager);

        /** @var \SvenLie\WordpressMigrate\Domain\Model\WordpressApi\Tag $tag */
        foreach ($tags as $tag) {
            $tagObject = new Tag();
            $tagObject->setPid($pid);
            $tagObject->setTitle($tag->getName());
            $tagObject->setSlug($tag->getSlug());
            $tagObject->setSeoDescription($tag->getDescription());

            $tagRepository->add($tagObject);
            $this->persistenceManager->persistAll();
            $insertedTagObjects[$tag->getId()] = $tagObject;
        }

        return $insertedTagObjects;
    }
}