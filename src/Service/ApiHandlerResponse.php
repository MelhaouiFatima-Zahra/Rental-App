<?php

namespace App\Service;


use App\Entity\Reservation;
use App\Repository\CarRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiHandlerResponse
{
    private SerializerInterface $serializer;
    private ApiResponse $apiResponse;
    private UserAccess $userAccess;
    private EntityManagerInterface $entityManager;
    private Security $security;
    private CarRepository $carRepository;
    private ApiRequest $apiRequest;
    private ReservationRepository $reservationRepository;

    public function __construct(
        SerializerInterface $serializer,
        ApiResponse $apiResponse,
        UserAccess $userAccess,
        EntityManagerInterface $entityManager,
        Security $security,
        CarRepository $carRepository,
        ApiRequest $apiRequest,
        ReservationRepository $reservationRepository
    ) {
        $this->serializer = $serializer;
        $this->apiResponse = $apiResponse;
        $this->userAccess = $userAccess;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->carRepository = $carRepository;
        $this->apiRequest = $apiRequest;
        $this->reservationRepository = $reservationRepository;
    }

    public function getUserReservation(ObjectRepository $repository, int $id): JsonResponse
    {
        if (null === $entity = $repository->find($id)) {
            return $this->apiResponse->notFound();
        }
        $entityId = $entity->getId();
        if ($entityId === null) {
            return $this->apiResponse->error('Entity ID is null');
        }
        if (!$this->userAccess->verifyUserId($entity->getId())) {
            return $this->apiResponse->error('You are not allowed');
        }
        try {
            $result = $this->serializer->normalize($entity, null, ['groups' => ['reservation:read']]);
            return $this->apiResponse->success($result);
        } catch (ExceptionInterface $e) {
            return $this->apiResponse->errorException($e);
        }
    }

    public function deleteReservationById(ObjectRepository $repository, int $id): JsonResponse
    {
        if (null === $entity = $repository->find($id)) {
            return $this->apiResponse->notFound();
        }
        if (!$this->userAccess->verifyUserId($entity->getUser()->getId())) {
            return $this->apiResponse->error('You are not allowed');
        }
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            return $this->apiResponse->successResponse('Reservation deleted successfully');
        } catch (ExceptionInterface $e) {
            return $this->apiResponse->errorException($e);
        }
    }

    /**
     * @throws Exception
     */
    public function createOrUpdateReservation(Request $request, $id = null): JsonResponse
    {
        $requestData = $this->apiRequest->processRequest($request, Reservation::class);

        if (isset($requestData['error'])) {
            return $requestData['error'];
        }

        $startDate = new \DateTime($requestData['data']['startDate']);
        $endDate = new \DateTime($requestData['data']['endDate']);
        $existingReservation = $id ? $this->reservationRepository->find($id) : null;

        if (!$this->carRepository->isCarAvailable($this->carRepository->find($requestData['data']['carId']), $startDate,
            $endDate, $existingReservation)) {
            return $this->apiResponse->error('Car is not available for the specified dates');
        }

        $reservation = $existingReservation ?? $requestData['entity'];
        $reservation->setStartDate($startDate)->setEndDate($endDate)->setCar($this->carRepository->find($requestData['data']['carId']))
            ->setUser($this->security->getUser())->setIsCanceled(false);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $this->apiResponse->successResponse('Reservation ' . ($id ? 'updated' : 'created') . ' successfully');
    }
}