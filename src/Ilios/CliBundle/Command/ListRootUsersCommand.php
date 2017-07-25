<?php
namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Entity\Manager\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Lists all users with root-level privileges.
 *
 * Class ListRootUsersCommand
 */
class ListRootUsersCommand extends Command
{
    /**
     * @var string
     */
    const COMMAND_NAME = 'ilios:maintenance:list-root-users';

    /**
     * @var UserManager
     */
    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Lists all users with root-level privileges.');
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->userManager->findDTOsBy(['root' => true]);

        if (empty($users)) {
            $output->writeln("No users with root-level privileges found.");
            return;
        }

        $rows = array_map(function ($dto) {
            return [
                $dto->id,
                $dto->firstName,
                $dto->lastName,
                $dto->email,
                $dto->phone,
                ($dto->enabled ? 'Yes' : 'No')
            ];
        }, $users);

        $table = new Table($output);
        $table
            ->setHeaders(array('Id', 'First', 'Last', 'Email', 'Phone Number', 'Enabled'))
            ->setRows($rows)
        ;
        $table->render();
    }
}
