<?php

namespace App\EventSubscriber;

use App\Repository\BookingRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Calendar subscriber class
 */
class CalendarSubscriber implements EventSubscriberInterface
{
    /**
     * Booking repo
     *
     * @var BookingRepository
     */
    private $bookingRepository;

    /**
     * Router
     *
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * Constructor
     *
     * @param BookingRepository $bookingRepository
     * @param UrlGeneratorInterface $router
     */
    public function __construct(BookingRepository $bookingRepository, UrlGeneratorInterface $router)
    {
        $this->bookingRepository = $bookingRepository;
        $this->router = $router;
    }

    /**
     * Get subscribed events
     *
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    /**
     * Set data event
     *
     * @param CalendarEvent $calendar
     * @return void
     */
    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        $bookings = $this->bookingRepository->findByFiltersBetweenStartEnd($filters, $start, $end);

        foreach ($bookings as $booking) {
            $bookingEvent = new Event(
                $booking->getName(),
                $booking->getStartDatetime(),
                $booking->getEndDatetime()
            );

            $bookingEvent->setOptions([
                'backgroundColor' => $booking->getColor(),
                'borderColor' => $booking->getColor(),
            ]);
            $bookingEvent->addOption(
                'url',
                $this->router->generate('app_booking_show', [
                    'uuid' => $booking->getUuid(),
                ])
            );

            $calendar->addEvent($bookingEvent);
        }
    }
}