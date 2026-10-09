<?php

namespace App\Controller;

use App\Entity\Etudiant;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SaisirEtudiantType;


 final class EtudiantController extends AbstractController
{
    #[Route('/etudiant', name: 'app_etudiant')]
    public function index(): Response
    {
        return $this->render('etudiant/index.html.twig', [
            'controller_name' => 'EtudiantController',
        ]);
    }

    #[Route('/etudiant/creer', name: 'etudiant_creer')]
    public function creer(ManagerRegistry $doctrine): Response
    {
        // Récupère l'EntityManager de Doctrine, nécessaire pour interagir avec la base de données
        $entityManager = $doctrine->getManager();

        // Création de l'étudiant
        $etudiant = new Etudiant();
        $etudiant->setNom('JOHN');
        $etudiant->setPrenom('Lee');
        $etudiant->setNaissance(new DateTime("2005-09-13"));
        $etudiant->setNiveau(2);

        // Prépare Doctrine à sauvegarder l'étudiant (mais n'exécute pas encore la requête SQL)
        $entityManager->persist($etudiant);

        // Exécute la requête SQL pour insérer les données dans la base de données
        $entityManager->flush();

        return new Response('Nouvel étudiant sauvegardé, ID '.$etudiant->getId());
    }

   #[Route('/etudiant/afficher/{id}', name: 'etudiant_afficher')]
    public function afficherAvecRepository(int $id, EtudiantRepository $etudiantRepository): Response
    {
            $etudiant = $etudiantRepository->find($id);

            // Si aucun étudiant n'est trouvé, une exception est levée pour indiquer que l'étudiant n'existe pas
            if (!$etudiant) {
                throw $this->createNotFoundException('Aucun étudiant trouvé pour cet ID.');
            }
        return new Response('Le nom de l\'étudiant est : '.$etudiant->getNom());
    }

    // Modifications multiples :
    #[Route('/etudiant/changer/niveau', name: 'etudiant_changer_niveau')]
    public function changerNiveau(
        EntityManagerInterface $entityManager,
        EtudiantRepository $etudiantRepository): Response
    {
    // Récupération de tous les étudiants
    $etudiants = $etudiantRepository->findAll(); // SELECT * FROM etudiant

    // Modification du niveau de chaque étudiant
    foreach ($etudiants as $etudiant) {
        $etudiant->setNiveau(3); // Mise à jour du champ niveau
    }

    // Enregistre les modifications en base
    $entityManager->flush();

    // Renvoie une réponse
    return new Response('Modification du niveau pour ' . count($etudiants) . ' étudiant(s).');
    }


    #[Route('/etudiant/rechercher/nom/{nom}', name: 'etudiant_rechercher_nom')]
    public function rechercherNom(
        string $nom,
        EtudiantRepository $etudiantRepository)
        {

        $etudiants = $etudiantRepository->findStudentByName($nom);
        // $etudiants = $etudiantRepository->findStudentByNameSQL($nom);

        return $this->render('etudiant/rechercher_nom.html.twig', [
            'nom' => $nom,
            'etudiants' => $etudiants
        ]);
    }

    #[Route('/etudiant/saisir', name: 'etudiant_saisir')]
    public function saisirEtudiant(Request $request, EntityManagerInterface $entityManager): Response
    {
    // Création d'un nouvel étudiant vide
    $etudiant = new Etudiant();

    // Création du formulaire
    $form = $this->createForm(SaisirEtudiantType::class, $etudiant);

    // Gestion de la requête
    $form->handleRequest($request);

    // Vérifie si le formulaire a été soumis et est valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Récupère les données du formulaire (déjà liées à $etudiant)
        $entityManager->persist($etudiant);
        $entityManager->flush();

    return $this->redirectToRoute('etudiant_afficher', ['id' => $etudiant->getId()]);
    }

    // Affichage du formulaire (si non soumis ou non valide)
    return $this->render('etudiant/saisir.html.twig', [
        'form' => $form->createView(),
    ]);
    } 

    // route à placer en dernière position pour éviter les conflits
    #[Route('/etudiant/{id}', name: 'etudiant_afficher_manager')]
    public function afficher(ManagerRegistry $doctrine, int $id): Response

    {
        $etudiant = $doctrine->getRepository(Etudiant::class)->find($id);

        if (!$etudiant) {
            throw $this->createNotFoundException(
                'Pas d\'étudiant trouvé avec cet ID : '.$id
            );
        }

        return new Response('Le nom de l\'étudiant est : '.$etudiant->getNom());
    }
}
