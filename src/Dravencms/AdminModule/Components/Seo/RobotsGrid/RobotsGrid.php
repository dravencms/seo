<?php declare(strict_types = 1);

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
use Dravencms\Components\BaseGrid\Grid;
use Dravencms\Locale\CurrentLocaleResolver;
use Dravencms\Model\Seo\Repository\RobotsRepository;
use Dravencms\Database\EntityManager;
use Contributte\Datagrid\Column\Action\Confirmation\StringConfirmation;
use Dravencms\User\Attributes\IsAllowed;
use Nette\Security\User;

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
    
    /** @var User */
    private $user;

    /**
     * @var array
     */
    public $onDelete = [];

    /**
     * RobotsGrid constructor.
     * @param RobotsRepository $robotsRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     * @param User $user
     * @param CurrentLocaleResolver $currentLocaleResolver
     */
    public function __construct(
        RobotsRepository $robotsRepository,
        BaseGridFactory $baseGridFactory,
        EntityManager $entityManager,
        User $user,
        CurrentLocaleResolver $currentLocaleResolver
    )
    {
        $this->user = $user;
        $this->baseGridFactory = $baseGridFactory;
        $this->robotsRepository = $robotsRepository;
        $this->currentLocale = $currentLocaleResolver->getCurrentLocale();
        $this->entityManager = $entityManager;
    }


    /**
     * @param string $name
     * @return Grid
     */
    public function createComponentGrid(string $name): Grid
    {
        /** @var Grid $grid */
        $grid = $this->baseGridFactory->create($this, $name);
        $grid->setWritePermission('seo', 'robotsEdit');

        $grid->setDataSource($this->robotsRepository->getRobotsQueryBuilder());

        $grid->addColumnText('name', 'seo.admin.field.name')
            ->setFilterText();

        $grid->addColumnDateTime('updatedAt', 'seo.admin.field.updated')
            ->setAlign('center')
            ->setFormat($this->currentLocale->getDateTimeFormat())
            ->setSortable()
            ->setFilterDate();

        $grid->addColumnBoolean('isActive', 'seo.admin.field.active');

        if ($this->user->isAllowed('seo', 'robotsEdit')) {

            $grid->addAction('edit', '', 'edit')
                ->setIcon('pencil')
                ->setTitle('seo.admin.action.edit')
                ->setClass('btn btn-sm btn-primary');
        }

        if ($this->user->isAllowed('seo', 'robotsDelete')) {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('seo.admin.action.delete')
                ->setClass('btn btn-sm btn-danger ajax')
                ->setConfirmation(new StringConfirmation('seo.admin.confirm.delete', 'name'));

            $grid->addGroupAction('seo.admin.action.delete')->onSelect[] = [$this, 'handleDelete'];
        }

        return $grid;
    }

    /**
     * @param $id
     * @throws \Exception
     */
    #[\Dravencms\User\Attributes\IsAllowed('seo', 'robotsDelete')]
    #[\Dravencms\Security\Mutation]
    public function handleDelete($id): void
    {
        $this->requirePermission('seo', 'robotsDelete');
        $robots = $this->robotsRepository->getById($id);
        foreach ($robots as $robot)
        {
            $this->entityManager->remove($robot);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render(): void
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/RobotsGrid.latte');
        $template->render();
    }
}
