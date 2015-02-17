<?php

namespace Ilios\LegacyCIBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Ilios\LegacyCIBundle\DependencyInjection\IliosLegacyCIExtension;

class IliosLegacyCIExtensionTest extends AbstractExtensionTestCase
{

    protected function getContainerExtensions()
    {
        return array(
            new IliosLegacyCIExtension()
        );
    }

    public function testDefaultParametersSet()
    {
        $testKey = 'test_key';
        $this->load(array('session' => array('encryption_key' => $testKey)));
        $parameters = array(
            'ilios_legacy.utilities.class' => 'Ilios\LegacyCIBundle\Utilities',
            'ilios_legacy.session_handler.class' => 'Ilios\LegacyCIBundle\Session\Handler',
            'ilios_legacy.ci_session_entity.class' => 'Ilios\CoreBundle\Entity\CISession',
            'ilios_legacy.session_extractor.class' => 'Ilios\LegacyCIBundle\Session\Extractor',
            'ilios_legacy.session.cookie_name' => 'ci_session',
            'ilios_legacy.session.encryption_key' => $testKey,
            'ilios_legacy.session.encrypt_cookie' => false,
            'ilios_legacy.authenticate.token.class' => 'Ilios\LegacyCIBundle\Authentication\Token',
            'ilios_legacy.authenticate.provider.class' => 'Ilios\LegacyCIBundle\Authentication\Provider',
            'ilios_legacy.authenticate.listener.class' => 'Ilios\LegacyCIBundle\Authentication\Listener',
        );
        foreach ($parameters as $name => $value) {
            $this->assertContainerBuilderHasParameter($name, $value);
        }
    }

    public function testCustomrParametersSet()
    {
        $faker = \Faker\Factory::create();
        $values = array(
            'cookie_name' => $faker->text,
            'encryption_key' => $faker->sha256,
            'encrypt_cookie' => true
        );
        $this->load(array('session' => $values));
        foreach ($values as $name => $value) {
            $this->assertContainerBuilderHasParameter(
                'ilios_legacy.session.' . $name,
                $value
            );
        }
    }

    public function testServicesSet()
    {
        $this->load(array('session' => array('encryption_key' => 'foo')));
        $services = array(
            'ilios_legacy.utilities',
            'ilios_legacy.session_handler',
            'ilios_legacy.token',
            'ilios_legacy_ci.security.authentication.provider',
            'ilios_legacy_ci.security.authentication.listener'
        );
        foreach ($services as $service) {
            $this->assertContainerBuilderHasService($service);
        }
    }
}
