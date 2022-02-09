<?php

namespace SvenLie\WordpressMigrate\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Content extends AbstractEntity
{
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
    protected $header = '';

    /**
     * @var string
     */
    protected $bodytext = '';

    /**
     * @var string
     */
    protected $ctype = '';

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
    public function setHidden(bool $hidden): void
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
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
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
    public function setCrdate(int $crdate): void
    {
        $this->crdate = $crdate;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader(string $header): void
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getBodytext(): string
    {
        return $this->bodytext;
    }

    /**
     * @param string $bodytext
     */
    public function setBodytext(string $bodytext): void
    {
        $this->bodytext = $bodytext;
    }

    /**
     * @return string
     */
    public function getCtype(): string
    {
        return $this->ctype;
    }

    /**
     * @param string $ctype
     */
    public function setCtype(string $ctype): void
    {
        $this->ctype = $ctype;
    }
}