<?php

/*
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace Dravencms\AdminModule\Components\Seo\RobotsGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Locale\CurrentLocale;
use Dravencms\Model\Locale\Repository\LocaleRepository;
use Dravencms\Model\Seo\Repository\RobotsRepository;
use Kdyby\Doctrine\EntityManager;

/**
 * Description of RobotsGrid
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class RobotsGrid extends BaseControl
{

    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var RobotsRepository */
    private $robotsRepository;

    /** @var CurrentLocale */
    private $currentLocale;

    /** @var EntityManager */
    private $entityManager;

    /**
     * @var array
     */
    public $onDelete = [];

    /**
     * RobotsGrid constructor.
     * @param RobotsRepository $robotsRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     * @param CurrentLocale $currentLocale
     */
    public function __construct(
        RobotsRepository $robotsRepository,
        BaseGridFactory $baseGridFactory,
        EntityManager $entityManager,
        CurrentLocale $currentLocale
    )
    {
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->robotsRepository = $robotsRepository;
        $this->currentLocale = $currentLocale;
        $this->entityManager = $entityManager;
    }


    /**
     * @param $name
     * @return \Dravencms\Components\BaseGrid\BaseGrid
     */
    public function createComponentGrid($name)
    {
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setModel($this->robotsRepository->getRobotsQueryBuilder());

        $grid->addColumnText('name', 'Name')
            ->setFilterText()
            ->setSuggestion();

        $grid->addColumnDate('updatedAt', 'Last edit', $this->currentLocale->getDateTimeFormat())
            ->setSortable()
            ->setFilterDate();
        $grid->getColumn('updatedAt')->cellPrototype->class[] = 'center';

        $grid->addColumnBoolean('isActive', 'Active');

        if ($this->presenter->isAllowed('seo', 'robotsEdit')) {
            $grid->addActionHref('edit', 'Upravit')
                ->setIcon('pencil');
        }

        if ($this->presenter->isAllowed('seo', 'robotsDelete')) {
            $grid->addActionHref('delete', 'Smazat', 'delete!')
                ->setCustomHref(function($row){
                    return $this->link('delete!', $row->getId());
                })
                ->setIcon('trash-o')
                ->setConfirm(function ($row) {
                    return ['Opravdu chcete smazat robots directivu %s ?', $row->getName()];
                });


            $operations = ['delete' => 'Smazat'];
            $grid->setOperation($operations, [$this, 'gridOperationsHandler'])
                ->setConfirm('delete', 'Opravu chcete smazat %i locales ?');
        }
        $grid->setExport();

        return $grid;
    }

    /**
     * @param $action
     * @param $ids
     */
    public function gridOperationsHandler($action, $ids)
    {
        switch ($action)
        {
            case 'delete':
                $this->handleDelete($ids);
                break;
        }
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function handleDelete($id)
    {
        $robots = $this->robotsRepository->getById($id);
        foreach ($robots AS $robot)
        {
            $this->entityManager->remove($robot);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/RobotsGrid.latte');
        $template->render();
    }
}
