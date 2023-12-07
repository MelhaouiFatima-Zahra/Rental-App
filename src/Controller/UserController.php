<?php

namespace App\Controller;


use App\Repository\UserRepository;
use App\Service\ApiHandlerResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private ApiHandlerResponse $apiResponseHandler;

    public function __construct(ApiHandlerResponse $apiResponseHandler, UserRepository $repository)
    {
        $this->userRepository = $repository;
        $this->apiResponseHandler = $apiResponseHandler;
    }

    /**
     * @Route("/api/users/{id}/reservations", methods={"GET"})
     */
    public function getAllReservationsByUserId(int $id): JsonResponse
    {
        return $this->apiResponseHandler->getUserReservation($this->userRepository, $id);
    }
}
