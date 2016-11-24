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

namespace Dravencms\AdminModule\Components\Seo\TrackingServiceForm;

use Dravencms\Components\BaseFormFactory;
use Dravencms\Model\Seo\Entities\TrackingService;
use Dravencms\Model\Seo\Repository\TrackingServiceRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

/**
 * Description of TrackingServiceForm
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class TrackingServiceForm extends Control
{
    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var EntityManager */
    private $entityManager;

    /** @var TrackingServiceRepository */
    private $trackingServiceRepository;

    /** @var TrackingService */
    private $trackingService = null;

    /** @var array */
    public $onSuccess = [];

    /**
     * RobotsForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param EntityManager $entityManager
     * @param TrackingServiceRepository $trackingServiceRepository
     * @param TrackingService|null $trackingService
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        EntityManager $entityManager,
        TrackingServiceRepository $trackingServiceRepository,
        TrackingService $trackingService = null
    ) {
        parent::__construct();

        $this->trackingService = $trackingService;

        $this->baseFormFactory = $baseFormFactory;
        $this->entityManager = $entityManager;
        $this->trackingServiceRepository = $trackingServiceRepository;


        if ($this->trackingService) {
            $this['form']->setDefaults([
                'name' => $this->trackingService->getName(),
                'code' => $this->trackingService->getCode(),
                'position' => $this->trackingService->getPosition()
            ]);
        }
    }

    /**
     * @return \Dravencms\Components\BaseForm
     */
    protected function createComponentForm()
    {
        $form = $this->baseFormFactory->create();

        $form->addText('name')
            ->setRequired('Please enter service name.')
            ->addRule(Form::MAX_LENGTH, 'Service name name is too long.', 255);

        $form->addTextArea('code')
            ->setRequired('Please enter service code.');

        $form->addSelect('position', null, [TrackingService::POSITION_BODY_BOTTOM => 'Body Bottom', TrackingService::POSITION_HEADER => 'Header'])
            ->setRequired('Please enter code position.');

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
        if (!$this->trackingServiceRepository->isNameFree($values->name, $this->trackingService)) {
            $form->addError('Tento název je již zabrán.');
        }

        if (strpos($values->code, '<script') === false)
        {
            $form->addError('V code chybi <script> tag.');
        }

        if (strpos($values->code, '</script>') === false )
        {
            $form->addError('V code chybi </script> tag.');
        }

        if (strpos($values->code, '%IDENTIFIER%') === false)
        {
            $form->addError('V code chybi %IDENTIFIER%.');
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


        if ($this->trackingService) {
            $trackingService = $this->trackingService;
            $trackingService->setName($values->name);
            $trackingService->setCode($values->code);
            $trackingService->setPosition($values->position);
        } else {
            $trackingService = new TrackingService($values->name, $values->code, $values->position);
        }

        $this->entityManager->persist($trackingService);

        $this->entityManager->flush();

        $this->onSuccess();
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/TrackingServiceForm.latte');
        $template->render();
    }
}