<?php
namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Menu;

use App\Form\IngredientType;
use App\Form\MenuType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Annotation\Route ;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\Image;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class Index extends AbstractController
{
  #[Route('/accueil', name: 'accueil')]
   /**
     * @Route("/", name="home")
     */
  public function home()
  {
      return $this->render('accueil/accueil.html.twig');
  }
 
    #[Route('/home1',name:'ingredient_list')]

    public function home1(Request $request)
{
    // Création du champ critere
    $form = $this->createFormBuilder()
        ->add("critere", TextType::class)
        // Le bouton "Valider" est retiré de cette ligne
        ->getForm();

    $form->handleRequest($request);
    $em = $this->getDoctrine()->getManager();
    $ingredients = $em->getRepository(Ingredient::class);
    $lesIngredients = $ingredients->findAll();

    // Lancer la recherche quand on clique sur le bouton
    if ($form->isSubmitted()) {
        $data = $form->getData();
        $lesIngredients = $ingredients->recherche($data['critere']);
    }

    return $this->render('ing/index.html.twig', ['lesIngredients' => $lesIngredients, 'form' => $form->createView()]);
}

    /**
     * @Route("/ingredient/new", name="new_ingredient")
     * Method({"GET", "POST"})*/ 
    public function newIngredient(Request $request) {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class,$ingredient);
    
          $form = $this->createFormBuilder($ingredient)
            ->add('libelle', TextType::class)
            ->add('quantite', IntegerType::class)
            ->add('cout', NumberType::class)
            ->add('menu',EntityType::class,['class' => Menu::class,
            'choice_label' => 'titre'])
            ->getForm();
    
         
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()) {
                $ingredient = $form->getData();
      
              $entityManager = $this->getDoctrine()->getManager();
              $entityManager->persist($ingredient);
              $entityManager->flush();
      
              return $this->redirectToRoute('ingredient_list');
            }
            return $this->render('ing/ajout.html.twig',['form' => $form->createView()]);
        }

              /**
     * @Route("/ingredient/{id}", name="ingredient_show")
     */
    public function show($id) {
      $ingredient = $this->getDoctrine()->getRepository(Ingredient::class)->find($id);

      return $this->render('ing/show.html.twig', array('ingredient' => $ingredient));
    }
   
    public function accueil()
    {
        return $this->render('accueil/accueil.html.twig');
      }

/**
     * @Route("/ingredient/edit/{id}", name="edit_ingredient")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id) {
      $ingredient = new Ingredient();
      $ingredient = $this->getDoctrine()->getRepository(Ingredient::class)->find($id);
  
        $form = $this->createFormBuilder($ingredient)
        ->add('libelle', TextType::class)
        ->add('quantite', IntegerType::class)
        ->add('cout', NumberType::class)
        ->add('menu',EntityType::class,['class' => Menu::class,
        'choice_label' => 'titre'])
          ->getForm();
  
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
  
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->flush();
  
          return $this->redirectToRoute('ingredient_list');
        }
  
        return $this->render('ing/edit.html.twig', ['form' => $form->createView()]);
      }

/**
     * @Route("/ingredient/delete/{id}",name="delete_ingredient")
     */
    public function delete(Request $request, $id): Response
    {
        $c = $this->getDoctrine()
            ->getRepository(Ingredient::class)
            ->find($id);
        if (!$c) {
            throw $this->createNotFoundException(
                'pas de ingredient avec id = '.$id
            );
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($c);

        $entityManager->flush();
        return $this->redirectToRoute('ingredient_list');
    }
    
    #[Route('/home2',name:'menu_list')]

    public function home2(Request $request)
{
    $form = $this->createFormBuilder()
        ->add("critere", TextType::class)
        ->getForm();

    $form->handleRequest($request);
    $em = $this->getDoctrine()->getManager();
    $menus = $em->getRepository(Menu::class);
    $lesMenus = $menus->findAll();

    if ($form->isSubmitted()) {
        $data = $form->getData();
        $lesMenus = $menus->recherche($data['critere']);
    }

    return $this->render('menu/index.html.twig', ['lesMenus' => $lesMenus, 'form' => $form->createView()]);
}


      
    /**
     * @Route("/menu/new", name="new_menu")
     * Method({"GET", "POST"})
     */
    public function newMenu(Request $request) {
      $menu = new Menu();
    
      $form = $this->createForm(MenuType::class,$menu);

      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()) {
        $menu = $form->getData();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($menu);
        $entityManager->flush();
        return $this->redirectToRoute('menu_list');
      }
      return $this->render('menu/new.html.twig',['form' => $form->createView()]);
  }

        /**
 * @Route("/menu/{id}", name="menu_show")
 */
public function show2($id) {
    $menu = $this->getDoctrine()->getRepository(Menu::class)->find($id);

    if (!$menu) {
        throw $this->createNotFoundException('Menu non trouvé');
    }

    $lesIngredients = $menu->getIngredients();

    return $this->render('menu/show.html.twig', [
        'menu' => $menu,
        'lesIngredients' => $lesIngredients,
    ]);
}




/**
 * @Route("/menu/delete/{id}", name="delete_menu")
 */
public function delete2(Request $request, $id): Response
{
    $entityManager = $this->getDoctrine()->getManager();

    $menu = $entityManager->getRepository(Menu::class)->find($id);

    if (!$menu) {
        throw $this->createNotFoundException(
            'Pas de menu avec id = ' . $id
        );
    }

    $ingredients = $menu->getIngredient();
    foreach ($ingredients as $ingredient) {
        $entityManager->remove($ingredient);
    }

    $entityManager->remove($menu);

    $entityManager->flush();

    return $this->redirectToRoute('menu_list');
}


/**
 * @Route("/menu/edit/{id}", name="edit_menu")
 * Method({"GET", "POST"})
 */
public function edit2(Request $request, $id)
{
    $entityManager = $this->getDoctrine()->getManager();

    $menu = $entityManager->getRepository(Menu::class)->find($id);

    if (!$menu) {
        throw $this->createNotFoundException(
            'Pas de menu avec id = ' . $id
        );
    }

    $form = $this->createFormBuilder($menu)
        ->add('titre', TextType::class)
        ->add('type', TextType::class)
        ->add('nbrcalories', IntegerType::class)
        ->add('price', NumberType::class)
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('menu_list');
    }

    return $this->render('menu/edit.html.twig', ['form' => $form->createView()]);
}
}