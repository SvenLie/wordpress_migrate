<?php

namespace SvenLie\WordpressMigrate\Controller;

use SvenLie\WordpressMigrate\Service\WordpressApiClient;
use SvenLie\WordpressMigrate\Utility\PageUtility;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class WordpressMigrateController extends ActionController
{
    public function __construct(ModuleTemplateFactory $moduleTemplateFactory, WordpressApiClient $wordpressApiClient, PageUtility $pageUtility)
    {
        $this->wordpressApiClient = $wordpressApiClient;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->pageUtility = $pageUtility;
    }

    public function indexAction()
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function migrateAction()
    {
        $wordpressUrl = $this->request->getArgument('wordpressUrl');

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
                $this->pageUtility->insertPages($pages);

                $this->addFlashMessage(
                    "geht",
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