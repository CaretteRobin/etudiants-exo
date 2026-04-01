<?php

namespace controller;

use model\Annonce;
use model\Annonceur;

class addItem{

    function addItemView($twig, $menu, $chemin, $cat, $dpt){

        echo $twig->render("add.html.twig",array(
                                    "breadcrumb" => $menu,
                                    "chemin" => $chemin,
                                    "categories" => $cat,
                                    "departements" => $dpt)
                                );

    }

    function addNewItem($twig, $menu, $chemin, $allPostVars){

        date_default_timezone_set('Europe/Paris');

        $getTrimmed = static function (array $data, $key) {
            return trim((string) ($data[$key] ?? ''));
        };

        /*
        * On récupère tous les champs du formulaire en supprimant
        * les caractères invisibles en début et fin de chaîne.
        */
        $nom = $getTrimmed($allPostVars, 'nom');
        $email = $getTrimmed($allPostVars, 'email');
        $phone = $getTrimmed($allPostVars, 'phone');
        $phoneNormalized = preg_replace('/\s+/', '', $phone);
        $ville = $getTrimmed($allPostVars, 'ville');
        $departement = $getTrimmed($allPostVars, 'departement');
        $categorie = $getTrimmed($allPostVars, 'categorie');
        $title = $getTrimmed($allPostVars, 'title');
        $description = $getTrimmed($allPostVars, 'description');
        $price = str_replace(',', '.', $getTrimmed($allPostVars, 'price'));
        $password = $getTrimmed($allPostVars, 'psw');
        $password_confirm = $getTrimmed($allPostVars, 'confirm-psw');

        // Tableau d'erreurs personnalisées
        $errors = array();
        $errors['nameAdvertiser'] = '';
        $errors['emailAdvertiser'] = '';
        $errors['phoneAdvertiser'] = '';
        $errors['villeAdvertiser'] = '';
        $errors['departmentAdvertiser'] = '';
        $errors['categorieAdvertiser'] = '';
        $errors['titleAdvertiser'] = '';
        $errors['descriptionAdvertiser'] = '';
        $errors['priceAdvertiser'] = '';
        $errors['passwordAdvertiser'] = '';

//        $fileInfos = $_FILES["fichier"];
//        $fileName = $fileInfos['name'];
//        $type_mime = $fileInfos['type'];
//        $taille = $fileInfos['size'];
//        $fichier_temporaire = $fileInfos['tmp_name'];
//        $code_erreur = $fileInfos['error'];


//        switch ($code_erreur){
//            case UPLOAD_ERR_OK :
//                $destination = "$chemin/upload/$fileName";
//
//                if (move_uploaded_file($fichier_temporaire, $destination)){
//                    $message  = "Transfert terminé - Fichier = $nom - ";
//                    $message .= "Taille = $taille octets - ";
//                    $message .= "Type MIME = $type_mime";
//                } else {
//                   $message = "Problème de copie sur le serveur";
//                }
//                break;
//            case UPLOAD_ERR_NO_FILE :
//                $message = "Pas de fichier saisi";
//                break;
//            case UPLOAD_ERR_INI_SIZE :
//                $message  = "Fichier '$fileName' non transféré ";
//                $message .= ' (taille > upload_max_filesize.';
//                break;
//            case UPLOAD_ERR_FORM_SIZE :
//                $message  = "Fichier '$fileName' non transféré ";
//                $message .= ' (taille > MAX_FILE_SIZE.';
//                break;
//            case UPLOAD_ERR_PARTIAL :
//                $message  = "Fichier '$fileName' non transféré ";
//                $message .= ' (problème lors du transfert';
//                break;
//            case UPLOAD_ERR_NO_TMP_DIR :
//                $message  = "Fichier '$fileName' non transféré ";
//                $message .= ' (pas de répertoire temporaire).';
//                break;
//            case UPLOAD_ERR_CANT_WRITE :
//                $message  = "Fichier '$fileName' non transféré ";
//                $message .= ' (erreur lors de l\'écriture du fichier sur disque).';
//                break;
//            case UPLOAD_ERR_EXTENSION :
//                $message  = "Fichier '$fileName' non transféré ";
//                $message .= ' (transfert stoppé par l\'extension).';
//                break;
//            default :
//                $message  = "Fichier '$fileName' non transféré ";
//                $message .= ' (erreur inconnue : $code_erreur';
//        }

        // On teste que les champs ne soient pas vides et soient de bons types
        if(empty($nom)) {
            $errors['nameAdvertiser'] = 'Veuillez entrer votre nom';
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['emailAdvertiser'] = 'Veuillez entrer une adresse mail correcte';
        }
        if($phone === '' || !preg_match('/^\d+$/', $phoneNormalized) ) {
            $errors['phoneAdvertiser'] = 'Veuillez entrer votre numéro de téléphone';
        }
        if(empty($ville)) {
            $errors['villeAdvertiser'] = 'Veuillez entrer votre ville';
        }
        if(!filter_var($departement, FILTER_VALIDATE_INT)) {
            $errors['departmentAdvertiser'] = 'Veuillez choisir un département';
        }
        if(!filter_var($categorie, FILTER_VALIDATE_INT)) {
            $errors['categorieAdvertiser'] = 'Veuillez choisir une catégorie';
        }
        if(empty($title)) {
            $errors['titleAdvertiser'] = 'Veuillez entrer un titre';
        }
        if(empty($description)) {
            $errors['descriptionAdvertiser'] = 'Veuillez entrer une description';
        }
        if($price === '' || !is_numeric($price)) {
            $errors['priceAdvertiser'] = 'Veuillez entrer un prix';
        }
        if(empty($password) || empty($password_confirm) || $password != $password_confirm) {
            $errors['passwordAdvertiser'] = 'Les mots de passes ne sont pas identiques';
        }

        // On vire les cases vides
        $errors = array_values(array_filter($errors));

        // S'il y a des erreurs on redirige vers la page d'erreur
        if (!empty($errors)) {

            echo $twig->render("add-error.html.twig",array(
                                    "breadcrumb" => $menu,
                                    "chemin" => $chemin,
                                    "errors" => $errors)
                                );
        }
        // sinon on ajoute à la base et on redirige vers une page de succès
        else{
            $annonce = new Annonce();
            $annonceur = new Annonceur();

            $annonceur->email = htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $annonceur->nom_annonceur = htmlspecialchars($nom, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $annonceur->telephone = htmlspecialchars($phone, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            $annonce->ville = htmlspecialchars($ville, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $annonce->id_departement = (int) $departement;
            $annonce->prix = (float) $price;
            $annonce->mdp = password_hash($password, PASSWORD_DEFAULT);
            $annonce->titre = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $annonce->description = htmlspecialchars($description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $annonce->id_categorie = (int) $categorie;
            $annonce->date = date('Y-m-d');


            $annonceur->save();
            $annonceur->annonce()->save($annonce);


            echo $twig->render("add-confirm.html.twig",array("breadcrumb" => $menu, "chemin" => $chemin));
        }
    }
}
