<?php
namespace App\Vue;
use App\Utilitaire\Vue_Composant;

class Vue_Entreprise_Gerer_Compte  extends Vue_Composant
{

    private string $msg ="";

    function __construct (string $msg="")
    {
        $this->msg=$msg;
    }

    function donneTexte () : string
    {
        return " 
    <H1>Gestion du compte</H1>
    <table style='display: inline-block'>
        <tr>
            <td>
                <form action='index.php' style='display: contents'>
                    
                    <input type='hidden' name='case' value='Gerer_Entreprise'>
                   ".genereChampHiddenCSRF()." 
                    <button type='submit' name='action' value='infoEntreprise'>
                        Les informations de l&apos;entreprise
                    </button>
                    
                </form>
            </td>
        </tr>
        <tr>
            <td>
                <form action='index.php' style='display: contents'>
                    
                    <input type='hidden' name='case' value='Gerer_Entreprise'>
                      ".genereChampHiddenCSRF()."   
                
                    <button type='submit' name='action' value='salariesHabitites'>
                        Personnes habilitées
                    </button>
                    
                </form>
            </td>
        </tr>
        <tr>
            <td>
                <form action='index.php' style='display: contents'>
                    
                    <input type='hidden' name='case' value='Gerer_monCompte'>
                    ".genereChampHiddenCSRF()."
                    <button type='submit' name='action' value='ChangerMDPEntreprise'>
                        Changer mot de passe
                    </button>
                   
                </form>
            </td>
        </tr>
        <tr>
            <td>
                <form action='index.php' style='display: contents'>
                    
                    <input type='hidden' name='case' value='Gerer_monCompte'>    
                    ".genereChampHiddenCSRF()."
                    <button type='submit' name='action' value='deconnexionEntreprise'>
                        Se déconnecter
                    </button>
                    
                </form>
            </td>
        </tr>
    </table>
   <br> $this->msg   ";
    }
}