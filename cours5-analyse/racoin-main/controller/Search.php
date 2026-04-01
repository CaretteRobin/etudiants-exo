<?php

namespace controller;

use model\Annonce;
use model\Categorie;

class Search {

    function show($twig, $menu, $chemin, $cat) {
        $template = "search.html.twig";
        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/search",
                'text' => "Recherche")
        );
        echo $twig->render($template, array("breadcrumb" => $menu, "chemin" => $chemin, "categories" => $cat));
    }

    function research($array, $twig, $menu, $chemin, $cat) {
        $template = "index.html.twig";
        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/search",
                'text' => "Résultats de la recherche")
        );

        $motclef = trim((string)($array['motclef'] ?? ''));
        $codepostal = trim((string)($array['codepostal'] ?? ''));
        $categorie = (string)($array['categorie'] ?? 'Toutes catégories');
        $prixMin = (string)($array['prix-min'] ?? 'Min');
        $prixMax = (string)($array['prix-max'] ?? 'Max');

        $nospace_mc = str_replace(' ', '', $motclef);
        $nospace_cp = str_replace(' ', '', $codepostal);


        $query = Annonce::select();

        if( ($nospace_mc === "") &&
            ($nospace_cp === "") &&
            (($categorie === "Toutes catégories" || $categorie === "-----")) &&
            ($prixMin === "Min") &&
            ( ($prixMax === "Max") || ($prixMax === "nolimit") ) ) {
            $annonce = Annonce::all();

        } else {
            // A REFAIRE SEPARER LES TRUCS
            if( ($nospace_mc !== "") ) {
                $query->where('description', 'like', '%'.$motclef.'%');
            }

            if( ($nospace_cp !== "") ) {
                $query->where('ville', '=', $codepostal);
            }

            if ( ($categorie !== "Toutes catégories" && $categorie !== "-----") ) {
                $categ = Categorie::select('id_categorie')->where('id_categorie', '=', $categorie)->first()->id_categorie;
                $query->where('id_categorie', '=', $categ);
            }

            if ( $prixMin !== "Min" && $prixMax !== "Max") {
                if($prixMax !== "nolimit") {
                    $query->whereBetween('prix', array($prixMin, $prixMax));
                } else {
                    $query->where('prix', '>=', $prixMin);
                }
            } elseif ( $prixMax !== "Max" && $prixMax !== "nolimit") {
                $query->where('prix', '<=', $prixMax);
            } elseif ( $prixMin !== "Min" ) {
                $query->where('prix', '>=', $prixMin);
            }

            $annonce = $query->get();
        }

        echo $twig->render($template, array("breadcrumb" => $menu, "chemin" => $chemin, "annonces" => $annonce, "categories" => $cat));

    }

}

?>
