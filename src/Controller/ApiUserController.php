<?php
namespace App\Controller;

use App\DTO\UserDto;
use App\Entity\User;
use App\Exception\ValidatorException;
use App\Service\AddUsers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiUserController extends AbstractController
{

    /**
     * @Route("/api/users", methods={"GET"})
     */
    public function getAll(AddUsers $addUsers): JsonResponse
    {
        $usersArray = $addUsers->getUsers();
        return $this->json(['users' => $usersArray], Response::HTTP_OK);
    }

    /**
     * @Route("/api/users/{id}", methods={"GET"})
     */
    public function get(User $user, AddUsers $addUsers): JsonResponse
    {
        return $this->json($addUsers->getUser($user), Response::HTTP_OK);
    }

    /**
     * @Route("/api/users/add", methods={"POST"})
     */
    public function add(Request $request, AddUsers $addUsers) :JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->validateRequestWithFields($data, ['name', 'idGroup']);
        }  catch (BadRequestHttpException $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $user = $addUsers->addUser($data['name'], $data['idGroup']);
        } catch (ValidatorException $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return new JsonResponse(
            [
                'status' => 'User created!',
                'user' => UserDto::fromUser($user)
            ],
            Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/users/{id}", methods={"PUT"})
     */
    public function update(User $user, Request $request, AddUsers $addUsers) :JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = empty($data['name']) ? $user->getName() : $data['name'];

        $lastIdGroup = empty($user->getUserGroup()) ? null : $user->getUserGroup()->getId();
        $idGroup = empty($data['idGroup']) ? $lastIdGroup : $data['idGroup'];

        try {
            $user = $addUsers->editUser($user, $name, $idGroup);
        } catch (ValidatorException $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return new JsonResponse(
            [
                'status' => 'User edited!',
                'user' => UserDto::fromUser($user, true)
            ],
            Response::HTTP_OK);
    }

    /**
     * @Route("/api/users/{id}", methods={"DELETE"})
     */
    public function delete(User $user, AddUsers $addUsers) :JsonResponse
    {
        try {
            $addUsers->removeUser($user);
        } catch (ValidatorException $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return new JsonResponse(
            [
                'status' => 'User deleted!'
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