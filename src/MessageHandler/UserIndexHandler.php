<?php
namespace App\MessageHandler;

use App\Entity\Manager\UserManager;
use App\Message\UserIndexRequest;
use App\Service\Index;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UserIndexHandler implements MessageHandlerInterface
{
    /**
     * @var Index
     */
    private $index;

    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(Index $index, UserManager $userManager)
    {
        $this->index = $index;
        $this->userManager = $userManager;
    }

    public function __invoke(UserIndexRequest $message)
    {
        $dtos = $this->userManager->findDTOsBy(['id' => $message->getUserIds()]);
        $this->index->indexUsers($dtos);
    }
}
