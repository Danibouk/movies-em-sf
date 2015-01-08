<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\User;
use AppBundle\Form\RegistrationType;

class UserController extends Controller
{
    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request)
    {
        //on crée un utilisateur vide (une instance de notre entité User)
        $user = new User();

        //on récupère une instance de notre formulaire
        //ce form est associé à l'utilisateur vide
        $registrationForm = $this->createForm(new RegistrationType(), $user);


        //traite le formulaire
        $registrationForm->handleRequest( $request );

        //si les données sont valides....
        if ( $registrationForm->isValid() ){
            //hydrate les autres propriétés de notre User

                //hacher le mot de passe
                //sha512, 5000 fois

                //générer un salt
                $salt = md5(uniqid());
                $user->setSalt( $salt );

                //générer un token
                $token = md5(uniqid());
                $user->setToken( $token );

                //date d'inscription et date de modification


            //sauvegarde le User en bdd
            dump($user);

        }

        //on shoot le formulaire à twig (on n'oublie pas le createView !)
        $params = array(
            "registrationForm" => $registrationForm->createView()
        );

        return $this->render('user/register.html.twig', $params);
    }
}
