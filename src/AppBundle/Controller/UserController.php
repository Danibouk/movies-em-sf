<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

use AppBundle\Entity\User;
use AppBundle\Form\RegistrationType;

class UserController extends Controller
{

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                Security::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);

        return $this->render(
            'user/login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }


    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction(){}


    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(){}


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

            //générer un salt
            $salt = md5(uniqid());
            $user->setSalt( $salt );

            //générer un token
            $token = md5(uniqid());
            $user->setToken( $token );

            //hacher le mot de passe
            //sha512, 5000 fois
            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword( $user, $user->getPassword() );
            $user->setPassword( $encoded );

            //date d'inscription et date de modification
            $user->setDateRegistered( new \DateTime() );
            $user->setDateModified( new \DateTime() );

            //assigne toujours ce rôle aux utilisateurs du front-office
            $user->setRoles( array("ROLE_USER") );

            //sauvegarde le User en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist( $user );
            $em->flush();

        }

        //on shoot le formulaire à twig (on n'oublie pas le createView !)
        $params = array(
            "registrationForm" => $registrationForm->createView()
        );

        return $this->render('user/register.html.twig', $params);
    }


    /**
     * Cette page affiche et traite le formulaire où l'on demande son email à l'utilisateur
     * @Route("/forgot-password", name="forgotPassword")
     */
    public function forgotPasswordAction()
    {

        //si soumis, traiter le formulaire contenant l'email

            //si l'email existe en base de donnée

                //envoyer un message contenant un lien vers checkEmailToken
                $message = \Swift_Message::newInstance()
                        ->setCharset("utf-8")
                        ->setSubject('Hello Email')
                        ->setFrom(array('movies@movies.com' => "Movies"))
                        ->setTo('gsylvestre@gmail.com')
                        ->setBody($this->renderView("email/forgot_password_email.html.twig", 
                            array("username" => $username)), "text/html")
                    ;
                $this->get('mailer')->send($message);

                //le prévenir d'aller lire ses emails
                return $this->render("user/lost_password_check_email.html.twig");

            //sinon

                //prévenir l'utilisateur de l'erreur

        return $this->render("user/forgot_password.html.twig");
    }  


    /**
     * L'utilisateur ayant oublié son mdp aboutira sur cette page après avoir cliqué sur le lien reçu par email
     * Cette page redirige toujours vers une autre page
     * @Route("/check-email-token/{email}/{token}", name="checkEmailToken")
     */
    public function checkEmailTokenAction($email, $token)
    {

        
        $userRepo = $this->getDoctrine->getRepository("AppBundle:User");

        //faire une requête en bdd pour récupérer l'utilisateur ayant cet email ET ce token
        //** faire bcp de tests pour s'assurer qu'il n'y a pas de faille **

        //éventuellement, ralentir volontairement ce code pour limiter les attaques en brute force

        //si l'utilisateur est trouvé

            //connecter programmatiquement l'utilisateur trouvé

            //rediriger vers une autre page qui affichera et traitera le formulaire de nouveau mdp

        //sinon

            //le rediriger vers l'accueil ou vers un site pour mécréant


    }   


    /**
     * Cette page affiche et traite le formulaire de changement de mot de passe
     * L'utilisateur doit être connecté pour y accéder
     * Route("/change-password", name="changePassword")
     */
    public function changePasswordAction(){

        return $this->render("user/change_password.html.twig");
    }


}
