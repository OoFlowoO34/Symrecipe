<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IngredientController extends AbstractController
{
    /**
     * This Controller display all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */

    #[Route('/ingredient', name: 'ingredient.index')]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        // ON PEUT AUSSI ECRIRE CA : $ingredients = $repository->findAll();  

        // dd($ingredients); // Genre de var_dump qui s affiche dans la page internet et affiche l objet

        $ingredients = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/ingredient/index.html.twig', [
            // AVEC CA : 
            'ingredients'=> $ingredients // Passes la variable $ingredients à la vue qu'on appellera dans la vuen en utilisant 'ingredients'

            // OU on peut faire comme ca :'ingredients'=> $repository->findAll()
        ]);
    }

    /**
     * This Controller show a form to create an ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
        ) : Response {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();
            
            $this->addFlash(
                'success',
                'Votre ingrédient a été créé avec succès'
            );

            //return $this->redirectToRoute('app_ingredient');
        }else{

        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form'=> $form->createView()
        ]);
    }


    //public function edit(IngredientRepository $repository, int $id) : Response
    /**
     * This controller allow to edit an ingredient
     *
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods:['GET','POST'])]
    public function edit(
        Ingredient $ingredient, 
        Request $request, 
        EntityManagerInterface $manager
    ) : Response
    {
        //$ingredient = $repository->findOneBy(["id" => $id ]);
        $form = $this->createForm(IngredientType::class, $ingredient);


// Soumission du formulaire
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();
            
            $this->addFlash(
                'success',
                'Votre ingrédient a été modifé avec succès'
            );

            //return $this->redirectToRoute('ingredient.index');
        }else{

        }
//____________________

        return $this->render('pages/ingredient/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods : ['GET'])]
    /**
     * This controller allow to delete an ingedrient
     *
     * @param Ingredient $ingredient
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Ingredient $ingredient, EntityManagerInterface $manager) : Response
    {   
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succès'
        );

        return $this->redirectToRoute('ingredient.index');
    }
}
