<?php


namespace App\Controller;

use App\Entity\Notes;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\Validator\Tests\Constraints\choice_callback;
use Symfony\Component\Validator\Tests\Fixtures\Entity;



class NotesController extends AbstractController
{

    /**
     * @param Request $request
     * @return Response
     * @throws /Exception
     * @Route("/new-note", name="new_note")
     */
    public function new(Request $request)
    {
        $user = $this->getUser();

        $note = new Notes();
        $note->setUser($user);
        $note->setCreateDate(new \DateTime('now'));

//        $category = new Category();
//        $category = $this->getDoctrine()->getRepository(Category::class)->findById(1);
        //dump($category);
        $form = $this->createFormBuilder($note)
            ->add('name', TextType::class, ['required'   => true])
            ->add('description', TextareaType::class, [
                'required'   => true,
                'attr' => [
                    'rows' => 1,
                    'cols' => 30
                ]])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'])
            ->add('save', SubmitType::class, ['label' => 'Create Note'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->flush();

            $this->addFlash('success', 'Successful add note!');
            return $this->redirectToRoute('my_notes');
        }

        return $this->render('notes/new.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     *
     * @Route("/my-notes/view/{id}", name="viewNote")
     */
    public function viewNote($id)
    {
        $note = $this->getDoctrine()->getRepository(Notes::class)->find($id);
        //dump($note);
        if ($this->getUser()->getId() == $note->getUser()->getId()) {

            return $this->render('notes/view_note.html.twig', array('note' => $note,
            'userId' => $note->getUser()->getId()));
        }
        else {
            $this->addFlash('warning', 'This is not your note!');
            return $this->redirectToRoute('my_notes');
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @Route("/my-notes/edit/{id}", name="editNote")
     * @return Response
     * @throws /Exception
     */

    public function editNote($id, Request $request)
    {

        $note = $this->getDoctrine()->getRepository(Notes::class)->find($id);
        //dump($note);
        if ($this->getUser()->getId() == $note->getUser()->getId()) {

            $note->setLastEditDate(new \DateTime('now'));

            $form = $this->createFormBuilder($note)
                ->add('name', TextType::class, ['required'   => true, ])
                ->add('description', TextareaType::class, [
                    'required'   => true,
                    'attr' => [
                        'rows' => 1,
                        'cols' => 30,
                    ]])

                ->add('category', EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'name'])
                ->add('save', SubmitType::class, ['label' => 'Edit Note'])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();

                $note->setName($form['name']->getData());
                $note->setDescription($form['description']->getData());
                $note->setCategory($form['category']->getData());

                $entityManager->persist($note);
                $entityManager->flush();

                $this->addFlash('message', 'Note are edited!');
                return $this->redirectToRoute('viewNote', array('id' => $id));
            }

            return $this->render('notes/edit.html.twig', [
                'form' => $form->createView(), 'userId' => $note->getUser()->getId()
            ]);

        }
        else {
            $this->addFlash('warning', 'This is not your note!');
            return $this->redirectToRoute('my_notes');
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route ("my-notes/delete/{id}", name="delete_note")
     */

    public function deleteNote($id)
    {

        $note = $this->getDoctrine()->getRepository(Notes::class)->find($id);

        if ($this->getUser()->getId() == $note->getUser()->getId()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($note);
            $em->flush();

            $name = $note->getName();

            $this->addFlash('message', 'Note: '.$name.', are deleted!');
            return $this->redirectToRoute('my_notes');
        }
        else {
            $this->addFlash('warning', 'This is not your note!');
            return $this->redirectToRoute('my_notes');
        }
    }

}


