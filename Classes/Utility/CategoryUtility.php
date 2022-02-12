<?php

namespace SvenLie\WordpressMigrate\Utility;

use SvenLie\WordpressMigrate\Domain\Model\Category;
use SvenLie\WordpressMigrate\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class CategoryUtility
{
    public function __construct(CategoryRepository $categoryRepository, PersistenceManager $persistenceManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->persistenceManager = $persistenceManager;
    }

    public function insertCategories(array $categories, int $pid)
    {
        $insertedCategoryObjects = [];
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = GeneralUtility::makeInstance(CategoryRepository::class);
        $categoryRepository->injectPersistenceManager($this->persistenceManager);

        /** @var \SvenLie\WordpressMigrate\Domain\Model\WordpressApi\Category $category */
        foreach ($categories as $category) {
            $categoryObject = new Category();
            $categoryObject->setPid($pid);
            $categoryObject->setTitle($category->getName());
            $categoryObject->setSlug($category->getSlug());

            if ($category->getParent() != 0) {
                $relatedCategoryObject = $insertedCategoryObjects[$category->getParent()];
                $categoryObject->setParent($this->persistenceManager->getIdentifierByObject($relatedCategoryObject));
            }

            $categoryRepository->add($categoryObject);
            $this->persistenceManager->persistAll();
            $insertedCategoryObjects[$category->getId()] = $categoryObject;
        }

        return $insertedCategoryObjects;
    }

}