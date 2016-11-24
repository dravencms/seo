<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Dravencms\AdminModule\SeoModule;

use Dravencms\AdminModule\Components\Seo\TrackingServiceForm\TrackingServiceFormFactory;
use Dravencms\AdminModule\Components\Seo\TrackingServiceGrid\TrackingServiceGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Model\Seo\Entities\TrackingService;
use Dravencms\Model\Seo\Repository\TrackingRepository;
use Dravencms\Model\Seo\Repository\TrackingServiceRepository;

/**
 * Description of TrackingServicePresenter
 *
 * @author Adam Schubert
 */
class TrackingServicePresenter extends SecuredPresenter
{

    /** @var TrackingRepository @inject */
    public $trackingRepository;

    /** @var TrackingServiceRepository @inject */
    public $trackingServiceRepository;

    /** @var TrackingServiceGridFactory @inject */
    public $trackingServiceGridFactory;

    /** @var TrackingServiceFormFactory @inject */
    public $trackingServiceFormFactory;

    /** @var TrackingService|null */
    private $trackingService = null;

    public function renderDefault()
    {
        $this->template->h1 = 'Tracking service codes';
    }

    /**
     * @isAllowed(seo,trackingEdit)
     * @param integer|null $id
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit($id = null)
    {
        if ($id) {
            $trackingService = $this->trackingServiceRepository->getOneById($id);

            if (!$trackingService) {
                $this->error();
            }

            $this->trackingService = $trackingService;
            $this->template->h1 = sprintf('Edit tracking service „%s“', $trackingService->getName());
        } else {
            $this->template->h1 = 'New tracking code';
        }
    }

    /**
     * @return \AdminModule\Components\Seo\TrackingServiceForm
     */
    protected function createComponentFormTrackingService()
    {
        $control = $this->trackingServiceFormFactory->create($this->trackingService);
        $control->onSuccess[] = function()
        {
            $this->flashMessage('Tracking service has been saved.', 'alert-success');
            $this->redirect('TrackingService:');
        };
        return $control;
    }

    /**
     * @return \AdminModule\Components\Seo\TrackingServiceGrid
     */
    protected function createComponentGridTrackingService()
    {
        $control = $this->trackingServiceGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('Tracking service has been successfully deleted', 'alert-success');
            $this->redirect('TrackingService:');
        };
        return $control;
    }
}
