<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\SetupAuthenticationCommand;
use Ilios\CoreBundle\Entity\ApplicationConfigInterface;
use Ilios\CoreBundle\Entity\Manager\ApplicationConfigManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Mockery as m;

/**
 * Class SetupAuthenticationCommandTest
 */
class SetupAuthenticationCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:setup:authentication';

    protected $applicationConfigManager;
    protected $questionHelper;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->applicationConfigManager = m::mock(ApplicationConfigManager::class);

        $command = new SetupAuthenticationCommand(
            $this->applicationConfigManager
        );
        $kernel = $this->createKernel();
        $kernel->boot();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $commandInApp->getHelper('question');
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->applicationConfigManager);
        unset($this->questionHelper);
    }

    public function testFormAuth()
    {
        $authTypeConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('form')->mock();
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'authentication_type'])->once()->andReturn($authTypeConfig);
        $this->applicationConfigManager->shouldReceive('update')->with($authTypeConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('flush')->once();

        $this->commandTester->setInputs(['form']);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertContains('How will your users authentication to Ilios (defaults to form)?', $output);
        $this->assertContains('Authentication Setup Successfully!', $output);
    }

    public function testCasAuth()
    {
        $authTypeConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('cas')->mock();
        $urlConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('URL')->mock();
        $versionConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('3')->mock();
        $verifySslConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with(true)->mock();
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'authentication_type'])->once()->andReturn($authTypeConfig);
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'cas_authentication_server'])->once()->andReturn($urlConfig);
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'cas_authentication_version'])->once()->andReturn($versionConfig);
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'cas_authentication_verify_ssl'])->once()->andReturn($verifySslConfig);
        $this->applicationConfigManager->shouldReceive('update')->with($authTypeConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('update')->with($urlConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('update')->with($versionConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('update')->with($verifySslConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('flush')->once();
        $this->commandTester->setInputs(['cas', 'URL', '3']);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertContains('How will your users authentication to Ilios (defaults to form)?', $output);
        $this->assertContains('What is the url for you CAS server?', $output);
        $this->assertContains('What version of CAS do you want to use (defaults to 3)?', $output);
        $this->assertContains(
            "If necessary set the 'cas_authentication_verify_ssl' and " .
            "'cas_authentication_certificate_path' variables as well.",
            $output
        );
        $this->assertContains('Authentication Setup Successfully!', $output);
    }

    public function testLdapAuth()
    {
        $authTypeConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('ldap')->mock();
        $hostConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('MOON13')->mock();
        $portConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('PORT88')->mock();
        $bindTemplateConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('uid=?')->mock();
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'authentication_type'])->once()->andReturn($authTypeConfig);
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'ldap_authentication_host'])->once()->andReturn($hostConfig);
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'ldap_authentication_port'])->once()->andReturn($portConfig);
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'ldap_authentication_bind_template'])->once()->andReturn($bindTemplateConfig);
        $this->applicationConfigManager->shouldReceive('update')->with($authTypeConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('update')->with($hostConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('update')->with($portConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('update')->with($bindTemplateConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('flush')->once();
        $this->commandTester->setInputs(['ldap', 'MOON13', 'PORT88', 'uid=?']);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertContains('How will your users authentication to Ilios (defaults to form)?', $output);
        $this->assertContains('What is the url for you LDAP server?', $output);
        $this->assertContains('What is the port for you LDAP server? (defaults to 636)', $output);
        $this->assertContains(
            'What is the bind template for your LDAP users?  (defaults to uid=%s,cn=users,dc=domain,dc=edu)',
            $output
        );
        $this->assertContains('Authentication Setup Successfully!', $output);
    }

    public function testShibAuth()
    {
        $authTypeConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('shibboleth')->mock();
        $loginPathConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('/LOGIN')->mock();
        $logoutPathConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('/LOGOUT')->mock();
        $attributeConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('cn1')->mock();
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'authentication_type'])->once()->andReturn($authTypeConfig);
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'shibboleth_authentication_login_path'])->once()->andReturn($loginPathConfig);
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'shibboleth_authentication_logout_path'])->once()->andReturn($logoutPathConfig);
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'shibboleth_authentication_user_id_attribute'])->once()->andReturn($attributeConfig);
        $this->applicationConfigManager->shouldReceive('update')->with($authTypeConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('update')->with($loginPathConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('update')->with($logoutPathConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('update')->with($attributeConfig, false)->once();
        $this->applicationConfigManager->shouldReceive('flush')->once();
        $this->commandTester->setInputs(['shibboleth', '/LOGIN', '/LOGOUT', 'cn1']);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertContains('How will your users authentication to Ilios (defaults to form)?', $output);
        $this->assertContains('What is the login path for the service provider?', $output);
        $this->assertContains('What is the logout path for the service provider?', $output);
        $this->assertContains('What field contains the Ilios user id?', $output);
        $this->assertContains('Authentication Setup Successfully!', $output);
    }
}
