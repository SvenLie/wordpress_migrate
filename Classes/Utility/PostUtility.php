<?php

namespace SvenLie\WordpressMigrate\Utility;

use GeorgRinger\News\Domain\Model\News;
use GeorgRinger\News\Domain\Repository\NewsRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class PostUtility
{

    public function __construct(PersistenceManager $persistenceManager)
    {
        $loadedExtensions = ExtensionManagementUtility::getLoadedExtensionListArray();
        $this->isNewsExtensionLoaded = in_array("news",$loadedExtensions);
        $this->persistenceManager = $persistenceManager;
    }

    public function insertPosts(array $posts, int $pid, array|bool $insertedCategoryObjects, array|bool $insertedTagObjects)
    {
        if (!$this->isNewsExtensionLoaded) {
            return false;
        }

        $insertedPostObjects = [];
        /** @var NewsRepository $newsRepository */
        $newsRepository = GeneralUtility::makeInstance(NewsRepository::class);
        $newsRepository->injectPersistenceManager($this->persistenceManager);

        foreach ($posts as $post) {
            $postObject = new News();
            $postObject->setPid($pid);
            $postObject->setDatetime(new \DateTime($post->getDateTime()));
            $postObject->setTitle(html_entity_decode($post->getTitle()));
            $postObject->setBodytext(html_entity_decode($post->getContent()));
            $postObject->setTeaser(strip_tags(html_entity_decode($post->getExcerpt())));
            $postObject->setPathSegment($post->getSlug());
            $postObject->setType(0);

            if (!empty($post->getCategories())) {
                $relatedCategoryObjects = new ObjectStorage();
                foreach ($post->getCategories() as $categoryId) {
                    $relatedCategoryObject = $insertedCategoryObjects[$categoryId];
                    $relatedCategoryObjects->attach($relatedCategoryObject);
                }
                $postObject->setCategories($relatedCategoryObjects);
            }

            if (!empty($post->getTags())) {
                $relatedTagObjects = new ObjectStorage();
                foreach ($post->getTags() as $tagId) {
                    $relatedTagObject = $insertedTagObjects[$tagId];
                    $relatedTagObjects->attach($relatedTagObject);
                }
                $postObject->setTags($relatedTagObjects);
            }

            $newsRepository->add($postObject);
            $this->persistenceManager->persistAll();
            $insertedPostObjects[$post->getId()] = $postObject;
        }

        return $insertedPostObjects;
    }
}