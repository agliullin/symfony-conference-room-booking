<?php

namespace App\Validator;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Traits\DatetimeRange;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Booking datetimeRange validator
 */
class BookingDatetimeRangeValidator extends ConstraintValidator
{
    /**
     * Use datetimeRange trait
     */
    use DatetimeRange;

    /**
     * Entity manager
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Validate
     *
     * @param $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof BookingDatetimeRange) {
            throw new UnexpectedTypeException($constraint, BookingDatetimeRange::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $dates = $this->getDatetimeRangeFromFormat($value, 'd.m.Y H:i');
        if ($dates) {
            $start = reset($dates);
            $end = end($dates);
            $filters = [
                'room' => $this->context->getObject()->getRoom()->getId()
            ];

            if ($this->context->getObject()->getId()) {
                $filters['id'] = -1 * $this->context->getObject()->getId();
            }

            /** @var BookingRepository $bookingRepository */
            $bookingRepository = $this->entityManager->getRepository(Booking::class);
            if ($bookingRepository->findByFiltersBetweenStartEnd($filters, $start, $end)) {
                $this->context->buildViolation($constraint->error)
                    ->addViolation();
            }
        }
    }
}