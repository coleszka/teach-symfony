<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Notes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class DisplayNotesController extends AbstractController
{
    /**
     * @return Response
     * @Route("/my-notes", name="my_notes")
     *
     */
    public function displayNotes()
    {
        $notes = $this->getDoctrine()->getRepository(Notes::class)->findBy(
            ['user' => $this->getUser()]
        );
        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy(
            ['User' => $this->getUser()]
        );
//        dump($notes);
//        dump($categories);
        return $this->render('notes/display_notes.html.twig',
            array('notes' => $notes, 'categories' => $categories));
    }
}