<?php
namespace App\Vue;
use App\Utilitaire\Vue_Composant;

class Vue_Menu_Administration extends Vue_Composant
{
    private string $typeDeVue="";
    public function __construct($typeDeVue )
    {
        $this->typeDeVue=$typeDeVue ;
    }
    function donneTexte(): string
    {
        switch($this->typeDeVue)
        {
            case "administrateurLogiciel" :
                return "
             <nav id='menu'>
              <ul id='menu-closed'> 
                <li><a href='?case=Gerer_utilisateur".genereVarHrefCSRF()."'>Utilisateurs</a></li>
                     
                <li><a href='?case=Gerer_monCompte".genereVarHrefCSRF()."'>Mon compte</a></li> 
               </ul>
            </nav> 
";
                break;
            case "gestionnaireCatalogue":
                return "
             <nav id='menu'>
              <ul id='menu-closed'> 
                        <li><a href='?case=Gerer_catalogue".genereVarHrefCSRF()."'>Catalogue</a></li>   
            
                <li><a href='?case=Gerer_monCompte".genereVarHrefCSRF()."'>Mon compte</a></li> 
               </ul>
            </nav> 
";
                break;
            case "commercialCafe":
                return "
             <nav id='menu'>
              <ul id='menu-closed'> 
             
             <li><a href='?case=Gerer_entreprisesPartenaires".genereVarHrefCSRF()."'>Entreprises partenaires</a></li>
               <li><a href='?case=Gerer_Commande".genereVarHrefCSRF()."'>Commandes</a></li>
            
                <li><a href='?case=Gerer_monCompte".genereVarHrefCSRF()."'>Mon compte</a></li> 
               </ul>
            </nav> 
";
                break;
            default:
                return "menu vide ! $this->typeDeVue";


        }

              
    }
}
