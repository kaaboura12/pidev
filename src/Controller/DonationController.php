<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DonationController extends AbstractController
{
    #[Route('/donation/add/{idevent}', name: 'add_donation')]
    public function addDonation(Request $request, int $idevent, EntityManagerInterface $entityManager): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($idevent);
        
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        // Get the data from the form
        $donation = new Donation();
        $donation->setEvent($event);
        $donation->setDonorname($request->request->get('donorname'));
        $donation->setEmail($request->request->get('email'));
        $donation->setMontant($request->request->get('montant'));
        $donation->setPaymentMethod($request->request->get('payment_method'));
        $donation->setNumTlf($request->request->get('num_tlf'));
        $donation->setDate(new \DateTime());
        
        // Add user if authenticated
        if ($this->getUser()) {
            $donation->setUser($this->getUser());
        }
        
        $entityManager->persist($donation);
        $entityManager->flush();
        
        $this->addFlash('success', 'Thank you for your donation!');
        
        return $this->redirectToRoute('event_details', ['idevent' => $idevent]);
    }

    #[Route('/donationsback', name: 'app_donation_show')]
    public function Showevent(EntityManagerInterface $entityManager): Response
    {
        // Get all donations from the database
        $donations = $entityManager->getRepository(Donation::class)->findAll();
        
        // Calculate statistics
        $totalAmount = 0;
        
        // Initialize arrays for data collection
        $donationDates = [];
        $donationAmounts = [];
        $paymentMethods = [];
        $donorNames = [];

        // Process donations for charts and statistics
        foreach ($donations as $donation) {
            $totalAmount += $donation->getMontant();
            $donationDates[] = $donation->getDate()->format('d M');
            $donationAmounts[] = $donation->getMontant();
            
            // Collect unique donor names
            if ($donation->getDonorname()) {
                $donorNames[$donation->getDonorname()] = true;
            }
            
            // Collect payment methods
            if ($donation->getpayment_method()) {
                $paymentMethods[$donation->getpayment_method()] = 
                    ($paymentMethods[$donation->getpayment_method()] ?? 0) + 1;
            }
        }

        // Calculate statistics
        $donorCount = count($donorNames);
        
        // Calculate percentage change (comparing with previous month)
        $lastMonthTotal = $this->calculateLastMonthTotal($entityManager);
        $percentageChange = $lastMonthTotal > 0 
            ? round((($totalAmount - $lastMonthTotal) / $lastMonthTotal) * 100, 1)
            : 0;
        
        // Calculate monthly progress
        $monthlyGoal = 1000; // Set your monthly goal here
        $monthlyProgress = $monthlyGoal > 0 
            ? min(100, round(($totalAmount / $monthlyGoal) * 100))
            : 0;

        return $this->render('backOffice/events/donationshow.html.twig', [
            'donations' => $donations,
            'totalAmount' => $totalAmount,
            'donorCount' => $donorCount,
            'percentageChange' => $percentageChange,
            'monthlyProgress' => $monthlyProgress,
            'donationDates' => array_values($donationDates),
            'donationAmounts' => array_values($donationAmounts),
            'paymentMethods' => array_keys($paymentMethods),
            'paymentMethodCounts' => array_values($paymentMethods),
        ]);
    }

    private function calculateLastMonthTotal(EntityManagerInterface $entityManager): float
    {
        $lastMonthStart = new \DateTime('first day of last month');
        $lastMonthEnd = new \DateTime('last day of last month');
        
        $lastMonthDonations = $entityManager->getRepository(Donation::class)
            ->createQueryBuilder('d')
            ->where('d.date BETWEEN :start AND :end')
            ->setParameter('start', $lastMonthStart)
            ->setParameter('end', $lastMonthEnd)
            ->getQuery()
            ->getResult();
        
        return array_reduce($lastMonthDonations, function($total, $donation) {
            return $total + $donation->getMontant();
        }, 0);
    }
}
