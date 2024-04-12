<?php

namespace App\Factory\DemandeClinique;

use App\Entity\DemandeClinique\Depot;
use App\Entity\DemandeClinique\Reponse;

class ReponseFactory
{
    public function creer(Depot $depot, string $titre, string $description, int $type): Reponse
    {
        return (new Reponse())
            ->setTitre($titre)
            ->setDescription($description)
            ->setDateCreation(new \DateTime())
            ->setDepot($depot)
            ->setType($type)
            ->setMotif('')
            ->setValide(0)
        ;
    }

    public function valider(Reponse $reponse, string $motif): Reponse
    {
        if (isset($motif)) {
            $reponse->setMotif($motif);
            $reponse->setValide(true); 
        }
       
        return $reponse;
    }
}