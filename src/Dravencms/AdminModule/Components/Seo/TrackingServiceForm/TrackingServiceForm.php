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

namespace Dravencms\AdminModule\Components\Seo\TrackingServiceForm;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Seo\Entities\TrackingService;
use Dravencms\Model\Seo\Repository\TrackingServiceRepository;
use Dravencms\Database\EntityManager;
use Dravencms\Components\BaseForm\Form;
use Nette\Security\User;

/**
 * Description of TrackingServiceForm
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class TrackingServiceForm extends BaseControl
{
    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var EntityManager */
    private $entityManager;

    /** @var TrackingServiceRepository */
    private $trackingServiceRepository;

    /** @var User */
    private $user;
    
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
        User $user,
        TrackingServiceRepository $trackingServiceRepository,
        ?TrackingService $trackingService = null
    ) {
        $this->trackingService = $trackingService;
        $this->user = $user;
        $this->baseFormFactory = $baseFormFactory;
        $this->entityManager = $entityManager;
        $this->trackingServiceRepository = $trackingServiceRepository;

    }

    /**
     * @return Form
     */
    protected function createComponentForm(): Form
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

        if ($this->trackingService) {
            $form->setDefaults([
                'name' => $this->trackingService->getName(),
                'code' => $this->trackingService->getCode(),
                'position' => $this->trackingService->getPosition(),
            ]);
        }

        return $form;
    }

    /**
     * @param Form $form
     */
    public function editFormValidate(Form $form): void
    {
        $values = $form->getValues();
        if (!$this->trackingServiceRepository->isNameFree($values->name, $this->trackingService)) {
            $form->addError('Tento název je již zabrán.');
        }

        $hasScript = preg_match('~<script\b[^>]*>.*?</script>~is', $values->code) === 1;
        $hasNoscript = preg_match('~<noscript\b[^>]*>.*?</noscript>~is', $values->code) === 1;

        if (!$hasScript && !$hasNoscript) {
            $form->addError('Kód musí obsahovat kompletní <script> nebo <noscript> tag.');
        }

        if (strpos($values->code, '%IDENTIFIER%') === false)
        {
            $form->addError('V code chybi %IDENTIFIER%.');
        }

        if (!$this->user->isAllowed('seo', 'trackingEdit')) {
            $form->addError('Nemáte oprávění editovat tracking.');
        }
    }

    /**
     * @param Form $form
     * @throws \Exception
     */
    public function editFormSucceeded(Form $form): void
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

    public function render(): void
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/TrackingServiceForm.latte');
        $template->render();
    }
}
