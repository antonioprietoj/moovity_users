<?php
namespace App\Controller;

use App\DTO\UserGroupDto;
use App\Entity\UserGroup;
use App\Exception\ValidatorException;
use App\Service\AddUserGroups;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiUserGroupController extends AbstractController
{
    /**
     * @Route("/api/groups", methods={"GET"})
     */
    public function getAll(AddUserGroups $addUserGroups): JsonResponse
    {
        $usersGroupsArray = $addUserGroups->getUserGroups();
        return $this->json(['groups' => $usersGroupsArray], Response::HTTP_OK);
    }

    /**
     * @Route("/api/groups/{id}", methods={"GET"})
     */
    public function get(UserGroup $group, AddUserGroups $addUserGroups): JsonResponse
    {
        return $this->json($addUserGroups->getUserGroup($group), Response::HTTP_OK);
    }

    /**
     * @Route("/api/groups/add", methods={"POST"})
     */
    public function add(Request $request, AddUserGroups $addUserGroups) :JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->validateRequestWithFields($data, ['name']);
        }  catch (BadRequestHttpException $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $userGroup = $addUserGroups->addUserGroup($data['name']);
        } catch (ValidatorException $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return new JsonResponse(
            [
                'status' => 'Group created!',
                'group' => UserGroupDto::fromUserGroup($userGroup)
            ],
            Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/groups/{id}", methods={"PUT"})
     */
    public function update(UserGroup $group, Request $request, AddUserGroups $addUserGroups) :JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = empty($data['name']) ? $group->getName() : $data['name'];

        try {
            $group = $addUserGroups->editUserGroup($group, $name);
        } catch (ValidatorException $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return new JsonResponse(
            [
                'status' => 'Group edited!',
                'group' => UserGroupDto::fromUserGroup($group, true)
            ],
            Response::HTTP_OK);
    }

    /**
     * @Route("/api/groups/{id}", methods={"DELETE"})
     */
    public function delete(UserGroup $userGroups, AddUserGroups $addUserGroups) :JsonResponse
    {
        try {
            $addUserGroups->removeUserGroup($userGroups);
        } catch (ValidatorException $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return new JsonResponse(
            [
                'status' => 'Group deleted!'
            ],
            Response::HTTP_OK
        );
    }

    protected function validateRequestWithFields($data, array $fields){
        $errormsg = "The field '%s' is required";

        foreach ($fields as $field){
            if (empty($data[$field])){
                throw new BadRequestHttpException(sprintf($errormsg,$field));
            }
        }
    }
}