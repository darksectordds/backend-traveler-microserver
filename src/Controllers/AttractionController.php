<?php

namespace App\Controllers;

use App\Models\Attraction;
use App\Repositories\AttractionRepository;
use Slim\Http\ServerRequest as Request;
use Slim\Http\Response as Response;
use App\Exceptions\NotFoundException;

class AttractionController
{
    protected $attractionRepository;

    public function __construct()
    {
        $this->attractionRepository = new AttractionRepository();
    }

    public function index(Request $request, Response $response, $args)
    {
        $params = $request->getParams();
        $page = $params['page'] ?? 1;
        $perPage = $params['limit'] ?? 10;

        $attractions = $this->attractionRepository->findAllWithFilters($params, $page, $perPage);

        $response
            ->getBody()
            ->write(json_encode($attractions));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, $args)
    {
        $id = $args['id'];

        $attraction = $this->attractionRepository->findById($id);
        if (!$attraction) {
            throw new NotFoundException("Attraction not found");
        }

        $response
            ->getBody()
            ->write(json_encode($attraction));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $attraction = new Attraction(null, $data['name'], $data['distance_from_center'], $data['city_id']);
        $attraction = $this->attractionRepository->save($attraction);

        $response
            ->getBody()
            ->write(json_encode($attraction));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $data = $request->getParams();

        $existingCity = $this->attractionRepository->findById($id);
        if (!$existingCity) {
            throw new NotFoundException("Attraction not found");
        }

        $attraction = new Attraction($id, $data['name'], $data['distance_from_center'], $data['city_id']);
        $attraction = $this->attractionRepository->save($attraction);

        $response
            ->getBody()
            ->write(json_encode($attraction));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $success = $this->attractionRepository->delete($id);

        if (!$success) {
            throw new NotFoundException("Attraction not found");
        }

        return $response->withStatus(204);
    }
}