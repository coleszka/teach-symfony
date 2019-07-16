<?php


namespace App\Controller;

use App\Entity\Notes;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormTypeInterface;


class NotesController extends AbstractController
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @Route("/new", name="new_note")
     */
    public function new(Request $request)
    {
        $user = $this->getUser();

        $note = new Notes();
        $note->setUser($user);
        $note->setCreateDate(new \DateTime('now'));
        $note->setCategory('kategoria');

        $form = $this->createFormBuilder($note)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Note'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->flush();
        }

        return $this->render('notes/new.html.twig', [
            'form' => $form->createView(),
        ]);




    }



}