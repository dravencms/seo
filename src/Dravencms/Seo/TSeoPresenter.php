<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 20.3.17
 * Time: 23:31
 */

namespace Dravencms\Seo;


use Dravencms\FrontModule\Components\Seo\Seo\Tracking\TrackingFactory;
use Dravencms\FrontModule\Components\Seo\Seo\Tracking\Tracking;

trait TSeoPresenter
{
    /** @var TrackingFactory */
    public $trackingFactory;

    /**
     * @param TrackingFactory $trackingFactory
     */
    public function injectSeoTrackingFactory(TrackingFactory $trackingFactory): void
    {
        $this->trackingFactory = $trackingFactory;
    }

    /**
     * @return Tracking
     */
    public function createComponentSeoTracking(): Tracking
    {
        return $this->trackingFactory->create();
    }
}
