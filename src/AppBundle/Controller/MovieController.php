<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MovieController extends Controller
{
    /**
     * @Route("/{page}", name="listMovies", requirements={"page":"\d+"}, defaults={"page":"1"})
     */
    public function listMoviesAction($page)
    {

        $numPerPage = 50;
        $offset = ($page-1)*$numPerPage;

        //récupère les films depuis la bdd
        $movieRepo = $this->getDoctrine()->getRepository("AppBundle:Movie");
        $movies = $movieRepo->findBy(array(), array(
                    "year" => "DESC",
                    "title" => "ASC"
                ), $numPerPage, $offset);

        $moviesNumber = $movieRepo->countAll();

        $maxPages = ceil($moviesNumber/$numPerPage);

        //prépare l'envoi à la vue
        $params = array(
            "currentPage" => $page,
            "numPerPage" => $numPerPage,
            "maxPages" => $maxPages,
            "movies" => $movies,
            "moviesNumber" => $moviesNumber
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