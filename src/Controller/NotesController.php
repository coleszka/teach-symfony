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
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\Validator\Tests\Constraints\choice_callback;


class NotesController extends AbstractController
{

    /**
     * @param Request $request
     * @return Response
     * @throws /Exception
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

    /**
     *
     * @Route("/mynotes/view/{id}", name="viewNote")
     */
    public function viewNote($id)
    {
        $note = $this->getDoctrine()->getRepository(Notes::class)->find($id);
        //dump($note);
        return $this->render('notes/view_note.html.twig', array('note' => $note));
    }

    /**
     * @param $id
     * @param Request $request
     * @Route("/mynotes/edit/{id}", name="editNote")
     * @return Response
     * @throws /Exception
     */

    public function editNote($id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $note = $this->getDoctrine()->getRepository(Notes::class)->find($id);
        //dump($note);
//        $note->setName($note->getName());
//        $note->setCreateDate($note->getCreateDate());
//        $note->setUser($note->getUser());
        $note->setLastEditDate(new \DateTime('now'));


//        $t = $note->getId();
//        $note1 = new Notes();
        $form = $this->createFormBuilder($note)
            ->add('name', TextType::class, ['required'   => true, ])
            ->add('description', TextareaType::class, [
                'required'   => true,
                'attr' => [
                    'rows' => 1,
                    'cols' => 30,
                ]])

            ->add('category', ChoiceType::class, [
                'choices' => [
                    'PHP' => 'php',
                    'SQL' => 'sql',
                    'Symfony' => 'symfony',
                    'Ubuntu' => 'ubuntu',
                    'Windows' => 'windows',]])
            ->add('save', SubmitType::class, ['label' => 'Edit Note'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $form['name']->getData();
            $description = $form['description']->getData();
            $category = $form['category']->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $note->setName($name);
            $note->setDescription($description);
            $note->setCategory($category);

            $entityManager->persist($note);
            $entityManager->flush();

            $this->addFlash('message', 'Note are edited!');
            return $this->redirectToRoute('viewNote', array('id' => $id));
        }

        return $this->render('notes/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route ("mynotes/delete/{id}", name="delete_note")
     */

    public function deleteNote(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $note = $em->getRepository(Notes::class)->find($id);

        $name = $note->getName();

        $em->remove($note);
        $em->flush();

        $this->addFlash('message', 'Note: '.$name.', are deleted!');
        return $this->redirectToRoute('my_notes');

    }


}