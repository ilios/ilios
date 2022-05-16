<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\SetupAuthenticationCommand;
use App\Entity\ApplicationConfigInterface;
use App\Repository\ApplicationConfigRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class SetupAuthenticationCommandTest
 * @group cli
 */
class SetupAuthenticationCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:setup-authentication';

    protected $applicationConfigRepository;
    protected $questionHelper;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->applicationConfigRepository = m::mock(ApplicationConfigRepository::class);

        $command = new SetupAuthenticationCommand(
            $this->applicationConfigRepository
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $commandInApp->getHelper('question');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->applicationConfigRepository);
        unset($this->questionHelper);
    }

    public function testFormAuth()
    {
        $authTypeConfig = m::mock(ApplicationConfigInterface::class)->shouldReceive('setValue')
            ->once()->with('form')->mock();
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'authentication_type'])->once()->andReturn($authTypeConfig);
        $this->applicationConfigRepository->shouldReceive('update')->with($authTypeConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('flush')->once();

        $this->commandTester->setInputs(['form']);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('How will your users authentication to Ilios (defaults to form)?', $output);
        $this->assertStringContainsString('Authentication Setup Successfully!', $output);
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
            ->once()->with('true')->mock();
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'authentication_type'])->once()->andReturn($authTypeConfig);
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'cas_authentication_server'])->once()->andReturn($urlConfig);
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'cas_authentication_version'])->once()->andReturn($versionConfig);
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'cas_authentication_verify_ssl'])->once()->andReturn($verifySslConfig);
        $this->applicationConfigRepository->shouldReceive('update')->with($authTypeConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('update')->with($urlConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('update')->with($versionConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('update')->with($verifySslConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('flush')->once();
        $this->commandTester->setInputs(['cas', 'URL', '3']);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('How will your users authentication to Ilios (defaults to form)?', $output);
        $this->assertStringContainsString('What is the url for you CAS server?', $output);
        $this->assertStringContainsString('What version of CAS do you want to use (defaults to 3)?', $output);
        $this->assertStringContainsString(
            "If necessary set the 'cas_authentication_verify_ssl' and " .
            "'cas_authentication_certificate_path' variables as well.",
            $output
        );
        $this->assertStringContainsString('Authentication Setup Successfully!', $output);
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
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'authentication_type'])->once()->andReturn($authTypeConfig);
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'ldap_authentication_host'])->once()->andReturn($hostConfig);
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'ldap_authentication_port'])->once()->andReturn($portConfig);
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'ldap_authentication_bind_template'])->once()->andReturn($bindTemplateConfig);
        $this->applicationConfigRepository->shouldReceive('update')->with($authTypeConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('update')->with($hostConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('update')->with($portConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('update')->with($bindTemplateConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('flush')->once();
        $this->commandTester->setInputs(['ldap', 'MOON13', 'PORT88', 'uid=?']);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('How will your users authentication to Ilios (defaults to form)?', $output);
        $this->assertStringContainsString('What is the url for you LDAP server?', $output);
        $this->assertStringContainsString('What is the port for you LDAP server? (defaults to 636)', $output);
        $this->assertStringContainsString(
            'What is the bind template for your LDAP users?  (defaults to uid=%s,cn=users,dc=domain,dc=edu)',
            $output
        );
        $this->assertStringContainsString('Authentication Setup Successfully!', $output);
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
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'authentication_type'])->once()->andReturn($authTypeConfig);
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'shibboleth_authentication_login_path'])->once()->andReturn($loginPathConfig);
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'shibboleth_authentication_logout_path'])->once()->andReturn($logoutPathConfig);
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'shibboleth_authentication_user_id_attribute'])->once()->andReturn($attributeConfig);
        $this->applicationConfigRepository->shouldReceive('update')->with($authTypeConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('update')->with($loginPathConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('update')->with($logoutPathConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('update')->with($attributeConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('flush')->once();
        $this->commandTester->setInputs(['shibboleth', '/LOGIN', '/LOGOUT', 'cn1']);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('How will your users authentication to Ilios (defaults to form)?', $output);
        $this->assertStringContainsString('What is the login path for the service provider?', $output);
        $this->assertStringContainsString('What is the logout path for the service provider?', $output);
        $this->assertStringContainsString('What field contains the Ilios user id?', $output);
        $this->assertStringContainsString('Authentication Setup Successfully!', $output);
    }
}
