<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Manager\UserManager;
use App\Message\UserIndexRequest;
use App\Service\Index\Users;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UserIndexHandler implements MessageHandlerInterface
{
    /**
     * @var Users
     */
    private $usersIndex;

    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(Users $index, UserManager $userManager)
    {
        $this->usersIndex = $index;
        $this->userManager = $userManager;
    }

    public function __invoke(UserIndexRequest $message)
    {
        $dtos = $this->userManager->findDTOsBy(['id' => $message->getUserIds()]);
        $this->usersIndex->index($dtos);
    }
}
