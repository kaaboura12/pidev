<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\EventaddmodifyType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EventeditType;

final class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event')]
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    #[Route('/event/{idevent}', name: 'event_details')]
    public function details(EntityManagerInterface $entityManager, int $idevent): Response
    {
        $event = $entityManager
            ->getRepository(Event::class)
            ->find($idevent);

        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        return $this->render('frontOffice/details.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/events', name: 'events_list')]
    public function listEvents(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager
            ->getRepository(Event::class)
            ->findAll();

        return $this->render('frontOffice/test.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/backevent', name: 'back_showevent')]
    public function show(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();
        
        // Create forms for each event
        $forms = [];
        foreach ($events as $event) {
            $forms[$event->getIdevent()] = $this->createForm(EventeditType::class, $event)->createView();
        }
        
        return $this->render('backOffice/events/eventshow.html.twig', [
            'events' => $events,
            'forms' => $forms,
        ]);
    }

    #[Route('/event/delete/{id}', name: 'app_event_delete')]
    public function delete(Event $event, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash('success', 'Event deleted successfully');

        return $this->redirectToRoute('back_showevent');
    }

    #[Route('/eventadd', name: 'app_event_new')]
    public function createOrEditEvent(
        Request $request, 
        EntityManagerInterface $entityManager,
        Event $event = null
    ): Response
    {
        $event = $event ?? new Event();
        $form = $this->createForm(EventaddmodifyType::class, $event);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            // Generate a unique filename for the image
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();

            // Move the uploaded file to the specified directory
            $imageFile->move(
                $this->getParameter('images_directory'), // Directory from services.yaml
                $newFilename
            );

            // Save the filename (path) into the article entity
            $event->setImage($newFilename);
        }
            
            
            $entityManager->persist($event);
            $entityManager->flush();
            
            $this->addFlash('success', 'Event has been saved successfully!');
            return $this->redirectToRoute('back_showevent'); // Adjust this route to your events listing page
        }
        
        return $this->render('backOffice/events/event_form.html.twig', [
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

    #[Route('/event/edit/{id}', name: 'app_event_edit', methods: ['POST'])]
    public function editEvent(
        Request $request, 
        Event $event, 
        EntityManagerInterface $entityManager
    ): Response 
    {
        $form = $this->createForm(EventeditType::class, $event);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                return $this->json([
                    'success' => true,
                    'message' => 'Event updated successfully'
                ]);
            } catch (\Exception $e) {
                return $this->json([
                    'success' => false,
                    'message' => 'Error updating event: ' . $e->getMessage()
                ], 500);
            }
        }
        
        return $this->json([
            'success' => false,
            'message' => 'Invalid form data'
        ], 400);
    }
}
