<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Seo\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Dravencms\Database\Attributes\Identifier;
use Nette;

/**
 * Class Robots
 * @package App\Model\Structure\Entities
 * @ORM\Entity
 * @ORM\Table(name="seoRobots")
 */
class Robots
{
    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    const ACTION_ALLOW = 'Allow';
    const ACTION_DISALLOW = 'Disallow';

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=false)
     */
    private $path;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var string
     * @ORM\Column(type="string",length=255)
     */
    private $action;

    /**
     * Robots constructor.
     * @param string $name
     * @param string $path
     * @param bool $isActive
     * @param string $action
     */
    public function __construct(string $name, string $path, bool $isActive = true, string $action = self::ACTION_ALLOW)
    {
        $this->name = $name;
        $this->path = $path;
        $this->isActive = $isActive;
        $this->setAction($action);
    }


    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        if (!in_array($action, array(self::ACTION_ALLOW, self::ACTION_DISALLOW))) {
            throw new \InvalidArgumentException("Invalid $action");
        }
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return boolean
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }
    
}