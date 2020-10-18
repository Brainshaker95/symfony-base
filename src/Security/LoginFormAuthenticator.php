<?php

namespace App\Security;

use App\Traits\HasCsrfTokenManager;
use App\Traits\HasFormFactory;
use App\Traits\HasPasswordEncoder;
use App\Traits\HasRouter;
use App\Traits\HasSecurity;
use App\Traits\HasSession;
use App\Traits\HasTranslator;
use App\Traits\HasUserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use HasCsrfTokenManager;
    use HasFormFactory;
    use HasPasswordEncoder;
    use HasRouter;
    use HasSecurity;
    use HasSession;
    use HasTranslator;
    use HasUserRepository;
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

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

            return new RedirectResponse($this->router->generate('app_index'));
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('app_profile'));
    }

    protected function getLoginUrl()
    {
        return $this->router->generate(self::LOGIN_ROUTE);
    }
}
