<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\DemandeFinancement;
use App\Form\DemandeFinancementType;

final class DemandeFinancementController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {

    }
    #[Route('/',name:'liste_demande_financement')]
    public function liste(Request $request): Response
    {
        $demande = new DemandeFinancement();

        $form = $this->createForm(DemandeFinancementType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em= $this->entityManager;
            $error = $this->validateBusinessRules($demande, $em);
            if ($error) {
                $this->addFlash('error', $error);
            } else {
                $em->persist($demande);
                $em->flush();
                return $this->redirectToRoute('liste_demande_financement');
            }

            return $this->redirectToRoute('liste_demande_financement');
        }
        $listeDemande = $this->entityManager->getRepository(DemandeFinancement::class)->findAll();

        return $this->render('demande-financement/liste.html.twig', [
            'liste' => $listeDemande,
            'form' => $form->createView()
        ]);
    }
    #[Route('/demande/edit/{id}', name: 'edit_demande_financement')]
    public function edit(int $id,Request $request,EntityManagerInterface $em): Response {

        $demande = $em->getRepository(DemandeFinancement::class)->find($id);
        $form = $this->createForm(DemandeFinancementType::class, $demande, [
            'is_editing' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // vérification 
            $error = $this->validateBusinessRules($demande, $em);
          
            if ($error) {
                $this->addFlash('error', $error);
                return $this->redirectToRoute('edit_demande_financement', ['id' => $id]);
            } else {
                $em->flush();   
                return $this->redirectToRoute('liste_demande_financement');
            }
        }

        return $this->render('demande-financement/edit.html.twig', [
            'form' => $form->createView(),
            'demande' => $demande
        ]);
    }
    
    private function validateBusinessRules(DemandeFinancement $demande,EntityManagerInterface $em): ?string {

        $repo = $em->getRepository(DemandeFinancement::class);

        // vérification du montant <= 10 000 000
        if ($demande->getStatut() === 'VALIDEE' && $demande->getMontantDemande() > 10000000) {
            return "Une demande est valide que si le montant est inférieur ou égal à 10 000 000";
        }

        // vérification nb max 3 demandes EN_ATTENTE par entreprise
        $attentes = $repo->count([
            'matricule' => $demande->getMatricule(),
            'statut' => 'EN_ATTENTE'
        ]);

        if ($demande->getStatut() === 'EN_ATTENTE' && $attentes >= 3) {
            return "Une entreprise ne peut pas avoir plus de 3 demandes en attente";
        }

        // total EN_ATTENTE + VALIDE <= 22 000 000
        $qb = $repo->createQueryBuilder('d')
            ->select('SUM(d.montantDemande)')
            ->where('d.matricule = :mat')
            ->andWhere('d.statut IN (:status)')
            ->setParameter('mat', $demande->getMatricule())
            ->setParameter('status', ['EN_ATTENTE', 'VALIDEE'])
            ->getQuery();

        $total = $qb->getSingleScalarResult() ?? 0;

        if ($total> 22000000) {
            return "Le total des demandes dépasse 22 000 000";
        }

        return null;
    }
}
