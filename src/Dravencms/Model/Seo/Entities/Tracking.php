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
 * Class Robots
 * @package App\Model\Structure\Entities
 * @ORM\Entity
 * @ORM\Table(name="seoTracking")
 */
class Tracking
{
    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=false)
     */
    private $identifier;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var TrackingService
     * @ORM\ManyToOne(targetEntity="TrackingService", inversedBy="trackings")
     * @ORM\JoinColumn(name="tracking_service_id", referencedColumnName="id")
     */
    private $trackingService;

    /**
     * Tracking constructor.
     * @param TrackingService $trackingService
     * @param $name
     * @param $identifier
     * @param bool $isActive
     */
    public function __construct(TrackingService $trackingService, $name, $identifier, $isActive = true)
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->isActive = $isActive;
        $this->trackingService = $trackingService;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @param TrackingService $trackingService
     */
    public function setTrackingService(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
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
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return TrackingService
     */
    public function getTrackingService()
    {
        return $this->trackingService;
    }
    
}