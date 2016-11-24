<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace App\Model\Seo\Repository;

use App\Model\Seo\Entities\Robots;
use App\Model\Seo\Entities\Tracking;
use App\Model\Seo\Entities\TrackingService;
use Kdyby\Doctrine\EntityManager;
use Nette;

class TrackingRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $trackingRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * MenuRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->trackingRepository = $entityManager->getRepository(Tracking::class);
    }

    /**
     * @param $id
     * @return mixed|null|Tracking
     */
    public function getOneById($id)
    {
        return $this->trackingRepository->find($id);
    }

    /**
     * @param $id
     * @return array
     */
    public function getById($id)
    {
        return $this->trackingRepository->findBy(['id' => $id]);
    }

    /**
     * @param $name
     * @param TrackingService $trackingService
     * @param Tracking|null $ignoreTracking
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isNameFree($name, TrackingService $trackingService, Tracking $ignoreTracking = null)
    {
        $qb = $this->trackingRepository->createQueryBuilder('t')
            ->select('t')
            ->where('t.name = :name')
            ->andWhere('t.trackingService = :trackingService')
            ->setParameters([
                'name' => $name,
                'trackingService' => $trackingService
            ]);

        if ($ignoreTracking)
        {
            $qb->andWhere('t != :ignoreTracking')
                ->setParameter('ignoreTracking', $ignoreTracking);
        }

        return (is_null($qb->getQuery()->getOneOrNullResult()));
    }

    /**
     * @param $identifier
     * @param TrackingService $trackingService
     * @param Tracking|null $ignoreTracking
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isIdentifierFree($identifier, TrackingService $trackingService, Tracking $ignoreTracking = null)
    {
        $qb = $this->trackingRepository->createQueryBuilder('t')
            ->select('t')
            ->where('t.identifier = :identifier')
            ->andWhere('t.trackingService = :trackingService')
            ->setParameters([
                'identifier' => $identifier,
                'trackingService' => $trackingService
            ]);

        if ($ignoreTracking)
        {
            $qb->andWhere('t != :ignoreTracking')
                ->setParameter('ignoreTracking', $ignoreTracking);
        }

        return (is_null($qb->getQuery()->getOneOrNullResult()));
    }

    /**
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function getTrackingQueryBuilder()
    {
        return $this->trackingRepository->createQueryBuilder('t')
            ->select('t');
    }

    /**
     * @param string $position
     * @return array
     */
    public function getByPosition($position = TrackingService::POSITION_HEADER)
    {
        $qb = $this->trackingRepository->createQueryBuilder('t')
            ->select('t')
            ->join('t.trackingService', 'ts')
            ->where('ts.position = :position')
            ->setParameter('position', $position);

        return $qb->getQuery()->getResult();
    }
}