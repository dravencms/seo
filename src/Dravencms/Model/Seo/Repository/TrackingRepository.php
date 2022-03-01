<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Seo\Repository;

use Dravencms\Model\Seo\Entities\Tracking;
use Dravencms\Model\Seo\Entities\TrackingService;
use Dravencms\Database\EntityManager;

class TrackingRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|Tracking */
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
     * @param int $id
     * @return null|Tracking
     */
    public function getOneById(int $id): Tracking
    {
        return $this->trackingRepository->find($id);
    }

    /**
     * @param $id
     * @return Tracking[]
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
    public function isNameFree(string $name, TrackingService $trackingService, Tracking $ignoreTracking = null): bool
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
    public function isIdentifierFree(string $identifier, TrackingService $trackingService, Tracking $ignoreTracking = null): bool
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
    public function getByPosition(string $position = TrackingService::POSITION_HEADER)
    {
        $qb = $this->trackingRepository->createQueryBuilder('t')
            ->select('t')
            ->join('t.trackingService', 'ts')
            ->where('ts.position = :position')
            ->setParameter('position', $position);

        return $qb->getQuery()->getResult();
    }
}