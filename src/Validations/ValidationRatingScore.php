<?php

namespace App\Validations;

use App\Exceptions\ValidationException;

class ValidationRatingScore
{
    /**
     * @throws ValidationException
     */
    public function __invoke(array $data): void
    {
        if (!isset($data['score']) || $data['score'] < 1 || $data['score'] > 5) {
            throw new ValidationException("Invalid rating score");
        }
    }
}