<?php

namespace SvenLie\WordpressMigrate\Utility;


use SvenLie\WordpressMigrate\Domain\Model\Content;
use SvenLie\WordpressMigrate\Domain\Model\Page;
use SvenLie\WordpressMigrate\Domain\Repository\ContentRepository;
use SvenLie\WordpressMigrate\Domain\Repository\PageRepository;
use \SvenLie\WordpressMigrate\Domain\Model\WordpressApi\Page as WordpressApiPage;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class PageUtility
{
    final const CTYPE_HEADING = 'header';
    final const CTYPE_HTML = 'html';
    final const DOKTYPE_PAGE = 1;

    public function __construct(PageRepository $pageRepository, ContentRepository $contentRepository, PersistenceManager $persistenceManager)
    {
        $this->pageRepository = $pageRepository;
        $this->contentRepository = $contentRepository;
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param \SvenLie\WordpressMigrate\Domain\Model\WordpressApi\Page[] $pages
     * @return int
     */
    public function insertPages(array $pages, int $pid)
    {
        $insertedPageObjects = [];
        $this->pageRepository->injectPersistenceManager($this->persistenceManager);
        foreach ($pages as $page) {
            $pageObject = new Page();
            $pageObject->setTitle(html_entity_decode($page->getTitle()));
            $pageObject->setDoktype($this::DOKTYPE_PAGE);
            $pageObject->setPid($pid);
            $pageObject->setSlug("/" . $page->getSlug());

            if ($page->getParent() != 0) {
                $relatedPageObject = $insertedPageObjects[$page->getParent()];
                $pageObject->setPid($this->persistenceManager->getIdentifierByObject($relatedPageObject));
            }

            $this->pageRepository->add($pageObject);
            $this->persistenceManager->persistAll();
            $this->insertPageContent($page, $pageObject);
            $insertedPageObjects[$page->getId()] = $pageObject;
        }

        return count($insertedPageObjects);
    }

    public function checkIfPageExist($pid): bool
    {
        $page = $this->pageRepository->findByUid($pid);

        if ($page) {
            return true;
        }
        return false;
    }

    protected function insertPageContent(WordpressApiPage $page, Page $pageObject) {
        $this->contentRepository->injectPersistenceManager($this->persistenceManager);
        $contentHeadingObject = new Content();
        $contentHeadingObject->setPid($pageObject->getUid());
        $contentHeadingObject->setCtype($this::CTYPE_HEADING);
        $contentHeadingObject->setHeader(html_entity_decode($page->getTitle()));
        $this->contentRepository->add($contentHeadingObject);
        $contentObject = new Content();
        $contentObject->setPid($pageObject->getUid());
        $contentObject->setCtype($this::CTYPE_HTML);
        $contentObject->setBodytext(html_entity_decode($page->getContent()));
        $this->contentRepository->add($contentObject);
        $this->persistenceManager->persistAll();
    }
}