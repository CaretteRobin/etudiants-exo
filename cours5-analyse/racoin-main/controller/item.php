<?php

namespace controller;
use model\Annonce;
use model\Annonceur;
use model\Departement;
use model\Photo;
use model\Categorie;

class item {
    protected $annonce = null;
    protected $annonceur = null;
    protected $departement = null;
    protected $photo = array();
    protected $categItem = null;
    protected $dptItem = null;

    public function __construct(){
    }
    function afficherItem($twig, $menu, $chemin, $n, $cat) {

        $this->annonce = Annonce::find($n);
        if(!isset($this->annonce)){
            echo "404";
            return;
        }

        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/cat/".$n,
                'text' => Categorie::find($this->annonce->id_categorie)->nom_categorie),
            array('href' => $chemin."/item/".$n,
            'text' => $this->annonce->titre)
        );

        $this->annonceur = Annonceur::find($this->annonce->id_annonceur);
        $this->departement = Departement::find($this->annonce->id_departement );
        $this->photo = Photo::where('id_annonce', '=', $n)->get();
        echo $twig->render("item.html.twig",array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce,
            "annonceur" => $this->annonceur,
            "dep" => $this->departement->nom_departement,
            "photo" => $this->photo,
            "categories" => $cat));
    }

    function supprimerItemGet($twig, $menu, $chemin,$n){
        $this->annonce = Annonce::find($n);
        if(!isset($this->annonce)){
            echo "404";
            return;
        }
        echo $twig->render("delGet.html.twig",array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce));
    }


    function supprimerItemPost($twig, $menu, $chemin, $n, $cat, $allPostVars = array()){
        $this->annonce = Annonce::find($n);
        $reponse = false;
        $pass = trim((string)($allPostVars['pass'] ?? ($_POST['pass'] ?? '')));
        if($pass !== '' && password_verify($pass,$this->annonce->mdp)){
            $reponse = true;
            photo::where('id_annonce', '=', $n)->delete();
            $this->annonce->delete();

        }

        echo $twig->render("delPost.html.twig",array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce,
            "pass" => $reponse,
            "categories" => $cat));
    }

    function modifyGet($twig, $menu, $chemin, $id){
        $this->annonce = Annonce::find($id);
        if(!isset($this->annonce)){
            echo "404";
            return;
        }
        echo $twig->render("modifyGet.html.twig",array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce));
    }

    function modifyPost($twig, $menu, $chemin, $n, $cat, $dpt, $allPostVars = array()){
        $this->annonce = Annonce::find($n);
        $this->annonceur = Annonceur::find($this->annonce->id_annonceur);
        $this->categItem = Categorie::find($this->annonce->id_categorie)->nom_categorie;
        $this->dptItem = Departement::find($this->annonce->id_departement)->nom_departement;

        $reponse = false;
        $pass = trim((string)($allPostVars['pass'] ?? ($_POST['pass'] ?? '')));
        if($pass !== '' && password_verify($pass,$this->annonce->mdp)){
            $reponse = true;

        }

        echo $twig->render("modifyPost.html.twig",array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce,
            "annonceur" => $this->annonceur,
            "pass" => $reponse,
            "categories" => $cat,
            "departements" => $dpt,
            "dptItem" => $this->dptItem,
            "categItem" => $this->categItem));
    }

    function edit($twig, $menu, $chemin, $allPostVars, $id){

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
        $newPassword = $getTrimmed($allPostVars, 'psw');


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
            $this->annonce = Annonce::find($id);
            $idannonceur = $this->annonce->id_annonceur;
            $this->annonceur = Annonceur::find($idannonceur);


            $this->annonceur->email = htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $this->annonceur->nom_annonceur = htmlspecialchars($nom, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $this->annonceur->telephone = htmlspecialchars($phone, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $this->annonce->ville = htmlspecialchars($ville, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $this->annonce->id_departement = (int) $departement;
            $this->annonce->prix = (float) $price;
            if($newPassword !== '') {
                $this->annonce->mdp = password_hash($newPassword, PASSWORD_DEFAULT);
            }
            $this->annonce->titre = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $this->annonce->description = htmlspecialchars($description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $this->annonce->id_categorie = (int) $categorie;
            $this->annonce->date = date('Y-m-d');
            $this->annonceur->save();
            $this->annonceur->annonce()->save($this->annonce);


            echo $twig->render("modif-confirm.html.twig",array("breadcrumb" => $menu, "chemin" => $chemin));
        }
    }
}
