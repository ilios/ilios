<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\UserIndexRequest;
use App\Repository\UserRepository;
use App\Service\Index\Users;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserIndexHandler
{
    public function __construct(private Users $usersIndex, private UserRepository $userRepository)
    {
    }

    public function __invoke(UserIndexRequest $message): void
    {
        $dtos = $this->userRepository->findDTOsBy(['id' => $message->getUserIds()]);
        $this->usersIndex->index($dtos);
    }
}
