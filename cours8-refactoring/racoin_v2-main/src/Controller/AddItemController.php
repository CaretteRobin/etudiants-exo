<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Advert;
use App\Model\Advertiser;
use App\Support\AdvertFormValidator;
use Twig\Environment;

class AddItemController
{
    private AdvertFormValidator $validator;

    public function __construct()
    {
        $this->validator = new AdvertFormValidator();
    }

    public function showForm(Environment $twig, array $menu, string $basePath, array $categories, array $departments): void
    {
        $template = $twig->load('add.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $basePath,
            'categories' => $categories,
            'departements' => $departments,
        ]);
    }

    private function isEmail(string $email): bool
    {
        return (preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email));
    }

    public function store(Environment $twig, array $menu, string $basePath, array $allPostVars): void
    {
        date_default_timezone_set('Europe/Paris');
        $errors = $this->validator->validateCreation($allPostVars);

        // S'il y a des erreurs on redirige vers la page d'erreur
        if (!empty($errors)) {

            $template = $twig->load('add-error.html.twig');
            echo $template->render([
                'breadcrumb' => $menu,
                'chemin' => $basePath,
                'errors' => $errors,
            ]);
        } // sinon on ajoute à la base et on redirige vers une page de succès
        else {
            $annonce   = new Advert();
            $annonceur = new Advertiser();

            $annonceur->email         = htmlentities($allPostVars['email']);
            $annonceur->nom_annonceur = htmlentities($allPostVars['nom']);
            $annonceur->telephone     = htmlentities($allPostVars['phone']);

            $annonce->ville          = htmlentities($allPostVars['ville']);
            $annonce->id_departement = $allPostVars['departement'];
            $annonce->prix           = htmlentities($allPostVars['price']);
            $annonce->mdp            = password_hash($allPostVars['psw'], PASSWORD_DEFAULT);
            $annonce->titre          = htmlentities($allPostVars['title']);
            $annonce->description    = htmlentities($allPostVars['description']);
            $annonce->id_categorie   = $allPostVars['categorie'];
            $annonce->date           = date('Y-m-d');


            $annonceur->save();
            $annonceur->advertisements()->save($annonce);

            $template = $twig->load('add-confirm.html.twig');
            echo $template->render(['breadcrumb' => $menu, 'chemin' => $basePath]);
        }
    }
}
