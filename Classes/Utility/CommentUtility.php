<?php

namespace SvenLie\WordpressMigrate\Utility;

use Nitsan\NsNewsComments\Domain\Model\Comment;
use Nitsan\NsNewsComments\Domain\Repository\CommentRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class CommentUtility
{
    public function __construct(PersistenceManager $persistenceManager)
    {
        $loadedExtensions = ExtensionManagementUtility::getLoadedExtensionListArray();
        $this->isNewsExtensionLoaded = in_array("news",$loadedExtensions);
        $this->isCommentExtensionLoaded = in_array("ns_news_comments",$loadedExtensions);
        $this->persistenceManager = $persistenceManager;
    }

    public function insertComments(array $comments, int $pid, array|bool $insertedPostObjects)
    {
        if (!$this->isNewsExtensionLoaded || !$this->isCommentExtensionLoaded) {
            return false;
        }

        $insertedCommentObjects = [];
        /** @var CommentRepository $commentRepository */
        $commentRepository = GeneralUtility::makeInstance(CommentRepository::class);
        $commentRepository->injectPersistenceManager($this->persistenceManager);

        /** @var \SvenLie\WordpressMigrate\Domain\Model\WordpressApi\Comment $comment */
        foreach ($comments as $comment) {
            $commentObject = new Comment();
            $commentObject->setPid($pid);
            $commentObject->setNewsuid($insertedPostObjects[$comment->getPost()]->getUid());
            $commentObject->setDescription(strip_tags(html_entity_decode($comment->getContent())));

            $commentRepository->add($commentObject);
            $this->persistenceManager->persistAll();
            $insertedCommentObjects[$comment->getId()] = $commentObject;
        }

        return $insertedCommentObjects;
    }
}