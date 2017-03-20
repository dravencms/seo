<?php
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 20.3.17
 * Time: 23:31
 */

namespace Dravencms\Seo;


use Dravencms\FrontModule\Components\Seo\Seo\Tracking\TrackingFactory;

trait TSeoPresenter
{
    /** @var TrackingFactory */
    public $trackingFactory;

    /**
     * @param TrackingFactory $trackingFactory
     */
    public function injectSeoTrackingFactory(TrackingFactory $trackingFactory)
    {
        $this->trackingFactory = $trackingFactory;
    }

    /**
     * @return \Dravencms\FrontModule\Components\Seo\Seo\Tracking\Tracking
     */
    public function createComponentSeoTracking()
    {
        return $this->trackingFactory->create();
    }
}