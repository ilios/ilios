<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Doctrine\Common\Inflector\Inflector;

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

        self::loadInflectionRules();
    }

    /**
     * @inheritdoc
     */
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Exercise\HTMLPurifierBundle\ExerciseHTMLPurifierBundle(),
            new Ilios\CoreBundle\IliosCoreBundle(),
            new Ilios\WebBundle\IliosWebBundle(),
            new Ilios\AuthenticationBundle\IliosAuthenticationBundle(),
            new Ilios\CliBundle\IliosCliBundle(),
            new Ilios\ApiBundle\IliosApiBundle(),
            new Http\HttplugBundle\HttplugBundle(),
            new Happyr\GoogleAnalyticsBundle\HappyrGoogleAnalyticsBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Liip\FunctionalTestBundle\LiipFunctionalTestBundle();

            if ('dev' === $this->getEnvironment()) {
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__) . '/var/logs';
    }

    /**
     * @inheritdoc
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() .'.yml');
    }

    /**
     * Get the kernel based on environmental variables
     * This is used in index.php to select the correct environment
     * Thanks https://www.pmg.com/blog/symfony-no-app-dev/ for this idea
     *
     * @return AppKernel
     */
    public static function fromEnvironment()
    {
        $env = getenv('ILIOS_API_ENVIRONMENT') ?: 'prod';
        $debug = filter_var(getenv('ILIOS_API_DEBUG'), FILTER_VALIDATE_BOOLEAN);

        return new self($env, $debug);
    }

    /**
     * Words which are difficult to inflect need custom
     * rules.  We set these up here so they are consistent across the
     * entire application.
     */
    public static function loadInflectionRules()
    {
        Inflector::rules('singular', [
            'rules' => ['/^aamc(p)crses$/i' => 'aamc\1crs'],
            'uninflected' => ['aamcpcrs'],
        ]);
        Inflector::rules('plural', [
            'rules' => ['/^aamc(p)crs$/i' => 'aamc\1crses'],
            'uninflected' => ['aamcpcrses'],
        ]);
    }
}
