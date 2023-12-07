<?php

namespace App\Tests;


use App\Entity\Car;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\CarRepository;
use App\Repository\ReservationRepository;
use App\Service\ApiHandlerResponse;
use App\Service\ApiRequest;
use App\Service\ApiResponse;
use App\Service\UserAccess;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class ApiHandlerResponseTest extends TestCase
{
    private $repository;
    private $entityManager;
    private $userAccess;
    private $apiResponse;
    private $security;
    private $carRepository;
    private $apiRequest;
    private $reservationRepository;
    private $serializer;

    protected function setUp(): void
    {
        // Create mocks for dependencies
        $this->repository = $this->createMock(ObjectRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userAccess = $this->createMock(UserAccess::class);
        $this->apiResponse = $this->createMock(ApiResponse::class);
        $this->security = $this->createMock(Security::class);
        $this->carRepository = $this->createMock(CarRepository::class);
        $this->apiRequest = $this->createMock(ApiRequest::class);
        $this->reservationRepository = $this->createMock(ReservationRepository::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testDeleteReservationByIdSuccess()
    {
        $apiHandler = new ApiHandlerResponse(
            $this->serializer,
            $this->apiResponse,
            $this->userAccess,
            $this->entityManager,
            $this->security,
            $this->carRepository,
            $this->apiRequest,
            $this->reservationRepository
        );

        // Mock entity and user access
        $entityId = 1;
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $entity = $this->createMock(Reservation::class);
        $entity->method('getUser')->willReturn($user);

        $this->repository->expects($this->once())
            ->method('find')
            ->with($entityId)
            ->willReturn($entity);

        $this->userAccess->expects($this->once())
            ->method('verifyUserId')
            ->with(1)
            ->willReturn(true);

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($entity);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->apiResponse->expects($this->once())
            ->method('successResponse')
            ->with('Reservation deleted successfully')
            ->willReturn(new JsonResponse(['status' => 'success']));

        $response = $apiHandler->deleteReservationById($this->repository, $entityId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(['status' => 'success'], json_decode($response->getContent(), true));
    }

    /**
     * @throws Exception
     */
    public function testCreateOrUpdateReservationSuccess()
    {
        $apiHandler = new ApiHandlerResponse(
            $this->serializer,
            $this->apiResponse,
            $this->userAccess,
            $this->entityManager,
            $this->security,
            $this->carRepository,
            $this->apiRequest,
            $this->reservationRepository
        );

        $this->apiRequest->expects($this->once())
            ->method('processRequest')
            ->willReturn([
                'data' => ['startDate' => '2023-01-01', 'endDate' => '2023-01-05', 'carId' => 1],
                'entity' => $this->createMock(Reservation::class)
            ]);

        $this->carRepository->expects($this->exactly(2))
            ->method('find')
            ->withConsecutive([1], [1])
            ->willReturn($this->createMock(Car::class));

        $this->carRepository->expects($this->once())
            ->method('isCarAvailable')
            ->willReturn(true);

        $this->entityManager->expects($this->once())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        $id= null ;
        $expectedMessage = 'Reservation ' . ($id ? 'updated' : 'created') . ' successfully';
        $this->apiResponse->expects($this->once())
            ->method('successResponse')
            ->with($expectedMessage)
            ->willReturn(new JsonResponse(['status' => 'success']));

        $response = $apiHandler->createOrUpdateReservation($this->createMock(Request::class),$id );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(['status' => 'success'], json_decode($response->getContent(), true));
    }

}