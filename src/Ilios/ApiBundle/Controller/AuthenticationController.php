<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\AuthenticationBundle\Classes\SessionUser;
use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * Class AuthenticationController
 * Authentication uses 'user' as the primary key and
 * needs to encode passwords
 * so we have to handle that specially.
 */
class AuthenticationController extends ApiController
{
    /**
     * @var UserPasswordEncoder
     */
    protected $passwordEncoder;

    /**
     * Inject dependancies without overriding ApiControllers constructor
     * @required
     * @param UserPasswordEncoder $passwordEncoder
     */
    public function setup(UserPasswordEncoder $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @inheritdoc
     */
    public function getAction($version, $object, $userId)
    {
        if ('authentications' !== $object) {
            throw new \Exception('This controller should only be used for Authentication');
        }
        $manager = $this->getManager($object);
        $dto = $manager->findDTOBy(['user' => $userId]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $userId));
        }

        return $this->resultsToResponse([$dto], $this->getPluralResponseKey($object), Response::HTTP_OK);
    }

    /**
     * Along with taking input this also encodes the passwords
     * so they can be stored safely in the database
     * @inheritdoc
     */
    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $class = $manager->getClass() . '[]';

        $arr = $this->extractPostDataFromRequest($request, $object);

        $userManager = $this->getManager('users');
        $needingHashedPassword = array_filter($arr, function ($obj) {
            return (!empty($obj->password) && !empty($obj->user));
        });
        $userIdsForHashing = array_map(function ($obj) {
            return $obj->user;
        }, $needingHashedPassword);
        //prefetch all the users we need for hashing
        $users = [];
        /** @var UserInterface $user */
        foreach ($userManager->findBy(['id' => $userIdsForHashing]) as $user) {
            $users[$user->getId()] = $user;
        }


        $encodedPasswords = [];
        foreach ($arr as $obj) {
            if (!empty($obj->password) && !empty($obj->user)) {
                $user = $users[$obj->user];
                if ($user) {
                    $sessionUser = new SessionUser($user);
                    $encodedPassword = $this->passwordEncoder->encodePassword($sessionUser, $obj->password);
                    $encodedPasswords[$user->getId()] = $encodedPassword;
                }
            }
            //unset the password here in case it is NULL and didn't satisfy the above condition
            unset($obj->password);
        }
        $json = json_encode($arr);
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');
        $this->validateAndAuthorizeEntities($entities, 'create');

        $entitiesByUserId = [];
        /** @var AuthenticationInterface $authentication */
        foreach ($entities as $authentication) {
            $entitiesByUserId[$authentication->getUser()->getId()] = $authentication;
        }

        foreach ($encodedPasswords as $userId => $password) {
            $entitiesByUserId[$userId]->setPasswordBcrypt($password);
        }
        $entities = array_values($entitiesByUserId);

        foreach ($entities as $entity) {
            $manager->update($entity, false);
        }
        $manager->flush();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }

    /**
     * Along with taking user input, this also encodes passwords so they
     * can be stored safely in the database
     *
     * @inheritdoc
     */
    public function putAction($version, $object, $userId, Request $request)
    {
        $manager = $this->getManager($object);
        $entity = $manager->findOneBy(['user'=> $userId]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = 'edit';
        } else {
            $entity = $manager->create();
            $code = Response::HTTP_CREATED;
            $permission = 'create';
        }

        $authObject = $this->extractPutDataFromRequest($request, $object);
        $userManager = $this->getManager('users');

        if (!empty($authObject->password) && !empty($authObject->user)) {
            /** @var UserInterface $user */
            $user = $userManager->findOneBy(['id' => $authObject->user]);
            if ($user) {
                //set the password to null to reset the encoder
                //so we don't use the legacy one
                $entity->setPasswordSha256(null);
                $sessionUser = new SessionUser($user);
                $encodedPassword = $this->passwordEncoder->encodePassword($sessionUser, $authObject->password);
            }
        }
        unset($authObject->password);

        $json = json_encode($authObject);
        $serializer = $this->getSerializer();
        $serializer->deserialize($json, get_class($entity), 'json', ['object_to_populate' => $entity]);
        if (isset($encodedPassword)) {
            $entity->setPasswordBcrypt($encodedPassword);
        }
        $this->validateAndAuthorizeEntities([$entity], $permission);

        $manager->update($entity, true, false);

        return $this->createResponse($this->getSingularResponseKey($object), $entity, $code);
    }

    /**
     * Deletes a record by userId
     * @inheritdoc
     */
    public function deleteAction($version, $object, $userId)
    {
        $manager = $this->getManager($object);
        $entity = $manager->findOneBy(['user'=> $userId]);

        if (! $entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $userId));
        }

        if (! $this->authorizationChecker->isGranted('delete', $entity)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager->delete($entity);

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }
}
