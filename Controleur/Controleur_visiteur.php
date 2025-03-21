<?php

use App\Modele\Modele_Entreprise;
use App\Modele\Modele_Salarie;
use App\Modele\Modele_Utilisateur;
use App\Vue\Vue_AfficherMessage;
use App\Vue\Vue_Connexion_Formulaire_client;
use App\Vue\Vue_ConsentementRGPD;
use App\Vue\Vue_Mail_Confirme;
use App\Vue\Vue_Mail_ReinitMdp;
use App\Vue\Vue_Menu_Administration;
use App\Vue\Vue_Structure_BasDePage;
use App\Vue\Vue_Structure_Entete;

use App\Vue\Vue_Utilisateur_Changement_MDPForce;
use PHPMailer\PHPMailer\PHPMailer;
use function App\Fonctions\CalculComplexiteMdp;
use function App\Fonctions\envoyerMail;

//Ce contrôleur gère le formulaire de connexion pour les visiteurs

$Vue->setEntete(new Vue_Structure_Entete());

switch ($action) {
    case "reinitmdpconfirm":

        //comme un qqc qui manque... je dis ça ! je dis rien !

        if(!filter_var($_REQUEST["email"], FILTER_VALIDATE_EMAIL)){
            $Vue->addToCorps(new Vue_Mail_ReinitMdp());
            $Vue->addToCorps(new Vue_AfficherMessage("<br><label><b>Erreur : Vous devez saisir un mail valide</b></label>"));


        }
        else {
            $utilisateur = Modele_Utilisateur::Utilisateur_Select_ParLogin($_REQUEST["email"]);
            if($utilisateur) {
                //calcul de la date dans une heure :
                $date = new DateTime();
                $date->add(new DateInterval('PT1H'));

                $token = \App\Modele\Modele_Token::Salarie_CreerToken(1, $utilisateur["idUtilisateur"], $date);
                $msg="<h1>Renouvellement de votre mot de passe </h1>";
                $msg.="Cliquez sur le lien suivant <a href='http://localhost:8080/index.php?action=reinitmdpToken&token=".urlencode($token )."'>ici</a> pour renouveler votre mot de passe";
                $resultat = envoyerMail("administration@cafe.local", "Administrateur café", $utilisateur["login"], $utilisateur["login"], "Réinitialisation de votre mot de passe", $msg);


                $Vue->addToCorps(new Vue_Mail_Confirme());
            }
            $Vue->addToCorps(new Vue_Mail_ReinitMdp());
            $Vue->addToCorps(new Vue_AfficherMessage("<br><label><b>Si l'e-mail communiqué est valide, vous recevrez un lien pour renouveler votre mot de passe.</b></label>"));

        }
        break;
    case "reinitmdp":


        $Vue->addToCorps(new Vue_Mail_ReinitMdp());

        break;
    case "submitModifMDPForce":
        if ($_REQUEST["NouveauPassword"] == $_REQUEST["ConfirmPassword"]) {
            $Vue->setEntete(new Vue_Structure_Entete());
            $complexite = CalculComplexiteMdp($_REQUEST["NouveauPassword"]);
            if ($complexite < 90) {
                $Vue->addToCorps(new Vue_Utilisateur_Changement_MDPForce("<label><b>Le mot de passe doit avoir une complexite d'au moins 90. Ici elle juste est de $complexite. Vous pouvez augmenter la longueur, le type de caractères (majuscule, miniscule, numérique, caractère spécial)</b></label>"));

            } else {
                Modele_Utilisateur::Utilisateur_Modifier_motDePasse($_SESSION["idUtilisateur"], $_REQUEST["NouveauPassword"]);
               // $Vue->addToCorps(new Vue_Utilisateur_Changement_MDPForce("<label><b>Votre mot de passe a bien été modifié</b></label>"));
                // Dans ce cas les mots de passe sont bons, il est donc modifié
                $utilisateur = Modele_Utilisateur::Utilisateur_Select_ParId($_SESSION["idUtilisateur"]);

                if ($utilisateur["aAccepteRGPD"] == 1)
                    switch ($utilisateur["idCategorie_utilisateur"]) {
                        case 1:
                            $_SESSION["typeConnexionBack"] = "administrateurLogiciel"; //Champ inutile, mais bien pour voir ce qu'il se passe avec des étudiants !
                            $Vue->setMenu(new Vue_Menu_Administration($_SESSION["typeConnexionBack"]));
                            break;
                        case 2:
                            $_SESSION["typeConnexionBack"] = "gestionnaireCatalogue";
                            $Vue->setMenu(new Vue_Menu_Administration($_SESSION["typeConnexionBack"]));
                            $Vue->addToCorps(new \App\Vue\Vue_AfficherMessage("Bienvenue " . $_REQUEST["compte"]));
                            break;
                        case 3:
                            $_SESSION["typeConnexionBack"] = "entrepriseCliente";
                            //error_log("idUtilisateur : " . $_SESSION["idUtilisateur"]);
                            $_SESSION["idEntreprise"] = Modele_Entreprise::Entreprise_Select_Par_IdUtilisateur($_SESSION["idUtilisateur"])["idEntreprise"];
                            include "./Controleur/Controleur_Gerer_Entreprise.php";
                            break;
                        case 4:
                            $_SESSION["typeConnexionBack"] = "salarieEntrepriseCliente";
                            $_SESSION["idSalarie"] = $utilisateur["idUtilisateur"];
                            $_SESSION["idEntreprise"] = Modele_Salarie::Salarie_Select_byId($_SESSION["idUtilisateur"])["idEntreprise"];
                            include "./Controleur/Controleur_Catalogue_client.php";
                            break;
                        case 5:
                            $_SESSION["typeConnexionBack"] = "commercialCafe";
                            $Vue->setMenu(new Vue_Menu_Administration($_SESSION["typeConnexionBack"]));
                            break;
                    }
                else {
                    $Vue->addToCorps(new Vue_ConsentementRGPD($utilisateur));
                }
            }
        } else {
            $Vue->setEntete(new Vue_Structure_Entete());
            $Vue->addToCorps(new Vue_Utilisateur_Changement_MDPForce("<label><b>Les nouveaux mots de passe ne sont pas identiques</b></label>"));
        }


        break;
    case    "Se connecter" :
        if (isset($_REQUEST["compte"]) and isset($_REQUEST["password"])) {
            //Si tous les paramètres du formulaire sont bons

            $utilisateur = Modele_Utilisateur::Utilisateur_Select_ParLogin($_REQUEST["compte"]);

            if ($utilisateur != null) {
                //error_log("utilisateur : " . $utilisateur["idUtilisateur"]);
                if ($utilisateur["desactiver"] == 0) {
                    if ($_REQUEST["password"] == $utilisateur["motDePasse"]) {
                        $_SESSION["idUtilisateur"] = $utilisateur["idUtilisateur"];
                        //error_log("idUtilisateur : " . $_SESSION["idUtilisateur"]);
                        $_SESSION["idCategorie_utilisateur"] = $utilisateur["idCategorie_utilisateur"];

                        if ($utilisateur["DoitChangerMotDePasse"] == 1) {
                            $Vue->addToCorps(new \App\Vue\Vue_Utilisateur_Changement_MDPForce());

                        } else {
                            if ($utilisateur["aAccepteRGPD"] == 1)
                                switch ($utilisateur["idCategorie_utilisateur"]) {
                                    case 1:
                                        $_SESSION["typeConnexionBack"] = "administrateurLogiciel"; //Champ inutile, mais bien pour voir ce qu'il se passe avec des étudiants !
                                        $Vue->setMenu(new Vue_Menu_Administration($_SESSION["typeConnexionBack"]));
                                        break;
                                    case 2:
                                        $_SESSION["typeConnexionBack"] = "gestionnaireCatalogue";
                                        $Vue->setMenu(new Vue_Menu_Administration($_SESSION["typeConnexionBack"]));
                                        $Vue->addToCorps(new \App\Vue\Vue_AfficherMessage("Bienvenue " . $_REQUEST["compte"]));
                                        break;
                                    case 3:
                                        $_SESSION["typeConnexionBack"] = "entrepriseCliente";
                                        //error_log("idUtilisateur : " . $_SESSION["idUtilisateur"]);
                                        $_SESSION["idEntreprise"] = Modele_Entreprise::Entreprise_Select_Par_IdUtilisateur($_SESSION["idUtilisateur"])["idEntreprise"];
                                        include "./Controleur/Controleur_Gerer_Entreprise.php";
                                        break;
                                    case 4:
                                        $_SESSION["typeConnexionBack"] = "salarieEntrepriseCliente";
                                        $_SESSION["idSalarie"] = $utilisateur["idUtilisateur"];
                                        $_SESSION["idEntreprise"] = Modele_Salarie::Salarie_Select_byId($_SESSION["idUtilisateur"])["idEntreprise"];
                                        include "./Controleur/Controleur_Catalogue_client.php";
                                        break;
                                    case 5:
                                        $_SESSION["typeConnexionBack"] = "commercialCafe";
                                        $Vue->setMenu(new Vue_Menu_Administration($_SESSION["typeConnexionBack"]));
                                        break;
                                }
                            else {
                                $Vue->addToCorps(new Vue_ConsentementRGPD($utilisateur));
                            }
                        }

                    } else {//mot de passe pas bon
                        $msgError = "Mot de passe erroné";

                        $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));

                    }
                } else {
                    $msgError = "Compte désactivé";

                    $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));

                }
            } else {
                $msgError = "Identification invalide";

                $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));
            }
        } else {
            $msgError = "Identification incomplete";

            $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));
        }
        break;
    default:

        $Vue->addToCorps(new Vue_Connexion_Formulaire_client());

        break;
}


$Vue->setBasDePage(new Vue_Structure_BasDePage());