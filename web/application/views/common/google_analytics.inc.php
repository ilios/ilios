<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Template to enable Google analytics
 *
 * Include this snippet in the JavaScript block in the <head> of your page template.
 *
 */
$trackingId = $this->config->item('ga_tracking_id');
$cookieDomain = $this->config->item('ga_cookie_domain');
if(!$cookieDomain){
    $cookieDomain = 'auto';
}
if($trackingId){
?>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', '<?php print $trackingId; ?>', '<?php print $cookieDomain; ?>');
      ga('send', 'pageview');

    </script>
<?php } ?>
