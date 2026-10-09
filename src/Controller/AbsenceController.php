<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AbsenceRepository;
use App\Entity\Absence;
use App\Form\AbsenceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EtudiantRepository;
use App\Form\AbsenceRechercheType;


class AbsenceController extends AbstractController
{
    #[Route('/absence', name: 'app_absence')]
    public function index(): Response
    {
        return $this->render('absence/index.html.twig', [
            'controller_name' => 'AbsenceController',
        ]);
    }

    #[Route('/absence/ajouter', name: 'absence_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle absence
        $absence = new Absence();

        // Créer le formulaire d'absence en associant l'objet $absence
        $form = $this->createForm(AbsenceType::class, $absence);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire (l'objet Absence)
            $absence = $form->getData();

            // Persister et sauvegarder dans la base de données
            $entityManager->persist($absence);
            $entityManager->flush();

            // Rediriger vers la liste des absences
            return $this->redirectToRoute('absence_lister');
        }

        // Si le formulaire n'a pas été soumis ou n'est pas valide, on l'affiche
        return $this->render('absence/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/absence/lister', name: 'absence_lister')]
    public function lister(AbsenceRepository $absenceRepository): Response
    {
        // Récupère toutes les absences enregistrées
        $absences = $absenceRepository->findAll();

        // Renvoie la vue avec les absences
        return $this->render('absence/lister.html.twig', [
            'absences' => $absences,
        ]);
    }


    #[Route('/absence/rechercher', name: 'absence_rechercher')]
    public function rechercher(Request $request, EtudiantRepository $etudiantRepository): Response
    {
        $form = $this->createForm(AbsenceRechercheType::class);
        $form->handleRequest($request);

        $etudiants = [];
        $formSubmitted = false;

    if ($form->isSubmitted() && $form->isValid()) {
        $formSubmitted = true;
        $etudiants = $etudiantRepository->findStudentByName($form->get('nom')->getData());
    }

    return $this->render('absence/rechercher.html.twig', [
        'form' => $form,
        'etudiants' => $etudiants,
        'formSubmitted' => $formSubmitted,
    ]);
    }
}
