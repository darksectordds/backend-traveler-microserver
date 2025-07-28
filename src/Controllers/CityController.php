<?php

namespace App\Controllers;

use App\Models\City;
use App\Repositories\CityRepository;
use Slim\Http\ServerRequest as Request;
use Slim\Http\Response as Response;
use App\Exceptions\NotFoundException;

class CityController
{
    protected $cityRepository;

    public function __construct()
    {
        $this->cityRepository = new CityRepository();
    }

    public function index(Request $request, Response $response, $args)
    {
        $page = $request->getQueryParam('page') ?? 1;
        $perPage = $request->getQueryParam('limit') ?? 10;

        $cities = $this->cityRepository->getPaginate($page, $perPage);

        $response
            ->getBody()
            ->write(json_encode($cities));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $city = $this->cityRepository->findById($id);

        if (!$city) {
            throw new NotFoundException("City not found");
        }

        $response
            ->getBody()
            ->write(json_encode($city));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $city = new City(null, $data['name']);
        $city = $this->cityRepository->save($city);

        $response
            ->getBody()
            ->write(json_encode($city));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $data = $request->getParams();

        $existingCity = $this->cityRepository->findById($id);
        if (!$existingCity) {
            throw new NotFoundException("City not found");
        }

        $city = new City($id, $data['name']);
        $city = $this->cityRepository->save($city);

        $response
            ->getBody()
            ->write(json_encode($city));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, $args)
    {
        $id = $args['id'];

        $success = $this->cityRepository->delete($id);
        if (!$success) {
            throw new NotFoundException("City not found");
        }

        return $response->withStatus(204);
    }
}