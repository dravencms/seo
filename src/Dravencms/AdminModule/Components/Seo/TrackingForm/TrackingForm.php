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

namespace Dravencms\AdminModule\Components\Seo\TrackingForm;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Seo\Entities\Tracking;
use Dravencms\Model\Seo\Repository\TrackingRepository;
use Dravencms\Model\Seo\Repository\TrackingServiceRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;

/**
 * Description of TrackingForm
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class TrackingForm extends BaseControl
{
    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var EntityManager */
    private $entityManager;

    /** @var TrackingRepository */
    private $trackingRepository;

    /** @var TrackingServiceRepository */
    private $trackingServiceRepository;

    /** @var Tracking */
    private $tracking = null;

    /** @var array */
    public $onSuccess = [];

    /**
     * TrackingForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param EntityManager $entityManager
     * @param TrackingRepository $trackingRepository
     * @param TrackingServiceRepository $trackingServiceRepository
     * @param Tracking|null $tracking
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        EntityManager $entityManager,
        TrackingRepository $trackingRepository,
        TrackingServiceRepository $trackingServiceRepository,
        Tracking $tracking = null
    ) {
        parent::__construct();

        $this->tracking = $tracking;

        $this->baseFormFactory = $baseFormFactory;
        $this->entityManager = $entityManager;
        $this->trackingRepository = $trackingRepository;
        $this->trackingServiceRepository = $trackingServiceRepository;


        if ($this->tracking) {
            $defaults = [
                'name' => $this->tracking->getName(),
                'identifier' => $this->tracking->getIdentifier(),
                'trackingService' => $this->tracking->getTrackingService()->getId(),
                'isActive' => $this->tracking->isActive()
            ];
        }
        else
        {
            $defaults = [
                'isActive' => true
            ];
        }

        $this['form']->setDefaults($defaults);
    }

    /**
     * @return \Dravencms\Components\BaseForm\BaseForm
     */
    protected function createComponentForm()
    {
        $form = $this->baseFormFactory->create();

        $form->addText('name')
            ->setRequired('Please enter directive name.')
            ->addRule(Form::MAX_LENGTH, 'Directive name name is too long.', 255);

        $form->addText('identifier')
            ->setRequired('Please enter identifier.')
            ->addRule(Form::MAX_LENGTH, 'Identifier is too long.', 255);

        $form->addSelect('trackingService', null, $this->trackingServiceRepository->getPairs())
            ->setRequired('Please enter tracking type.');

        $form->addCheckbox('isActive');


        $form->addSubmit('send');

        $form->onValidate[] = [$this, 'editFormValidate'];
        $form->onSuccess[] = [$this, 'editFormSucceeded'];

        return $form;
    }

    /**
     * @param Form $form
     */
    public function editFormValidate(Form $form)
    {
        $values = $form->getValues();

        $trackingService = $this->trackingServiceRepository->getOneById($values->trackingService);

        if (!$this->trackingRepository->isNameFree($values->name, $trackingService, $this->tracking)) {
            $form->addError('Tento název je již zabrán.');
        }

        if (!$this->trackingRepository->isIdentifierFree($values->name, $trackingService, $this->tracking)) {
            $form->addError('Tento název je již zabrán.');
        }

        if (!$this->presenter->isAllowed('seo', 'trackingEdit')) {
            $form->addError('Nemáte oprávění editovat tracking.');
        }
    }

    /**
     * @param Form $form
     * @throws \Exception
     */
    public function editFormSucceeded(Form $form)
    {
        $values = $form->getValues();
        $trackingService = $this->trackingServiceRepository->getOneById($values->trackingService);

        if ($this->tracking) {
            $tracking = $this->tracking;
            $tracking->setName($values->name);
            $tracking->setIdentifier($values->identifier);
            $tracking->setTrackingService($trackingService);
            $tracking->setIsActive($values->isActive);
        } else {
            $tracking = new Tracking($trackingService, $values->name, $values->identifier, $values->isActive);
        }

        $this->entityManager->persist($tracking);

        $this->entityManager->flush();

        $this->onSuccess();
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/TrackingForm.latte');
        $template->render();
    }
}