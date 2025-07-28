<?php

use App\Controllers\AttractionController;
use App\Controllers\CityController;
use App\Controllers\RatingController;
use App\Controllers\TravelerController;
use App\Exceptions\NotFoundException;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$app = AppFactory::create();

// Middleware для парсинга JSON
$app->addBodyParsingMiddleware();

// Обработка ошибок
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Кастомный обработчик 404
$errorMiddleware->setErrorHandler(
    NotFoundException::class,
    function (Request $request, Throwable $exception, bool $displayErrorDetails) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(['error' => $exception->getMessage()]));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
);

// Регистрируем контроллеры
$container = $app->getContainer();
$container['CityController'] = function ($container) { return new CityController(); };
$container['TravelerController'] = function ($container) { return new TravelerController(); };
$container['AttractionController'] = function ($container) { return new AttractionController(); };
$container['RatingController'] = function ($container) { return new RatingController(); };

// Routes: Городов
$app->get('/cities', [CityController::class, 'index']);
$app->post('/cities', [CityController::class, 'store']);
$app->get('/cities/{id}', [CityController::class, 'show']);
$app->put('/cities/{id}', [CityController::class, 'update']);
$app->delete('/cities/{id}', [CityController::class, 'destroy']);

// Routes: Путешественники
$app->get('/travelers', [TravelerController::class, 'index']);
$app->post('/travelers', [TravelerController::class, 'store']);
$app->get('/travelers/{id}', [TravelerController::class, 'show']);
$app->put('/travelers/{id}', [TravelerController::class, 'update']);
$app->delete('/travelers/{id}', [TravelerController::class, 'destroy']);

// Routes: Достопримечательности
$app->get('/attractions', [AttractionController::class, 'index']);
$app->post('/attractions', [AttractionController::class, 'store']);
$app->get('/attractions/{id}', [AttractionController::class, 'show']);
$app->put('/attractions/{id}', [AttractionController::class, 'update']);
$app->delete('/attractions/{id}', [AttractionController::class, 'destroy']);

// Routes: Оценка
$app->get('/rating', [RatingController::class, 'index']);
$app->post('/rating', [RatingController::class, 'store']);
$app->get('/rating/{id}', [RatingController::class, 'show']);
$app->put('/rating/{id}', [RatingController::class, 'update']);
$app->delete('/rating/{id}', [RatingController::class, 'destroy']);


$app->run();