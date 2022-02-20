<?php

namespace SvenLie\WordpressMigrate\Controller;

use SvenLie\WordpressMigrate\Service\WordpressApiClient;
use SvenLie\WordpressMigrate\Utility\CategoryUtility;
use SvenLie\WordpressMigrate\Utility\CommentUtility;
use SvenLie\WordpressMigrate\Utility\PageUtility;
use SvenLie\WordpressMigrate\Utility\PostUtility;
use SvenLie\WordpressMigrate\Utility\TagUtility;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class WordpressMigrateController extends ActionController
{
    public function __construct(ModuleTemplateFactory $moduleTemplateFactory, WordpressApiClient $wordpressApiClient, PageUtility $pageUtility, PostUtility $postUtility, CategoryUtility $categoryUtility, TagUtility $tagUtility, CommentUtility $commentUtility)
    {
        $this->wordpressApiClient = $wordpressApiClient;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->pageUtility = $pageUtility;
        $this->postUtility = $postUtility;
        $this->categoryUtility = $categoryUtility;
        $this->tagUtility = $tagUtility;
        $this->commentUtility = $commentUtility;
        $loadedExtensions = ExtensionManagementUtility::getLoadedExtensionListArray();
        $this->isNewsExtensionLoaded = in_array("news",$loadedExtensions);
        $this->isCommentExtensionLoaded = in_array("ns_news_comments",$loadedExtensions);
    }

    public function indexAction()
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        if (!$this->isNewsExtensionLoaded) {
            $this->addFlashMessage(
                LocalizationUtility::translate("LLL:EXT:wordpress_migrate/Resources/Private/Language/Backend/locallang_mod.xlf:warning.newsExtensionNotInstalled"),
                '',
                AbstractMessage::WARNING
            );
        }

        if (!$this->isCommentExtensionLoaded) {
            $this->addFlashMessage(
                LocalizationUtility::translate("LLL:EXT:wordpress_migrate/Resources/Private/Language/Backend/locallang_mod.xlf:warning.commentExtensionNotInstalled"),
                '',
                AbstractMessage::WARNING
            );
        }

        $this->view->assignMultiple(
            [
                'isNewsExtensionLoaded' => $this->isNewsExtensionLoaded,
                'isCommentExtensionLoaded' => $this->isCommentExtensionLoaded,
            ]
        );
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function migrateAction()
    {
        $wordpressUrl = $this->request->hasArgument('wordpressUrl') ? $this->request->getArgument('wordpressUrl') : null;
        $pagePid = $this->request->hasArgument('pagePid') ? $this->request->getArgument('pagePid') : null;
        $newsPid = $this->request->hasArgument('newsPid') ? $this->request->getArgument('newsPid') : null;
        $categoryPid = $this->request->hasArgument('categoryPid') ? $this->request->getArgument('categoryPid') : null;
        $tagPid = $this->request->hasArgument('tagPid') ? $this->request->getArgument('tagPid') : null;
        $commentPid = $this->request->hasArgument('commentPid') ? $this->request->getArgument('commentPid') : null;

        if (!$this->pageUtility->checkIfPageExist($pagePid)) {
            $this->addFlashMessage(
                LocalizationUtility::translate("LLL:EXT:wordpress_migrate/Resources/Private/Language/Backend/locallang_mod.xlf:error.pagePidNotExisting"),
                '',
                AbstractMessage::ERROR
            );
            $this->redirect('index');
        }

        if ($this->isNewsExtensionLoaded && !$this->pageUtility->checkIfPageExist($newsPid)) {
            $this->addFlashMessage(
                LocalizationUtility::translate("LLL:EXT:wordpress_migrate/Resources/Private/Language/Backend/locallang_mod.xlf:error.newsPidNotExisting"),
                '',
                AbstractMessage::ERROR
            );
            $this->redirect('index');
        }

        if (!$this->pageUtility->checkIfPageExist($categoryPid)) {
            $this->addFlashMessage(
                LocalizationUtility::translate("LLL:EXT:wordpress_migrate/Resources/Private/Language/Backend/locallang_mod.xlf:error.categoryPidNotExisting"),
                '',
                AbstractMessage::ERROR
            );
            $this->redirect('index');
        }

        if ($this->isNewsExtensionLoaded && !$this->pageUtility->checkIfPageExist($tagPid)) {
            $this->addFlashMessage(
                LocalizationUtility::translate("LLL:EXT:wordpress_migrate/Resources/Private/Language/Backend/locallang_mod.xlf:error.tagPidNotExisting"),
                '',
                AbstractMessage::ERROR
            );
            $this->redirect('index');
        }

        if ($this->isNewsExtensionLoaded && $this->isCommentExtensionLoaded && !$this->pageUtility->checkIfPageExist($commentPid)) {
            $this->addFlashMessage(
                LocalizationUtility::translate("LLL:EXT:wordpress_migrate/Resources/Private/Language/Backend/locallang_mod.xlf:error.commentPidNotExisting"),
                '',
                AbstractMessage::ERROR
            );
            $this->redirect('index');
        }

        if ($wordpressUrl) {
            $this->wordpressApiClient->setWordpressUri($wordpressUrl);

            $pages = $this->wordpressApiClient->getPages();
            $posts = $this->wordpressApiClient->getPosts();
            $comments = $this->wordpressApiClient->getComments();
            $categories = $this->wordpressApiClient->getCategories();
            $tags = $this->wordpressApiClient->getTags();

            if($pages === false || $posts === false || $comments === false || $categories === false || $tags === false) {
                $this->addFlashMessage(
                    LocalizationUtility::translate("LLL:EXT:wordpress_migrate/Resources/Private/Language/Backend/locallang_mod.xlf:error.errorOccurredWhileRetrieving"),
                    '',
                    AbstractMessage::ERROR
                );
            } else {
                $this->pageUtility->insertPages($pages, $pagePid);
                $insertedCategoryObjects = $this->categoryUtility->insertCategories($categories, $categoryPid);
                if ($this->isNewsExtensionLoaded) {
                    $insertedTagObjects = $this->tagUtility->insertTags($tags, $tagPid);
                    $insertedPostObjects = $this->postUtility->insertPosts($posts, $newsPid, $insertedCategoryObjects, $insertedTagObjects);
                    if($this->isCommentExtensionLoaded) {
                        $this->commentUtility->insertComments($comments,$commentPid,$insertedPostObjects);
                    }
                }

                $this->addFlashMessage(
                    LocalizationUtility::translate("LLL:EXT:wordpress_migrate/Resources/Private/Language/Backend/locallang_mod.xlf:success.migrated"),
                    '',
                    AbstractMessage::OK
                );
            }


        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate("LLL:EXT:wordpress_migrate/Resources/Private/Language/Backend/locallang_mod.xlf:error.wordpressUrlNotFilled"),
                '',
                AbstractMessage::ERROR
            );
        }

        $this->redirect('index');
    }
}