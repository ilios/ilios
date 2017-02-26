<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AuthenticationController
 * Authentication uses 'user' as the primary key and
 * needs to encode passwords
 * so we have to handle that specially.
 * @package Ilios\ApiBundle\Controller
 */
class AuthenticationController extends ApiController
{
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

        $json = $this->extractDataFromRequest($request, $object);
        $arr = json_decode($json);

        $userManager = $this->container->get('ilioscore.user.manager');
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
        $encoder = $this->container->get('security.password_encoder');
        foreach ($arr as $obj) {
            if (!empty($obj->password) && !empty($obj->user)) {
                $user = $users[$obj->user];
                if ($user) {
                    $encodedPassword = $encoder->encodePassword($user, $obj->password);
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

        $entitesByUserId = [];
        /** @var AuthenticationInterface $authentication */
        foreach ($entities as $authentication) {
            $entitesByUserId[$authentication->getUser()->getId()] = $authentication;
        }

        foreach ($encodedPasswords as $userId => $password) {
            $entitesByUserId[$userId]->setPasswordBcrypt($password);
        }
        $entities = array_values($entitesByUserId);

        foreach ($entities as $entity) {
            $manager->update($entity, false);
        }
        $manager->flushAndClear();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }

    /**
     * Along with taking input this also encodes passwords so they
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

        $json = $this->extractDataFromRequest($request, $object, $singleItem = true);
        $authObject = json_decode($json);

        $userManager = $this->container->get('ilioscore.user.manager');
        $encoder = $this->container->get('security.password_encoder');

        if (!empty($authObject->password) && !empty($authObject->user)) {
            $user = $userManager->findOneBy(['id' => $authObject->user]);
            if ($user) {
                //set the password to null to reset the encoder
                //so we don't use the lagacy one
                $entity->setPasswordSha256(null);
                $encodedPassword = $encoder->encodePassword($user, $authObject->password);
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

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $entity)) {
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
