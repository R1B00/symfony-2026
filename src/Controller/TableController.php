<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\TableChoiceType;
use Symfony\Component\HttpFoundation\Request;



final class TableController extends AbstractController
{
    #[Route('/table', name: 'app_table')]
    public function index(): Response
    {
        return $this->render('table/index.html.twig', [
            'controller_name' => 'TableController',
        ]);
    }

    #[Route('/table/print', name: 'app_table_print')]
    public function print(Request $requete): Response
    {
        $nombre = $requete->query->get('n',2);
        $table = [];

        for ($i = 0 ; $i < 11 ; $i++) {
            $table[$i] = $nombre * $i ;
        }

        return $this->render('table/print.html.twig', [
            'nombre' => $nombre,
            'table' => $table,
        ]);
    }


    #[Route('/table/select', name: 'app_table_select')]
    public function select(Request $requete): Response
    {
    $form = $this->createForm(TableChoiceType::class); // question 3
    $form->handleRequest($requete);

     // Vérifie si le formulaire a été soumis et est valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Récupère les données soumises
        $dataFormulaire = $form->getData();

        // Récupère la valeur d'un champ spécifique du formulaire
        $retour['n'] = $dataFormulaire['nombre'];

        // Redirige vers une autre route avec les données passées
        return $this->redirectToRoute('app_table_print', $retour);
    }


    return $this->render('table/select.html.twig', [ // question 4
        'form' => $form,
    ]);
    }
}
