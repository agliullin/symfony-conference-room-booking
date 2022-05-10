<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Room;
use App\Form\BookingInRoomType;
use App\Repository\BookingRepository;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Schedule controller
 *
 * @Route("/schedule")
 */
class ScheduleController extends AbstractController
{
    /**
     * Schedule page
     *
     * @Route("/", name="app_schedule")
     */
    public function index(RoomRepository $roomRepository): Response
    {
        return $this->render('schedule/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    /**
     * Calendar in room
     *
     * @Route("/{uuid}", name="app_schedule_room", methods={"GET"})
     */
    public function room(Room $room): Response
    {
        return $this->render('schedule/room.html.twig', [
            'room' => $room,
        ]);
    }

    /**
     * New booking in room
     *
     * @Route("/{uuid}/new", name="app_schedule_room_booking_new", methods={"GET", "POST"})
     */
    public function roomBookingNew(Request $request, Room $room, BookingRepository $bookingRepository): Response
    {
        $booking = new Booking();
        $booking->setRoom($room);
        $form = $this->createForm(BookingInRoomType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setOwner($this->getUser());
            if ($bookingRepository->addBooking($booking)) {
                $this->addFlash(
                    'success',
                    'Booking added successfully!'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Booking adding failed!'
                );
            }

            return $this->redirectToRoute('app_schedule_room', ['uuid' => $room->getUuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('schedule/booking/new.html.twig', [
            'booking' => $booking,
            'form' => $form,
            'room' => $room,
        ]);
    }
}
