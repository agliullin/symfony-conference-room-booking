<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Booking controller
 *
 * @Route("/booking")
 */
class BookingController extends AbstractController
{
    /**
     * Detail booking
     *
     * @Route("/{uuid}", name="app_booking_show", methods={"GET"})
     */
    public function show(Booking $booking): Response
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    /**
     * Edit booking
     *
     * @Route("/{uuid}/edit", name="app_booking_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Booking $booking, BookingRepository $bookingRepository): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($bookingRepository->addBooking($booking)) {
                $this->addFlash(
                    'success',
                    'Booking edited successfully!'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Booking editing failed!'
                );
            }

            return $this->redirectToRoute('app_booking_show', ['uuid' => $booking->getUuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }

    /**
     * Delete booking
     *
     * @Route("/{id}", name="app_booking_delete", methods={"POST"})
     */
    public function delete(Request $request, Booking $booking, BookingRepository $bookingRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
            $bookingRepository->remove($booking);
            $this->addFlash(
                'success',
                'Booking deleted successfully!'
            );
        }

        return $this->redirectToRoute('app_schedule_room', ['uuid' => $booking->getRoom()->getUuid()], Response::HTTP_SEE_OTHER);
    }
}
