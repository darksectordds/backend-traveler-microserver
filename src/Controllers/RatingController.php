<?php

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Models\Rating;
use App\Repositories\RatingRepository;
use App\Validations\ValidationRatingScore;
use Slim\Http\ServerRequest as Request;
use Slim\Http\Response as Response;
use App\Exceptions\NotFoundException;

class RatingController
{
    protected $ratingRepository;

    public function __construct()
    {
        $this->ratingRepository = new RatingRepository();
    }

    public function index(Request $request, Response $response, $args)
    {
        $page = $request->getQueryParam('page') ?? 1;
        $perPage = $request->getQueryParam('limit') ?? 10;

        $ratings = $this->ratingRepository->getPaginate($page, $perPage);

        $response
            ->getBody()
            ->write(json_encode($ratings));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $rating = $this->ratingRepository->findById($id);

        if (!$rating) {
            throw new NotFoundException("Rating not found");
        }

        $response
            ->getBody()
            ->write(json_encode($rating));

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $validator = new ValidationRatingScore();
        $validator($data);

        $rating = new Rating(null, $data['traveler_id'], $data['attraction_id'], $data['score']);
        $rating = $this->ratingRepository->save($rating);

        $response
            ->getBody()
            ->write(json_encode($rating));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    /**
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function update(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $data = $request->getParams();

        $validator = new ValidationRatingScore();
        $validator($data);

        $existingCity = $this->ratingRepository->findById($id);
        if (!$existingCity) {
            throw new NotFoundException("Rating not found");
        }

        $rating = new Rating(null, $data['traveler_id'], $data['attraction_id'], $data['score']);
        $rating = $this->ratingRepository->save($rating);

        $response
            ->getBody()
            ->write(json_encode($rating));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $success = $this->ratingRepository->delete($id);

        if (!$success) {
            throw new NotFoundException("Rating not found");
        }

        return $response->withStatus(204);
    }
}