<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\SeoModule;


use Dravencms\AdminModule\Components\Seo\RobotsForm\RobotsFormFactory;
use Dravencms\AdminModule\Components\Seo\RobotsForm\RobotsForm;
use Dravencms\AdminModule\Components\Seo\RobotsGrid\RobotsGridFactory;
use Dravencms\AdminModule\Components\Seo\RobotsGrid\RobotsGrid;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Model\Seo\Entities\Robots;
use Dravencms\Model\Seo\Repository\RobotsRepository;

/**
 * Description of RobotsPresenter
 *
 * @author Adam Schubert
 */
class RobotsPresenter extends SecuredPresenter
{
    /** @var RobotsRepository @inject */
    public $robotsRepository;

    /** @var RobotsGridFactory @inject */
    public $robotsGridFactory;

    /** @var RobotsFormFactory @inject */
    public $robotsFormFactory;

    /** @var null|Robots */
    private $robots = null;

    public function renderDefault(): void
    {
        $this->template->h1 = 'Robots.txt';
    }

    /**
     * @isAllowed(seo,robotsEdit)
     * @param $id
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit(int $id = null): void
    {
        if ($id) {
            $robots = $this->robotsRepository->getOneById($id);

            if (!$robots) {
                $this->error();
            }
            $this->robots = $robots;
            $this->template->h1 = sprintf('Edit robots.txt direcive „%s“', $robots->getName());
        } else {
            $this->template->h1 = 'New robots.txt directive';
        }
    }

    /**
     * @return RobotsForm
     */
    protected function createComponentFormRobots(): RobotsForm
    {
        $control = $this->robotsFormFactory->create($this->robots);
        $control->onSuccess[] = function()
        {
            $this->flashMessage('Rpbots has been successfully saved', 'alert-success');
            $this->redirect('Robots:');
        };
        return $control;
    }

    /**
     * @return RobotsGrid
     */
    public function createComponentGridRobots(): RobotsGrid
    {
        $control = $this->robotsGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('Robots has been successfully deleted', 'alert-success');
            $this->redirect('Robots:');
        };
        return $control;
    }
}
