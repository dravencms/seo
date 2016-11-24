<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace App\Model\Seo\Repository;

use App\Model\Seo\Entities\Robots;
use Kdyby\Doctrine\EntityManager;
use Nette;

class RobotsRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
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
     * @param $id
     * @return mixed|null|Robots
     */
    public function getOneById($id)
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
     * @return \Kdyby\Doctrine\QueryBuilder
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
    public function isNameFree($name, Robots $robotsIgnore = null)
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
    public function isPathFree($path, Robots $robotsIgnore = null)
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
    public function getActive($isActive = true)
    {
        return $this->robotsRepository->findBy(['isActive' => $isActive]);
    }
}