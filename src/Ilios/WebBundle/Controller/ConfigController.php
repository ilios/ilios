<?php

namespace Ilios\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use phpCAS;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ConfigController
 * @package Ilios\WebBundle\Controller
 */
class ConfigController extends Controller
{
    public function indexAction(Request $request)
    {
        $configuration = [];
        $authenticationType = $this->container->getParameter('ilios_authentication.type');

        $configuration['type'] = $authenticationType;
        if ($authenticationType == 'shibboleth') {
            $loginPath = $this->container->getParameter('ilios_authentication.shibboleth.login_path');
            $url = $request->getSchemeAndHttpHost();
            $configuration['loginUrl'] = $url . $loginPath;
        }
        if ($authenticationType === 'cas') {
            $cas = $this->container->get('ilios_authentication.cas.manager');

            $configuration['casLoginUrl'] = $cas->getLoginUrl();
        }
        $configuration['locale'] = $this->container->getParameter('locale');

        $ldapUrl = $this->container->getParameter('ilios_core.ldap.url');
        if (!empty($ldapUrl)) {
            $configuration['userSearchType'] = 'ldap';
        } else {
            $configuration['userSearchType'] = 'local';
        }
        $configuration['maxUploadSize'] = $this->fileUploadMaxSize();


        return new JsonResponse(array('config' => $configuration));
    }

    /**
     * Get the maximum configured upload size for files
     * combines information from post_max_size and upload_max_size to get the size in bytes
     * Modified from http://stackoverflow.com/a/25370978/796999
     * @return int
     */
    protected function fileUploadMaxSize()
    {
        static $maxSize = -1;

        if ($maxSize < 0) {
            // Start with post_max_size.
            $maxSize = $this->parseSize(ini_get('post_max_size'));

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
            if ($uploadMax > 0 && $uploadMax < $maxSize) {
                $maxSize = $uploadMax;
            }
        }
        return $maxSize;
    }

    /**
     * Convert human readable strings about size into bytes
     * Modified from http://stackoverflow.com/a/25370978/796999
     *
     * @param string $size
     * @return float
     */
    protected function parseSize($size)
    {
        //Remove the non-unit characters from the size.
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        //Remove the non-numeric characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            // Find the position of the unit in the ordered string
            // which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }
}
