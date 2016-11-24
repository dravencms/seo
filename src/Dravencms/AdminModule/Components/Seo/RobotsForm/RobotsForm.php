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

namespace Dravencms\AdminModule\Components\Seo\RobotsForm;

use Dravencms\Components\BaseFormFactory;
use Dravencms\Model\Seo\Entities\Robots;
use Dravencms\Model\Seo\Repository\RobotsRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

/**
 * Description of RobotsForm
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class RobotsForm extends Control
{
    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var EntityManager */
    private $entityManager;

    /** @var RobotsRepository */
    private $robotsRepository;

    /** @var Robots */
    private $robots = null;

    /** @var array */
    public $onSuccess = [];

    /**
     * RobotsForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param EntityManager $entityManager
     * @param RobotsRepository $robotsRepository
     * @param Robots|null $robots
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        EntityManager $entityManager,
        RobotsRepository $robotsRepository,
        Robots $robots = null
    ) {
        parent::__construct();

        $this->robots = $robots;

        $this->baseFormFactory = $baseFormFactory;
        $this->entityManager = $entityManager;
        $this->robotsRepository = $robotsRepository;


        if ($this->robots) {
            $defaults = [
                'name' => $this->robots->getName(),
                'path' => $this->robots->getPath(),
                'action' => $this->robots->getAction(),
                'isActive' => $this->robots->isActive()
            ];

        } else {
            $defaults = [
                'isActive' => true
            ];
        }

        $this['form']->setDefaults($defaults);
    }

    /**
     * @return \Dravencms\Components\BaseForm
     */
    protected function createComponentForm()
    {
        $form = $this->baseFormFactory->create();

        $form->addText('name')
            ->setRequired('Please enter directive name.')
            ->addRule(Form::MAX_LENGTH, 'Directive name name is too long.', 255);

        $form->addText('path')
            ->setRequired('Please enter path.')
            ->addRule(Form::MAX_LENGTH, 'Directive path is too long.', 255);

        $form->addSelect('action', null, [Robots::ACTION_ALLOW => 'Allow', Robots::ACTION_DISALLOW => 'DisAllow'])
            ->setRequired('Please enter directive type.');

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
        if (!$this->robotsRepository->isNameFree($values->name, $this->robots)) {
            $form->addError('Tento název je již zabrán.');
        }

        if (!$this->robotsRepository->isPathFree($values->path, $this->robots)) {
            $form->addError('Tento kod je již zabrán.');
        }

        if (!$this->presenter->isAllowed('seo', 'robotsEdit')) {
            $form->addError('Nemáte oprávění editovat robots.');
        }
    }

    /**
     * @param Form $form
     * @throws \Exception
     */
    public function editFormSucceeded(Form $form)
    {
        $values = $form->getValues();


        if ($this->robots) {
            $robots = $this->robots;
            $robots->setName($values->name);
            $robots->setPath($values->path);
            $robots->setAction($values->action);
            $robots->setIsActive($values->isActive);
        } else {
            $robots = new Robots($values->name, $values->path, $values->isActive, $values->action);
        }


        $this->entityManager->persist($robots);

        $this->entityManager->flush();

        $this->onSuccess();
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/RobotsForm.latte');
        $template->render();
    }
}