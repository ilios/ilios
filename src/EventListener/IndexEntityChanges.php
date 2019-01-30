<?php
namespace App\EventListener;

use App\Entity\User;
use App\Entity\UserInterface;
use App\Service\Search;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Doctrine event listener.
 * Listen for every change to an entity and index them.
 */
class IndexEntityChanges
{
    /**
     * @var Search
     */
    private $search;

    public function __construct(Search $search)
    {
        $this->search = $search;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof User) {
            $this->indexUser($entity);
        }
    }
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof User) {
            $this->indexUser($entity);
        }
    }

    /**
     * We have to do this work in preRemove because in postRemove we no longer
     * have access to the entity ID
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof User) {
            $this->search->delete([
                'index' => Search::PRIVATE_INDEX,
                'type' => User::class,
                'id' => $entity->getId(),
            ]);
        }
    }

    protected function indexUser(UserInterface $user)
    {
        $data = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'middleName' => $user->getMiddleName(),
            'email' => $user->getEmail(),
            'campusId' => $user->getCampusId(),
            'username' => null,
        ];
        if ($authentication = $user->getAuthentication()) {
            $data['username'] = $authentication->getUsername();
        }
        $this->search->index([
            'index' => Search::PRIVATE_INDEX,
            'type' => User::class,
            'id' => $data['id'],
            'body' => $data
        ]);
    }

}
