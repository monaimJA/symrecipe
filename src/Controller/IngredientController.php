<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $ingredientRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            10/* limit per page */
        );
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }
    #[Route('/ingredient/new', 'ingredient.new', methods: ['GET', 'POST'])]
    public function new (Request $request, EntityManagerInterface $objectManager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $objectManager->persist($ingredient);
            $objectManager->flush();
            $this->addFlash(
                'success',
                'ingrédient a été ajoutée avec succés'
            );
            return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredient, EntityManagerInterface $entityManagerInterface, Request $request):Response
    {

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $entityManagerInterface->persist($ingredient);
            $entityManagerInterface->flush();
            $this->addFlash(
                'success',
                'ingrédient a été modifié avec succés'
            );
            return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('ingredient/suppression/{id}','ingredient.delete',methods:['POST'])]
    public function delete(Ingredient $ingredient,EntityManagerInterface $entityManagerInterface):Response
    {
        if(!$ingredient){
            $this->addFlash('success','l\'ingrédient n\'a pas été trouvé');
            return $this->redirectToRoute('pages/ingredient/index.html.twig');
        }
        $entityManagerInterface->remove($ingredient);
        $entityManagerInterface->flush();
        $this->addFlash('success','l\'ingrédient a été supprimé avec succés');
        return $this->redirectToRoute('pages/ingredient/index.html.twig');
    }
}
