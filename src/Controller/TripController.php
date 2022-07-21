<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Entity\User;
use App\Form\TripType;
use App\Repository\TripRepository;
use App\Service\Locator;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/trip')]
class TripController extends AbstractController
{
    #[Route('/', name: 'app_trip_index', methods: ['GET'])]
    public function index(TripRepository $tripRepository): Response
    {
        return $this->render('trip/index.html.twig', [
            'trips' => $tripRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/mes-trajets', name: 'app_my_trips')]
    public function myTrips(TripRepository $tripRepository): Response
    {
        return $this->render('trip/my_trips.html.twig', [
            'trips' => $tripRepository->findPassengerTrips($this->getUser()),
        ]);
    }

    #[Route('/je-conduis', name: 'app_user_driver')]
    public function userDriver(TripRepository $tripRepository): Response
    {
        return $this->render('trip/driver_trips.html.twig', [
            'trips' => $tripRepository->findBy(['driver' => $this->getUser()], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_trip_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TripRepository $tripRepository, Locator $locator): Response
    {
        $trip = new Trip();
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            $locator->setCoordinates($trip);
            $trip->setDriver($this->getUser());
            $tripRepository->add($trip, true);
            
            return $this->redirectToRoute('app_user_driver', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trip/new.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trip_show', methods: ['GET'])]
    public function show(Trip $trip): Response
    {
        return $this->render('trip/show.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trip_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trip $trip, TripRepository $tripRepository, Locator $locator): Response
    {
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $locator->setCoordinates($trip);
            $trip->setDriver($this->getUser());
            $tripRepository->add($trip, true);

            return $this->redirectToRoute('app_user_driver', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trip/edit.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trip_delete', methods: ['POST'])]
    public function delete(Request $request, Trip $trip, TripRepository $tripRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trip->getId(), $request->request->get('_token'))) {
            $tripRepository->remove($trip, true);
        }

        return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/trip/{id}/accepter', name: 'app_trip_accept', methods: ['POST'])]
    public function acceptReservation(
        Trip $trip,
        TripRepository $tripRepository,
        MailerInterface $mailer
    ): Response {
        /** @var User */
        $user = $this->getUser();

        $trip->addPassenger($user);
        $trip->setSpots($trip->getSpots() - 1);
        $tripRepository->add($trip, true);

        $email = (new Email())
        ->from($this->getParameter('mailer_from'))
        ->to($trip->getDriver()->getEmail())
        ->subject('Vous avez un nouveau passager !')
        ->html($this->renderView('trip_mail/accept_trip_mail.html.twig', [
            'trip' => $trip, 
            'user' => $user
        ]));
        $mailer->send($email);

        $this->addFlash('success', 'Bienvenue à bord !');

        return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/trip/{id}/annuler', name: 'app_trip_cancel', methods: ['POST'])]
    public function cancelReservation(
        Trip $trip,
        TripRepository $tripRepository,
    ): Response {
        /** @var User */
        $user = $this->getUser();

        $trip->removePassenger($user);
        $trip->setSpots($trip->getSpots() + 1);
        $tripRepository->add($trip, true);

        $this->addFlash('warning', 'Vous avez bien décommandé ce trajet !');

        return $this->redirectToRoute('app_my_trips', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/drive/{id}/annuler', name: 'app_driver_cancel', methods: ['POST'])]
    public function cancelDriving(
        Trip $trip,
        TripRepository $tripRepository,
        MailerInterface $mailer
    ): Response {

        foreach ($trip->getPassengers() as $passenger) {
            $email = (new Email())
            ->from($this->getParameter('mailer_from'))
            ->to($passenger->getEmail())
            ->subject('Annulation d\'un trajet')
            ->html($this->renderView('trip_mail/cancel_driver_trip.html.twig', [
                'trip' => $trip, 
                'passenger' => $passenger
            ]));
            $mailer->send($email);
        }

        $tripRepository->remove($trip, true);

        $this->addFlash('warning', 'Vous avez bien décommandé ce trajet !');

        return $this->redirectToRoute('app_user_driver', [], Response::HTTP_SEE_OTHER);
    }

}
