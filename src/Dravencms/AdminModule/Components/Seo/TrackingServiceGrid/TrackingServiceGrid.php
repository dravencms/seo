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

namespace Dravencms\AdminModule\Components\Seo\TrackingServiceGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Components\BaseGrid\Grid;
use Dravencms\Locale\CurrentLocaleResolver;
use Dravencms\Model\Seo\Repository\TrackingServiceRepository;
use Dravencms\Database\EntityManager;
use Contributte\Datagrid\Column\Action\Confirmation\StringConfirmation;
use Dravencms\User\Attributes\IsAllowed;
use Nette\Security\User;

/**
 * Description of TrackingServiceGrid
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class TrackingServiceGrid extends BaseControl
{

    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var TrackingServiceRepository */
    private $trackingServiceRepository;

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
     * TrackingServiceGrid constructor.
     * @param TrackingServiceRepository $trackingServiceRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     * @param User $user
     * @param CurrentLocaleResolver $currentLocaleResolver
     */
    public function __construct(
        TrackingServiceRepository $trackingServiceRepository,
        BaseGridFactory $baseGridFactory,
        EntityManager $entityManager,
        User $user,
        CurrentLocaleResolver $currentLocaleResolver
    )
    {
        $this->baseGridFactory = $baseGridFactory;
        $this->trackingServiceRepository = $trackingServiceRepository;
        $this->currentLocale = $currentLocaleResolver->getCurrentLocale();
        $this->entityManager = $entityManager;
        $this->user = $user;
    }


    /**
     * @param string $name
     * @return Grid
     */
    public function createComponentGrid(string $name): Grid
    {
        /** @var Grid $grid */
        $grid = $this->baseGridFactory->create($this, $name);
        $grid->setWritePermission('seo', 'trackingEdit');

        $grid->setDataSource($this->trackingServiceRepository->getTrackingServiceQueryBuilder());

        $grid->addColumnText('name', 'seo.admin.field.name')
            ->setFilterText();

        $grid->addColumnNumber('position', 'seo.admin.field.position')
            ->setAlign('center')
            ->setFilterRange();

        $countCol = function ($row) {
            return count($row->getTrackings());
        };

        $grid->addColumnText('users', 'seo.admin.field.uses')
            ->setAlign('center')
            ->setRenderer($countCol);

        $grid->addColumnDateTime('updatedAt', 'seo.admin.field.updated')
            ->setFormat($this->currentLocale->getDateTimeFormat())
            ->setAlign('center')
            ->setSortable()
            ->setFilterDate();

        if ($this->user->isAllowed('seo', 'trackingEdit')) {

            $grid->addAction('edit', '', 'edit')
                ->setIcon('pencil')
                ->setTitle('seo.admin.action.edit')
                ->setClass('btn btn-sm btn-primary');
        }

        if ($this->user->isAllowed('seo', 'trackingDelete')) {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('seo.admin.action.delete')
                ->setClass('btn btn-sm btn-danger ajax')
                ->setConfirmation(new StringConfirmation('seo.admin.confirm.delete', 'name'));

            $grid->allowRowsAction('delete', function($item) {
                return !(count($item->getTrackings()) > 0);
            });

            $grid->addGroupAction('seo.admin.action.delete')->onSelect[] = [$this, 'handleDelete'];
        }

        return $grid;
    }
    
    /**
     * @param $id
     * @throws \Exception
     */
    #[\Dravencms\User\Attributes\IsAllowed('seo', 'trackingDelete')]
    #[\Dravencms\Security\Mutation]
    public function handleDelete($id): void
    {
        $this->requirePermission('seo', 'trackingDelete');
        $trackingServices = $this->trackingServiceRepository->getById($id);
        foreach ($trackingServices as $trackingService)
        {
            $this->entityManager->remove($trackingService);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render(): void
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/TrackingServiceGrid.latte');
        $template->render();
    }
}
