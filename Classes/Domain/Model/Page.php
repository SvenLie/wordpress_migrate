<?php

namespace SvenLie\WordpressMigrate\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Page extends AbstractEntity
{

    protected $doktype = 0;
    /**
     * hidden
     *
     * @var boolean
     */
    protected $hidden = false;
    /**
     * deleted
     *
     * @var boolean
     */
    protected $deleted = false;
    /**
     * crdate
     *
     * @var int
     */
    protected $crdate = 0;
    /**
     * title
     *
     * @var string
     */
    protected $title = '';
    /**
     * subtitle
     *
     * @var string
     */
    protected $subtitle = '';

    /**
     * @var string
     */
    protected $slug = '';

    /**
     * Field Media
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $media;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden(bool $hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $media
     */
    public function setMedia(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $media)
    {
        $this->media = $media;
    }

    public function addMedia(\TYPO3\CMS\Extbase\Domain\Model\FileReference $media)
    {
        $this->media->attach($media);
    }

    public function removeMedia(\TYPO3\CMS\Extbase\Domain\Model\FileReference $media)
    {
        $this->media->detach($media);
    }

    /**
     * @return int
     */
    public function getCrdate(): int
    {
        return $this->crdate;
    }

    /**
     * @param int $crdate
     */
    public function setCrdate(int $crdate)
    {
        $this->crdate = $crdate;
    }

    /**
     * @return int
     */
    public function getDoktype(): int
    {
        return $this->doktype;
    }

    /**
     * @param int $doktype
     */
    public function setDoktype(int $doktype)
    {
        $this->doktype = $doktype;
    }

}