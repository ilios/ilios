<?php
namespace Ilios\AuthenticationBundle\Shibboleth;

use Symfony\Component\HttpFoundation\Request;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface;

class Authentication
{
    protected $request;
    protected $authManager;
    public function __construct(Request $request, AuthenticationManagerInterface $authManager)
    {
        $this->request = $request;
        $this->authManager = $authManager;
    }
    public function isAuthenticated()
    {
        return (bool) $this->request->server->get('Shib-Application-ID');
    }
    public function getUser()
    {

        if ($this->isAuthenticated()) {
            $eppn = $this->request->server->get('eppn');
            if (!$eppn) {
                throw new \Exception(
                    "No 'eepn' found for authenticated user.  Dump of SERVER global: " .
                    var_export($_SERVER, true)
                );
            }
            $authEntity = $this->authManager->findAuthenticationBy(array('eppn' => $eppn));
            if ($authEntity) {
                return $authEntity->getUser();
            }
        }

        return null;
    }
}
