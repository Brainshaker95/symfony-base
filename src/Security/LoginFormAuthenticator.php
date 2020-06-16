<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    /**
     * @var CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var Session<mixed>
     */
    protected $session;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var UserRepository;
     */
    protected $userRepository;

    /**
     * @param Session<mixed> $session
     */
    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        FormFactoryInterface $formFactory,
        UserPasswordEncoderInterface $passwordEncoder,
        Security $security,
        SessionInterface $session,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        UserRepository $userRepository
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->formFactory      = $formFactory;
        $this->passwordEncoder  = $passwordEncoder;
        $this->security         = $security;
        $this->session          = $session;
        $this->translator       = $translator;
        $this->urlGenerator     = $urlGenerator;
        $this->userRepository   = $userRepository;
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->get('username'),
            'password' => $request->get('password'),
            'token'    => $request->get('_token'),
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['username'];
        $token    = new CsrfToken('authenticate', $credentials['token']);
        $flashBag = $this->session->getFlashBag();

        if (!$username) {
            $flashBag->add('error', 'error.form.login.user.empty');

            return null;
        }

        $this->session->set('last_username', $username);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            $flashBag->add('error', 'error.form.login.token.invalid');

            return null;
        }

        if (!$credentials['password']) {
            $flashBag->add('error', 'error.form.login.password.empty');

            return null;
        }

        /**
         * @var UserInterface|null;
         */
        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            $flashBag->add('error', 'error.form.login.user.not_found');

            return null;
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            return true;
        }

        $flashBag = $this->session->getFlashBag();

        $flashBag->add('error', 'error.form.login.credentials.invalid');

        return false;
    }

    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $user = $this->security->getUser();

        if (!$this->security->isGranted('ROLE_USER', $user)) {
            $flashBag = $this->session->getFlashBag();

            $flashBag->add('info', 'info.user_not_activated');

            return new RedirectResponse($this->urlGenerator->generate('app_index'));
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_profile'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
