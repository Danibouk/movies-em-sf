<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

class MovieController extends Controller
{
    /**
     * @Route("/{page}", name="listMovies", requirements={"page":"\d+"}, defaults={"page":"1"})
     */
    public function listMoviesAction(Request $request, $page)
    {

        $minYear = $request->query->get("minYear") ? $request->query->get("minYear") : 1900;
        $maxYear = $request->query->get("maxYear") ? $request->query->get("maxYear") : date("Y");

        $numPerPage = 50;
        $offset = ($page-1)*$numPerPage;

        //récupère les films depuis la bdd
        $movieRepo = $this->getDoctrine()->getRepository("AppBundle:Movie");

        $moviesNumber = $movieRepo->countAll($minYear, $maxYear);
        $maxPages = ceil($moviesNumber/$numPerPage);

        //si l'utilisateur a déconné avec l'url...
        //page trop grande : on le redirige vers la dernière page
        if ($page > $maxPages){
            return $this->redirect( 
                $this->generateUrl( "listMovies", array("page" => $maxPages) ) 
            );
        }
        //à l'inverse, page trop petite : 
        //si sur la page "0" par exemple...
        elseif ($page < 1){
            return $this->redirect( 
                $this->generateUrl( "listMovies", array("page" => 1) ) 
            );
        }

        $movies = $movieRepo->findByYear($minYear, $maxYear, $numPerPage, $offset);
        $moviesNumber = count($movies);

        //prépare l'envoi à la vue
        $params = array(
            "currentPage" => $page,
            "numPerPage" => $numPerPage,
            "maxPages" => $maxPages,
            "movies" => $movies,
            "moviesNumber" => $moviesNumber,
            "minYear" => $minYear,
            "maxYear" => $maxYear
        );

        //envoie la 
        return $this->render('movie/list_movies.html.twig', $params);
    }


    /**
     * @Route("/movie/{id}", name="movieDetails") 
     */
    public function detailsAction($id){
        //récupère le film depuis la bdd, en fonction de son id (présent dans l'URL)
        $movieRepo = $this->getDoctrine()->getRepository("AppBundle:Movie");
        $movie = $movieRepo->find($id);

        $params = array(
            "movie" => $movie
        );

        //envoie la vue, en lui passant les variables
        return $this->render('movie/movie_details.html.twig', $params);    
    }

}
