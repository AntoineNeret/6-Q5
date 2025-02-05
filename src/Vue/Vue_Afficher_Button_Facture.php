<?php
namespace App\Vue;
use App\Utilitaire\Vue_Composant;

class Vue_Afficher_Button_Facture extends Vue_Composant
{
    private int $idCommande;
    public function __construct(int $idCommande)
    {
        $this->idCommande=$idCommande;
    }

    function donneTexte(): string
    {
        return "<form action='index.php' style='display: contents' >
                        
                        <input type='hidden' name='case' value='Gerer_CommandeClient' >
                        '.genereChampHiddenCSRF().'
                        <input type='hidden' name='idCommande' value='$this->idCommande' >
                        <button type='submit' name='action' value='AfficherCommandePDF' >
                            Voir facture
                        </button>
         </form >";
    }
}