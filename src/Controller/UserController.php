<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    /**
     * Pour l'enregistrement d'un nouvel utilisateur, nous ne pouvons insérer le mdp en clair en BDD.
     * Pour cela, Symfony nous fournit un outil pour hasher (encrypter) le password.
     * Pour l'utiliser, nous avons jute à l'injecter comme dépendance (de notre fonction).
     * L'injection de dépendance se fait entre les parenthèses de la fonction.
     * 
     * @Route("/inscription", name="user_register", methods={"GET|POST"})
     */
    public function register(Request $request, EntityManagerInterface $entityManger, UserPasswordHasherInterface $passwordHasher): Response
    {   
        # on cree une nouvelle...
        $user = new User();

        $form = $this->createForm(UserFormType::class, $user)
            ->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                // Nous settons les propriétés qui ne sont pas dans le form et donc auto-hydratées.
                // les propriétés createdAt et updatedAt attendent un objet de type DateTime().
                $user->setCreatedAt(new DateTime());
                $user->setUpdatedAt(new DateTime());
                // Pour assurer un rôle utilisateur à tous les utilisateurs, on set le role égalment.
                $user->setRoles(['ROLE_USER']);

                # on récupère la valeur de l'input 'password' dans le formulaire
                $plainPassword = $form->get('password')->getData();

                # On reset le password du user en le hachant.
                # Pour hasher, on utiliser l'outil de hashage qu'on a injecté dans notre action.
                 $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user, $plainPassword
                 )
                );
                # Notre user est 
                $entityManger->persist($user);
                $entityManger->flush();
                # On peut enfin return et rediriger l'utilisateur la où on le souhaite.
                return $this->redirectToRoute('default_home');


            }
            # On rend la vue qui contient le formulaire d'inscription
            return $this->render("user/register.html.twig", [
                'form_register' => $form->createView()
            ]);
    }
}
