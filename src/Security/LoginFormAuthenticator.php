<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    const CSRF_TOKEN = 'csrf_token';
    const EMAIL = 'email';
    const PASSWORD = 'password';

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * LoginFormAuthenticator constructor.
     * @param UserRepository $userRepository
     * @param RouterInterface $router
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserRepository $userRepository, RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
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
            self::PASSWORD => $request->request->get(self::PASSWORD),
            self::CSRF_TOKEN => $request->request->get('_'.self::CSRF_TOKEN),
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
        //authenticate is the value used in login.html.twig
        $token = new CsrfToken('authenticate', $credentials[self::CSRF_TOKEN]);
        if(!$this->csrfTokenManager->isTokenValid($token)){
            throw new InvalidCsrfTokenException();
        }

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
        return $this->passwordEncoder->isPasswordValid($user, $credentials[self::PASSWORD]);
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
        if($targetPath = $this->getTargetPath($request->getSession(), $providerKey)){
            return new RedirectResponse($targetPath);
        }

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
