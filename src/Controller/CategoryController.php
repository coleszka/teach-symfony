<?php


namespace App\Controller;
use App\Entity\Category;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{

    public function new(Request $request)
    {
        $user = $this->getUser();

        $category = new Category();


    }

}