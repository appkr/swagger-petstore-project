<?php

use Appkr\PetStore\ApiClient;
use Appkr\PetStore\ApiException;
use Appkr\PetStore\Configuration;
use Appkr\PetStore\Model\Pet;
use Appkr\PetStore\Service\PetApi;
use Illuminate\Support\Collection;

$apiConfig = new Configuration();
$apiConfig->addDefaultHeader(
    'Accept', 'application/json'
);
$apiClient = new ApiClient($apiConfig);
$petApi = new PetApi($apiClient);

Route::post('pet', function () use ($petApi) {
    $pet = new Pet([
        'category' => 'BullDog',
        'name' => 'Bully',
        'photosUrl' => 'http://image.news1.kr/system/photos/2016/11/28/2257769/article.jpg',
        'tags' => 'Dog',
        'status' => Pet::STATUS_AVAILABLE
    ]);

    return $petApi->addPet($pet);
});

Route::get('pet/findByStatus', function () use ($petApi) {
    $status = Pet::STATUS_AVAILABLE;
    $response = $petApi->findPetsByStatus($status ?? null);

    return (new Collection($response))
        ->forPage(1, 10)
        ->sortByDesc(function (Pet $pet, int $index) {
            return $pet->getId();
        })->map(function (Pet $pet) {
            return [
                'id' => $pet->getId(),
                'name' => $pet->getName(),
            ];
        })->values();
});

Route::get('pet/{petId}', function ($petId) use ($petApi) {
    $pet = $petApi->getPetById($petId);

    return Response::json([
        'id' => $pet->getId(),
        'name' => $pet->getName(),
    ]);
});

Route::put('pet/{petId}', function ($petId) use ($petApi) {
    return $petApi->updatePetWithForm(
        $petId, 'doooggie', Pet::STATUS_SOLD
    );
});

Route::get('/', function () {
    return view('welcome');
});
