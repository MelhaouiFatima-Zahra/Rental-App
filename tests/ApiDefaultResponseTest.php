<?php

use App\Entity\Car;
use App\Service\ApiDefaultResponse;
use App\Service\ApiResponse;
use App\Service\UserAccess;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiDefaultResponseTest extends TestCase
{
    // Mock objects
    private $serializer;
    private $apiResponse;
    private $userAccess;
    private $repository;

    protected function setUp(): void
    {
        // Créer des mocks pour les dépendances
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->apiResponse = $this->createMock(ApiResponse::class);
        $this->userAccess = $this->createMock(UserAccess::class);
        $this->repository = $this->createMock(ObjectRepository::class);
    }
//    /**
//     * @throws \Exception
//     */
//    public function testGetAll()
//    {
//
//        $apiDefaultResponse = new ApiDefaultResponse(
//            $this->serializer,
//            $this->apiResponse,
//            $this->userAccess
//        );
//
//        $fakeEntities = [
//            (new Car())
//                ->setBrand('Brand1')
//                ->setTransmission('transmission1')
//                ->setMileage(200.30)
//                ->setImage('image1')
//                ->setDailyRentalPrice(200)
//                ->setColor('color')
//                ->setFuelType('fuelType1')
//                ->setNumberOfSeats(5)
//                ->setYearOfManufacture(2000)
//                ->setAvailability(true),
//            (new Car())
//                ->setBrand('Brand2')
//                ->setTransmission('transmission2')
//                ->setMileage(200.30)
//                ->setImage('image2')
//                ->setDailyRentalPrice(200)
//                ->setColor('color')
//                ->setFuelType('fuelType2')
//                ->setNumberOfSeats(5)
//                ->setYearOfManufacture(2000)
//                ->setAvailability(true)
//        ];
//
//        $this->serializer->expects($this->any())
//            ->method('normalize')
//            ->willReturn([]);
//
//        // Mock findAll method in the repository
//        $this->repository->expects($this->once())
//            ->method('findAll')
//            ->willReturn($fakeEntities);
//
//        $response = $apiDefaultResponse->getAll($this->repository);
//
//        $this->assertInstanceOf(JsonResponse::class, $response);
//        $data = json_decode($response->getContent(), true);
//        $this->assertCount(2, $data);
//    }

//    public function testGetOneByIdSuccess()
//    {
//        $fakeCarId = 1;
//        $fakeEntity = (new Car())
//            ->setBrand('Brand1')
//            ->setTransmission('transmission1')
//            ->setMileage(200.30)
//            ->setImage('image1')
//            ->setDailyRentalPrice(200)
//            ->setColor('color')
//            ->setFuelType('fuelType1')
//            ->setNumberOfSeats(5)
//            ->setYearOfManufacture(2000)
//            ->setAvailability(true);
//        $this->repository->method('find')->willReturn($fakeEntity);
//        $serializer = $this->getMockBuilder(SerializerInterface::class)
//            ->addMethods(['normalize'])
//            ->getMock();
//        $serializer->method('normalize')->willReturn(['fake_data']);
//        $this->apiResponse->method('success')->willReturn(new JsonResponse(['status' => 'success']));
//        $apiDefaultResponse = new ApiDefaultResponse($serializer, $this->apiResponse, $this->userAccess);
//        $response = $apiDefaultResponse->getOneById($this->repository, $fakeCarId);
//        $this->assertInstanceOf(JsonResponse::class, $response);
//        $this->assertEquals(['status' => 'success'], json_decode($response->getContent(), true));
//    }
}
