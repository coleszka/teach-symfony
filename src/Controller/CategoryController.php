<?php


namespace App\Controller;
use App\Entity\Category;

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
            return $this->redirectToRoute('new_category');
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

}