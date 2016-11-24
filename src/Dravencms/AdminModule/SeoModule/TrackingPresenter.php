<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\SeoModule;


use Dravencms\AdminModule\Components\Seo\TrackingFormFactory;
use Dravencms\AdminModule\Components\Seo\TrackingGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use App\Model\Seo\Entities\Tracking;
use App\Model\Seo\Repository\TrackingRepository;
use App\Model\Seo\Repository\TrackingServiceRepository;
/**
 * Description of TrackingPresenter
 *
 * @author Adam Schubert
 */
class TrackingPresenter extends SecuredPresenter
{
    /** @var TrackingRepository @inject */
    public $trackingRepository;

    /** @var TrackingServiceRepository @inject */
    public $trackingServiceRepository;

    /** @var TrackingGridFactory @inject */
    public $trackingGridFactory;

    /** @var TrackingFormFactory @inject */
    public $trackingFormFactory;

    /** @var null|Tracking */
    private $tracking = null;

    public function renderDefault()
    {
        $this->template->h1 = 'Tracking codes';
    }

    /**
     * @isAllowed(seo,trackingEdit)
     * @param $id
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit($id)
    {
        if ($id) {
            $tracking = $this->trackingRepository->getOneById($id);

            if (!$tracking) {
                $this->error();
            }

            $this->tracking = $tracking;
            $this->template->h1 = sprintf('Edit tracking code „%s“', $tracking->getName());
        } else {
            $this->template->h1 = 'New tracking code';
        }
    }

    /**
     * @return \AdminModule\Components\Seo\TrackingForm
     */
    protected function createComponentFormTracking()
    {
        $control = $this->trackingFormFactory->create($this->tracking);
        $control->onSuccess[] = function()
        {
            $this->flashMessage('Tracking has been successfully saved', 'alert-success');
            $this->redirect('Tracking:');
        };
        return $control;
    }

    /**
     * @return \AdminModule\Components\Seo\TrackingGrid
     */
    public function createComponentTrackingGrid()
    {
        $control = $this->trackingGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('Tracking has been successfully deleted', 'alert-success');
            $this->redirect('Tracking:');
        };
        return $control;
    }
}
