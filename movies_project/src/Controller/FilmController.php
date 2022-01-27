<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Doctrine\ORM\EntityManagerInterface;

use App\Entity\Film;
use App\Repository\FilmRepository;
use App\Service\APIFilm;
use App\Service\FileUploader;
use App\Form\FilmType;
use App\Form\FileCSVType;
use App\Form\verifAdminType;
use Psr\Log\LoggerInterface;

class FilmController extends AbstractController
{
    /**
     * @Route("/film", name="film")
     */
    public function index(FilmRepository $repo)
    {
        $films = $repo->findBy([], ['note' => 'DESC','nom' => 'ASC']);

        return $this->render('film/index.html.twig', [
            'films' => $films
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('film/home.html.twig');
    }

    /**
     * @Route("/film/upload", name="upload")
     */
    public function upload()
    {
        return $this->render('film/uploadFile.html.twig');
    }

    /**
     * @Route("/film/doUpload", name="do-upload")
     * @param Request $request
     * @param string $uploadDir
     * @param FileUploader $uploader
     * @param LoggerInterface $logger
     * @return Response
     */
    public function uploadFile(Request $request, string $uploadDir,
                          FileUploader $uploader, LoggerInterface $logger): Response
    {
        $token = $request->get("token");

        if (!$this->isCsrfTokenValid('upload', $token))
        {
            $logger->info("CSRF failure");

            return new Response("Operation not allowed",  Response::HTTP_BAD_REQUEST,
                ['content-type' => 'text/plain']);
        }

        $file = $request->files->get('myfile');

        if (empty($file))
        {
            return new Response("No file specified",
               Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
        }

        return new Response("Bad file extension",
        Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);

        $filename = $file->getClientOriginalName();
        $uploader->upload($uploadDir, $file, $filename);

        return new Response("File uploaded",  Response::HTTP_OK,
            ['content-type' => 'text/plain']);
    }


    /**
     * @Route("/film/picture", name="film_picture")
     */
    public function picture(Film $films = null, Request $request, EntityManagerInterface $manager)
    {
        if(!$films)
        {
            $films = new Film();
        }

        $form = $this->createForm(FilmType::class, $films);

            foreach ($films as $film)
            {
                $manager->persist($film);
                $manager->flush();
            }

        return $this->render('film/picture.html.twig', [
            'formPicture' => $form->createView(),
        ]);
    }

    /**
    * @Route("/film/stats", name="film_stats")
    */
    public function statistiques(FilmRepository $repo)
    {
        $films = $repo->countByNote();

        $notes = [];
        $filmsCount = [];

        foreach($films as $film){
            $notes[] = $film['noteFilm'];
            $filmsCount[] = $film['count'];
        }

        return $this->render('film/stats.html.twig', [
            'filmNom'=>json_encode($notes),
            'filmNote'=>json_encode($filmsCount)
        ]);
    }

    /**
     * @Route("/film/add", name="film_add")
     * @Route("/film/{id}/update", name="film_update")
     */
    public function form(Film $film = null, Request $request, EntityManagerInterface $manager)
    {
        if(!$film)
        {
            $film = new Film();
        }

        $form = $this->createForm(FilmType::class, $film);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {   
            $element=$form->getData();   
            $description = APIFilm::search($element->getNom());  
            
            $film = new Film; 
            $film->setNom($element->getNom());
            $film->setNote($element->getNote());
            $film->setDescription($description);

            $manager->persist($film);
            $manager->flush();

            return $this->redirectToRoute('film_show', ['id' => $film->getId()]);
        }

        return $this->render('film/add.html.twig', [
            'formFilm' => $form->createView(),
            'updateMode' => $film->getId() !== null
        ]);
    }

    /**
     * @Route("/film/{id}/delete", name="film_delete")
     */
    public function delete(?Film $film, Request $request, EntityManagerInterface $manager)
    {
        $codeAdmin = $this->getParameter('codeAdmin');
        $form = $this->createForm(verifAdminType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) { 
            $element=$form->get('codeAdmin'); 
            if ($codeAdmin != $element) {
                printf('code faux');
            }  

            $manager->remove($film);
            $manager->flush();

            return $this->redirectToRoute('film');
        }

        return $this->render('film/delete.html.twig', [
            'formDelete' => $form->createView(),
        ]);

    }

    /**
     * @Route("/film/{id}", name="film_show")
     */
    public function show(Film $film)
    {
      return $this->render('film/show.html.twig', [
            'film' => $film 
        ]);
    }

}
