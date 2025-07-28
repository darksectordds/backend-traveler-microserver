<?php

namespace App\Controllers;

use App\Models\Traveler;
use App\Repositories\TravelerRepository;
use Slim\Http\ServerRequest as Request;
use Slim\Http\Response as Response;
use App\Exceptions\NotFoundException;

class TravelerController
{
    protected $travelerRepository;

    public function __construct()
    {
        $this->travelerRepository = new TravelerRepository();
    }

    public function index(Request $request, Response $response, $args)
    {
        $page = $request->getQueryParam('page') ?? 1;
        $perPage = $request->getQueryParam('limit') ?? 10;

        $travelers = $this->travelerRepository->getPaginate($page, $perPage);

        $response->getBody()->write(json_encode($travelers));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, $args)
    {
        $id = $args['id'];

        $traveler = $this->travelerRepository->findById($id);
        if (!$traveler) {
            throw new NotFoundException("Traveler not found");
        }

        $response->getBody()->write(json_encode($traveler));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $traveler = new Traveler(null, $data['name']);
        $traveler = $this->travelerRepository->save($traveler);

        $response->getBody()->write(json_encode($traveler));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $data = $request->getParams();

        $existingTraveler = $this->travelerRepository->findById($id);
        if (!$existingTraveler) {
            throw new NotFoundException("Traveler not found");
        }

        $traveler = new Traveler($id, $data['name']);
        $traveler = $this->travelerRepository->save($traveler);

        $response->getBody()->write(json_encode($traveler));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, $args)
    {
        $id = $args['id'];

        $success = $this->travelerRepository->delete($id);
        if (!$success) {
            throw new NotFoundException("Traveler not found");
        }

        return $response->withStatus(204);
    }
}