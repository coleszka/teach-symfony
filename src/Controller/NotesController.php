<?php


namespace App\Controller;

use App\Entity\Notes;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormTypeInterface;
use function Symfony\Component\Validator\Tests\Constraints\choice_callback;


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


        $form = $this->createFormBuilder($note)
            ->add('name', TextType::class, ['required'   => true])
            ->add('description', TextareaType::class, [
                'required'   => true,
                'attr' => [
                    'rows' => 1,
                    'cols' => 30
                ]])

            ->add('category', ChoiceType::class, [
                'choices' => [
                    'PHP' => 'php',
                    'SQL' => 'sql',
                    'Symfony' => 'symfony',
                    'Ubuntu' => 'ubuntu',
                    'Windows' => 'windows',]])
            ->add('save', SubmitType::class, ['label' => 'Create Note'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute('my_notes');
        }

        return $this->render('notes/new.html.twig', [
            'form' => $form->createView(),
        ]);




    }



}