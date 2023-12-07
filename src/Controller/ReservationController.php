<?php

namespace App\Controller;


use App\Repository\ReservationRepository;
use App\Service\ApiHandlerResponse;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ReservationController extends AbstractController
{
    private ApiHandlerResponse $apiResponseHandler;
    private ReservationRepository $reservationRepository;

    public function __construct(ApiHandlerResponse $apiResponseHandler, ReservationRepository $reservationRepository)
    {
        $this->apiResponseHandler = $apiResponseHandler;
        $this->reservationRepository = $reservationRepository;
    }

    /**
     * @Route("/api/reservations", methods={"POST"})
     * @throws Exception
     */
    public function createReservation(Request $request): JsonResponse
    {
        return $this->apiResponseHandler->createOrUpdateReservation($request, null);
    }

    /**
     * @Route("/api/reservations/{id}", methods={"PUT"})
     * @throws Exception
     */
    public function updateReservation(Request $request, int $id): JsonResponse
    {
        return $this->apiResponseHandler->createOrUpdateReservation($request, $id);
    }

    /**
     * @Route("/api/reservations/{id}", methods={"DELETE"})
     */
    public function deleteReservations(int $id): JsonResponse
    {
        return $this->apiResponseHandler->deleteReservationById($this->reservationRepository, $id);
    }
}
