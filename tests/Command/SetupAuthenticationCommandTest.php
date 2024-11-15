<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\SetupAuthenticationCommand;
use App\Entity\ApplicationConfigInterface;
use App\Repository\ApplicationConfigRepository;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class SetupAuthenticationCommandTest
 */
#[Group('cli')]
class SetupAuthenticationCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $applicationConfigRepository;
    protected CommandTester $commandTester;

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
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->applicationConfigRepository);
        unset($this->commandTester);
    }

    public function testFormAuth(): void
    {
        $authTypeConfig = m::mock(ApplicationConfigInterface::class);
        $authTypeConfig->shouldReceive('setValue')->once()->with('form');
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'authentication_type'])->once()->andReturn($authTypeConfig);
        $this->applicationConfigRepository->shouldReceive('update')->with($authTypeConfig, false)->once();
        $this->applicationConfigRepository->shouldReceive('flush')->once();

        $this->commandTester->setInputs(['form']);
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('How will your users authentication to Ilios (defaults to form)?', $output);
        $this->assertStringContainsString('Authentication Setup Successfully!', $output);
    }

    public function testCasAuth(): void
    {
        $authTypeConfig = m::mock(ApplicationConfigInterface::class);
        $authTypeConfig->shouldReceive('setValue')->once()->with('cas');
        $urlConfig = m::mock(ApplicationConfigInterface::class);
        $urlConfig->shouldReceive('setValue')->once()->with('URL');
        $versionConfig = m::mock(ApplicationConfigInterface::class);
        $versionConfig->shouldReceive('setValue')->once()->with('3');
        $verifySslConfig = m::mock(ApplicationConfigInterface::class);
        $verifySslConfig->shouldReceive('setValue')->once()->with('true');
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
        $this->commandTester->execute([]);

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

    public function testLdapAuth(): void
    {
        $authTypeConfig = m::mock(ApplicationConfigInterface::class);
        $authTypeConfig->shouldReceive('setValue')->once()->with('ldap');
        $hostConfig = m::mock(ApplicationConfigInterface::class);
        $hostConfig->shouldReceive('setValue')->once()->with('MOON13');
        $portConfig = m::mock(ApplicationConfigInterface::class);
        $portConfig->shouldReceive('setValue')->once()->with('PORT88');
        $bindTemplateConfig = m::mock(ApplicationConfigInterface::class);
        $bindTemplateConfig->shouldReceive('setValue')->once()->with('uid=?');
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
        $this->commandTester->execute([]);

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

    public function testShibAuth(): void
    {
        $authTypeConfig = m::mock(ApplicationConfigInterface::class);
        $authTypeConfig->shouldReceive('setValue')->once()->with('shibboleth');
        $loginPathConfig = m::mock(ApplicationConfigInterface::class);
        $loginPathConfig->shouldReceive('setValue')->once()->with('/LOGIN');
        $logoutPathConfig = m::mock(ApplicationConfigInterface::class);
        $logoutPathConfig->shouldReceive('setValue')->once()->with('/LOGOUT');
        $attributeConfig = m::mock(ApplicationConfigInterface::class);
        $attributeConfig->shouldReceive('setValue')->once()->with('cn1');
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
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('How will your users authentication to Ilios (defaults to form)?', $output);
        $this->assertStringContainsString('What is the login path for the service provider?', $output);
        $this->assertStringContainsString('What is the logout path for the service provider?', $output);
        $this->assertStringContainsString('What field contains the Ilios user id?', $output);
        $this->assertStringContainsString('Authentication Setup Successfully!', $output);
    }
}
