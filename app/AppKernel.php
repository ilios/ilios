<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel
 */
class AppKernel extends Kernel
{
    /**
     * @inheritdoc
     */
    public function __construct($environment, $debug)
    {
        // Force a UTC timezone on everyone
        date_default_timezone_set('UTC');
        parent::__construct($environment, $debug);
    }

    /**
     * @inheritdoc
     */
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\AopBundle\JMSAopBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Matthias\SymfonyConsoleForm\Bundle\SymfonyConsoleFormBundle(),
            new Exercise\HTMLPurifierBundle\ExerciseHTMLPurifierBundle(),
            new Ilios\CoreBundle\IliosCoreBundle(),
            new Ilios\WebBundle\IliosWebBundle(),
            new Ilios\AuthenticationBundle\IliosAuthenticationBundle(),
            new Ilios\CliBundle\IliosCliBundle(),
            new Snc\RedisBundle\SncRedisBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new IC\Bundle\Base\TestBundle\ICBaseTestBundle();
            $bundles[] = new Liip\FunctionalTestBundle\LiipFunctionalTestBundle();

        }

        return $bundles;
    }

    /**
     * @inheritdoc
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * @inheritdoc
     */
    protected function getContainerBaseClass()
    {
        // Register an alternative container for testing which allows us to mock services.
        if ('test' == $this->environment) {
            return '\PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer';
        }

        return parent::getContainerBaseClass();
    }
}
