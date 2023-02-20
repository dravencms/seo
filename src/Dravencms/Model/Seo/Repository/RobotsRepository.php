<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Seo\Repository;

use Dravencms\Model\Seo\Entities\Robots;
use Dravencms\Database\EntityManager;

class RobotsRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|Robots */
    private $robotsRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * MenuRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->robotsRepository = $entityManager->getRepository(Robots::class);
    }

    /**
     * @param int $id
     * @return null|Robots
     */
    public function getOneById(int $id): Robots
    {
        return $this->robotsRepository->find($id);
    }

    /**
     * @param $id
     * @return Robots[]
     */
    public function getById($id)
    {
        return $this->robotsRepository->findBy(['id' => $id]);
    }

    /**
     * @return QueryBuilder
     */
    public function getRobotsQueryBuilder()
    {
        $qb = $this->robotsRepository->createQueryBuilder('r')
            ->select('r');
        return $qb;
    }

    /**
     * @param $name
     * @param Robots|null $robotsIgnore
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isNameFree(string $name, Robots $robotsIgnore = null): bool
    {
        $qb = $this->robotsRepository->createQueryBuilder('r')
            ->select('r')
            ->where('r.name = :name')
            ->setParameters([
                'name' => $name
            ]);

        if ($robotsIgnore)
        {
            $qb->andWhere('r != :robotsIgnore')
                ->setParameter('robotsIgnore', $robotsIgnore);
        }

        return (is_null($qb->getQuery()->getOneOrNullResult()));
    }

    /**
     * @param $path
     * @param Robots|null $robotsIgnore
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isPathFree(string $path, Robots $robotsIgnore = null): bool
    {
        $qb = $this->robotsRepository->createQueryBuilder('r')
            ->select('r')
            ->where('r.path = :path')
            ->setParameters([
                'path' => $path
            ]);

        if ($robotsIgnore)
        {
            $qb->andWhere('r != :robotsIgnore')
                ->setParameter('robotsIgnore', $robotsIgnore);
        }

        return (is_null($qb->getQuery()->getOneOrNullResult()));
    }

    /**
     * @return Robots[]
     */
    public function getAll()
    {
        return $this->robotsRepository->findAll();
    }

    /**
     * @param bool $isActive
     * @return array
     */
    public function getActive(bool $isActive = true)
    {
        return $this->robotsRepository->findBy(['isActive' => $isActive]);
    }
}