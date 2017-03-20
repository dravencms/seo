<?php
namespace Dravencms\FrontModule\Components\Seo\Seo\Tracking;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Model\Seo\Entities\TrackingService;
use Dravencms\Model\Seo\Repository\TrackingRepository;

/**
 * Class Tracking
 * @package FrontModule\Components\Seo
 */
class Tracking extends BaseControl
{
    /** @var TrackingRepository */
    private $trackingRepository;

    public function __construct(TrackingRepository $trackingRepository)
    {
        parent::__construct();
        $this->trackingRepository = $trackingRepository;
    }

    public function renderHeader()
    {
        $template = $this->template;
        $template->trackings = $this->trackingRepository->getByPosition(TrackingService::POSITION_HEADER);
        $template->setFile(__DIR__.'/tracking.latte');
        $template->render();
    }

    public function renderFooter()
    {
        $template = $this->template;
        $template->trackings = $this->trackingRepository->getByPosition(TrackingService::POSITION_BODY_BOTTOM);
        $template->setFile(__DIR__.'/tracking.latte');
        $template->render();
    }
}
