<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace App\Model\Seo\Repository;

use App\Model\Seo\Entities\TrackingService;
use Kdyby\Doctrine\EntityManager;
use Nette;

class TrackingServiceRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
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
     * @param $id
     * @return mixed|null|TrackingService
     */
    public function getOneById($id)
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
     * @return \Kdyby\Doctrine\QueryBuilder
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
    public function isNameFree($name, TrackingService $ignoreTrackingService = null)
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