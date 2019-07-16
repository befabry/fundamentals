<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    const EMAIL = 'email';

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * LoginFormAuthenticator constructor.
     * @param UserRepository $userRepository
     * @param RouterInterface $router
     */
    public function __construct(UserRepository $userRepository, RouterInterface $router)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    /**
     * Called at the start of every request
     * If false => nothing else happens, back to the controller
     * If true => calls getCredentials
     *
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    /**
     * Retrieve the data from the post login form, then calls getUser
     *
     * @param Request $request
     * @return array<string>
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            self::EMAIL =>  $request->request->get(self::EMAIL),
            'password' => $request->request->get('password'),
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials[self::EMAIL]
        );

        return $credentials;
    }

    /**
     * If it returns null, the authentication process stop and throw an error
     * If an User is fetched, it calls checkCredentials
     *
     * @param array $credentials
     * @param UserProviderInterface $userProvider
     * @return \App\Entity\User|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $this->userRepository->findOneBy([self::EMAIL => $credentials[self::EMAIL]]);
    }

    /**
     * Check if the credentials are valid
     *
     * @param array $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * Redirect to the homepage after the login
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->router->generate('app_homepage'));
    }

    /**
     * Return the URL to the login page.
     *
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->router->generate('app_login');
    }
}
