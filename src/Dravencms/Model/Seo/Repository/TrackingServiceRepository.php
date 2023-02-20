<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Seo\Repository;

use Dravencms\Model\Seo\Entities\TrackingService;
use Dravencms\Database\EntityManager;

class TrackingServiceRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|TrackingService */
    private $trackingServiceRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * MenuRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->trackingServiceRepository = $entityManager->getRepository(TrackingService::class);
    }

    /**
     * @param int $id
     * @return null|TrackingService
     */
    public function getOneById(int $id): TrackingService
    {
        return $this->trackingServiceRepository->find($id);
    }

    /**
     * @param $id
     * @return TrackingService[]
     */
    public function getById($id)
    {
        return $this->trackingServiceRepository->findBy(['id' => $id]);
    }

    /**
     * @return QueryBuilder
     */
    public function getTrackingServiceQueryBuilder()
    {
        $qb = $this->trackingServiceRepository->createQueryBuilder('ts')
            ->select('ts');
        return $qb;
    }

    /**
     * @param $name
     * @param TrackingService|null $ignoreTrackingService
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isNameFree(string $name, TrackingService $ignoreTrackingService = null): bool
    {
        $qb = $this->trackingServiceRepository->createQueryBuilder('ts')
            ->select('ts')
            ->where('ts.name = :name')
            ->setParameters([
                'name' => $name
            ]);

        if ($ignoreTrackingService)
        {
            $qb->andWhere('ts != :ignoreTrackingService')
                ->setParameter('ignoreTrackingService', $ignoreTrackingService);
        }

        return (is_null($qb->getQuery()->getOneOrNullResult()));
    }

    /**
     * @return array
     */
    public function getPairs()
    {
        return $this->trackingServiceRepository->findPairs('name');
    }
}