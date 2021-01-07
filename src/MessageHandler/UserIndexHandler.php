<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\UserIndexRequest;
use App\Repository\UserRepository;
use App\Service\Index\Users;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UserIndexHandler implements MessageHandlerInterface
{
    /**
     * @var Users
     */
    private $usersIndex;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(Users $index, UserRepository $userRepository)
    {
        $this->usersIndex = $index;
        $this->userRepository = $userRepository;
    }

    public function __invoke(UserIndexRequest $message)
    {
        $dtos = $this->userRepository->findDTOsBy(['id' => $message->getUserIds()]);
        $this->usersIndex->index($dtos);
    }
}
