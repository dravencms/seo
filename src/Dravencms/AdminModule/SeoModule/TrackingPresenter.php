<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\SeoModule;


use Dravencms\AdminModule\Components\Seo\TrackingForm\TrackingFormFactory;
use Dravencms\AdminModule\Components\Seo\TrackingForm\TrackingForm;
use Dravencms\AdminModule\Components\Seo\TrackingGrid\TrackingGridFactory;
use Dravencms\AdminModule\Components\Seo\TrackingGrid\TrackingGrid;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Model\Seo\Entities\Tracking;
use Dravencms\Model\Seo\Repository\TrackingRepository;
use Dravencms\Model\Seo\Repository\TrackingServiceRepository;
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

    public function renderDefault(): void
    {
        $this->template->h1 = 'Tracking codes';
    }

    /**
     * @isAllowed(seo,trackingEdit)
     * @param $id
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit(int $id = null): void
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
     * @return TrackingForm
     */
    protected function createComponentFormTracking(): TrackingForm
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
     * @return TrackingGrid
     */
    public function createComponentTrackingGrid(): TrackingGrid
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
