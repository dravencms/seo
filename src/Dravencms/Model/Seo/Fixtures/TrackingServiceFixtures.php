<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Seo\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Dravencms\Model\Seo\Entities\TrackingService;

class TrackingServiceFixtures extends AbstractFixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $ga = '<script>
  (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');

  ga(\'create\', \'%IDENTIFIER%\', \'auto\');
  ga(\'send\', \'pageview\');

</script>';
        $locale = new TrackingService('Google Analytics', $ga);
        $manager->persist($locale);

        $fbp = '<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version=\'2.0\';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,\'script\',\'//connect.facebook.net/en_US/fbevents.js\');

fbq(\'init\', \'%IDENTIFIER%\');
fbq(\'track\', "PageView");</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=%IDENTIFIER%&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->';

        $locale = new TrackingService('Facebook Pixel', $fbp, TrackingService::POSITION_HEADER);
        $manager->persist($locale);

        $gcro = '<script type="text/javascript">
  /* <![CDATA[ */
  var google_conversion_id = %IDENTIFIER%;
  var google_custom_params = window.google_tag_params;
  var google_remarketing_only = true;
  /* ]]> */
  </script>
  <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
  </script>
  <noscript>
  <div style="display:inline;">
  <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/%IDENTIFIER%/?value=0&amp;guid=ON&amp;script=0"/>
  </div>
  </noscript>';

        $locale = new TrackingService('Google Conversion Remarketing Only', $gcro);
        $manager->persist($locale);

        $hoz = '<script type="text/javascript">
//<![CDATA[
var _hwq = _hwq || [];
_hwq.push([\'setKey\', \'%IDENTIFIER%\']);
_hwq.push([\'setTopPos\', \'60\']);
_hwq.push([\'showWidget\', \'21\']);
(function() {
  var ho = document.createElement(\'script\'); ho.type = \'text/javascript\'; ho.async = true;
  ho.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.heureka.cz/direct/i/gjs.php?n=wdgt&sak=%IDENTIFIER%\';
  var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ho, s);
})();
//]]>  
</script>';

        $locale = new TrackingService('Heureka Overeno zakazniky', $hoz);
        $manager->persist($locale);

        $sklik = '<script type="text/javascript">
/* <![CDATA[ */
var seznam_retargeting_id = %IDENTIFIER%;
/* ]]> */
</script>
<script type="text/javascript" src="//c.imedia.cz/js/retargeting.js"></script>';

        $locale = new TrackingService('Sklik retargeting', $sklik);
        $manager->persist($locale);


        $hotjar = '<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:%IDENTIFIER%,hjsv:5};
        a=o.getElementsByTagName(\'head\')[0];
        r=o.createElement(\'script\');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,\'//static.hotjar.com/c/hotjar-\',\'.js?sv=\');
</script>';

        $locale = new TrackingService('Hotjar', $hotjar);
        $manager->persist($locale);

        $manager->flush();
    }
}