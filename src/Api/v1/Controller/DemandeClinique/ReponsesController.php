<?php

namespace App\Api\v1\Controller\DemandeClinique;

use App\Entity\DemandeClinique\Reponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Manager\DemandeClinique\ReponseManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/demande-clinique")
 */
class ReponsesController extends AbstractController
{
    /** @var ReponseManager */
    private $reponseManager;

    /** @var EntityManagerInterface $entityManagerInterface */
    private $entityManagerInterface;

    public function __construct(ReponseManager $reponseManager,EntityManagerInterface $entityManagerInterface)
    {
        $this->reponseManager = $reponseManager;
        $this->entityManagerInterface = $entityManagerInterface;
    }
    
    /**
     * @Route("/reponses/validation", name="api_v1_depots_valider_reponses", methods={"POST"})
     */
    public function validerReponses(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        foreach ($data['idReponses'] as $value) {
           
            $reponse = $this->entityManagerInterface->getRepository(Reponse::class)->find($value);
            if (!$reponse) {
                throw $this->createNotFoundException('Reponse non trouvée pour l\'identifiant donné.');
            }

            $this->reponseManager->valider($reponse, $data['motif']);
        }

        return $this->json('Resource updated', Response::HTTP_OK);
    }
}
