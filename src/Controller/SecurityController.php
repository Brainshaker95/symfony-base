<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\LoginType;
use App\Form\Type\RegisterType;
use App\Traits\HasAuthenticator;
use App\Traits\HasEntityManager;
use App\Traits\HasGuardHandler;
use App\Traits\HasPasswordEncoder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends FrontendController
{
    use HasAuthenticator;
    use HasEntityManager;
    use HasGuardHandler;
    use HasPasswordEncoder;

    public function registerAction(Request $request): ?Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user
                ->setTheme('dark')
                ->setPassword(
                    $this->passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->authenticator,
                'main'
            );
        }

        return $this->render('page/register.html.twig', [
            'register_form' => $form->createView(),
        ]);
    }

    public function loginAction(Request $request): Response
    {
        $form = $this->createForm(LoginType::class, null, [
            'username' => $request->getSession()->get('last_username'),
        ]);

        return $this->render('page/login.html.twig', [
            'login_form' => $form->createView(),
        ]);
    }
}
