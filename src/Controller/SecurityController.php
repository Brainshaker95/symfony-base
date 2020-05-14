<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegisterType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var LoginFormAuthenticator
     */
    protected $authenticator;

    /**
     * @var AuthenticationUtils
     */
    protected $authenticationUtils;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var GuardAuthenticatorHandler
     */
    protected $guardHandler;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    public function __construct(
        LoginFormAuthenticator $authenticator,
        AuthenticationUtils $authenticationUtils,
        GuardAuthenticatorHandler $guardHandler,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->authenticator       = $authenticator;
        $this->authenticationUtils = $authenticationUtils;
        $this->entityManager       = $entityManager;
        $this->guardHandler        = $guardHandler;
        $this->passwordEncoder     = $passwordEncoder;
    }

    public function registerAction(Request $request): ?Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
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

        return $this->render('security/register.html.twig', [
            'register_form' => $form->createView(),
        ]);
    }

    public function loginAction(Request $request): Response
    {
        $form = $this->createForm(LoginType::class, null, [
            'username' => $request->get('username') ?: '',
        ]);

        return $this->render('security/login.html.twig', [
            'login_form'    => $form->createView(),
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'error'         => $this->authenticationUtils->getLastAuthenticationError(),
        ]);
    }
}
