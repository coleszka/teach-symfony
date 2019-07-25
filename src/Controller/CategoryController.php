<?php


namespace App\Controller;
use App\Entity\Category;

use App\Entity\Notes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @param Request $request
     * @return /Response
     * @Route("/new-category", name="new_category")
     * @throws /Exception
     */
    public function new(Request $request)
    {
        $user = $this->getUser();

        $category = new Category();
        $category->setUser($user);

        $form = $this->createFormBuilder()
            ->add('name', TextType::class, ['required'   => true])
            ->add('description', TextareaType::class, [
                'required'   => true,
                'attr' => [
                    'rows' => 1,
                    'cols' => 30
                ]])
            ->add('color', ColorType::class, [
                'label' => 'Color'])
            ->add('save', SubmitType::class, ['label' => 'Create category'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setName($form['name']->getData());
            $category->setDescription($form['description']->getData());
            $category->setColor($form['color']->getData());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Successful add category: '.$form['name']->getData().'!');
            return $this->redirectToRoute('my_categories');
        }

        return $this->render('category/new_category.html.twig', ['form' => $form->createView(),
        ]);


    }

    /**
     * @return /Response
     * @Route("/categories", name="my_categories")
     */
    public function displayCategories()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy(
            ['User' => $this->getUser()]
        );
        //dump($categories);
        return $this->render('category/display_categories.html.twig', array('categories' => $categories));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/categories/edit/{id}", name="edit_category")
     */

    public function editCategory(Request $request, $id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        if ($this->getUser()->getId() == $category->getUser()->getId()) {

            $form = $this->createFormBuilder($category)
                    ->add('name', TextType::class, ['required' => true])
                    ->add('description', TextareaType::class, [
                        'required'   => true,
                        'attr' => [
                            'rows' => 1,
                            'cols' => 30
                        ]])
                    ->add('color', ColorType::class, [
                        'label' => 'Color'])
                    ->add('save', SubmitType::class, ['label' => 'Edit category'])
                    ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();

                //dump($color);
                $category->setName($form['name']->getData());
                $category->setDescription($form['description']->getData());
                $category->setColor($form['color']->getData());

                $entityManager->persist($category);
                $entityManager->flush();

                $this->addFlash('message', 'Category are edited!');
                return $this->redirectToRoute('my_categories', array('id' => $id));
            }

            return $this->render('category/edit_category.html.twig', [
                'form' => $form->createView()
            ]);
        }
        else {
            $this->addFlash('warning', 'This is not your category!');
            return $this->redirectToRoute('my_categories');
        }
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/categories/delete/{id}", name="delete_category")
     */
    public function deleteCategory($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $note = $this->getDoctrine()->getRepository(Notes::class)->findBy(
            ['category' => $category]
        );
        //dump($note);
        if ($this->getUser()->getId() == $category->getUser()->getId()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            foreach ($note as $notes)
            {
                $em->remove($notes);
            }
            $em->flush();

            $name = $category->getName();

            $this->addFlash('success', 'Category: '.$name.', are deleted!');
            return $this->redirectToRoute('my_categories');
        }
        else{
            $this->addFlash('warning', 'This is not your category!');
            return $this->redirectToRoute('my_categories');
        }
    }

}