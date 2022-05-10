<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Booking datetimeRange constraint
 *
 * @Annotation
 */
class BookingDatetimeRange extends Constraint
{
    public string $error = 'Booking already exists in this datetime range';
}