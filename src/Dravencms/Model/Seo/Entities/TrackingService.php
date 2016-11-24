<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Seo\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class RobotsAction
 * @package App\Model\Structure\Entities
 * @ORM\Entity
 * @ORM\Table(name="seoTrackingService")
 */
class TrackingService extends Nette\Object
{
    use Identifier;
    use TimestampableEntity;

    const POSITION_HEADER = 'header';
    const POSITION_BODY_BOTTOM = 'bodyBottom';

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text",nullable=false)
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(type="string",length=255)
     */
    private $position;

    /**
     * @var ArrayCollection|Tracking[]
     * @ORM\OneToMany(targetEntity="Tracking", mappedBy="trackingService",cascade={"persist"})
     */
    private $trackings;

    /**
     * TrackingType constructor.
     * @param string $name
     * @param string $code
     * @param string $position
     */
    public function __construct($name, $code, $position = self::POSITION_BODY_BOTTOM)
    {
        $this->name = $name;
        $this->code = $code;
        $this->position = $position;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        if (!in_array($position, [self::POSITION_HEADER, self::POSITION_BODY_BOTTOM]))
        {
            throw new \InvalidArgumentException('argument $position has invalid value');
        }
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return Tracking[]|ArrayCollection
     */
    public function getTrackings()
    {
        return $this->trackings;
    }
}