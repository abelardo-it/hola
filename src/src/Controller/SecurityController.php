<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var SessionInterface
     */
    private SessionInterface $session;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(
        UserRepository $userRepository,
        SessionInterface $session,
        UserPasswordEncoderInterface $passwordEncoder) {
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function login(): Response
    {
        return $this->render("/security/login.html.twig");
    }

    public function __invoke(Request $request): Response
    {
        $response = new Response();
        $userName = $request->request->get("username");
        $user = $this->userRepository->findOneBy(['username'=>  $userName]);

        if (null === $user) {
            return new RedirectResponse("/", Response::HTTP_FOUND);
        }

        if (in_array("ADMIN", $user->getRoles(), true)) {
            return $this->redirectToAdminPage($request, $user);
        }

        if (in_array("PAGE_1", $user->getRoles(), true) || in_array("PAGE_2", $user->getRoles(), true)) {
            return $this->redirectToNonAdminUser($request, $user);
        }

         return new RedirectResponse("/", Response::HTTP_FOUND);;
    }

    private function redirectToAdminPage(Request $request, User $adminUser): Response
    {
        //adminpassword
        $password = $request->request->get("password");
        if ($this->passwordEncoder->isPasswordValid($adminUser, $password)) {
            $this->session->set("user", $adminUser);
            return new RedirectResponse("admin", Response::HTTP_FOUND);
        }
        $this->addFlash('warning', 'La password no es correcta');
        return $this->redirectToRoute("login_path");
    }

    /**
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    private function redirectToNonAdminUser(Request $request, User $user): RedirectResponse
    {

        $password = $request->request->get("password");
        if ($this->passwordEncoder->isPasswordValid($user, $password)) {

            $isAdminUser = (in_array("ADMIN", $user->getRoles(), true));
            $isUser1= (in_array("PAGE_1", $user->getRoles(), true));
            $isUser2 = (in_array("PAGE_2", $user->getRoles(), true));

            $this->session->set("user", $user);

            if ($isAdminUser || $isUser1) {
                return new RedirectResponse("page/1", Response::HTTP_FOUND);
            }
            if ($isAdminUser || $isUser2) {
                return new RedirectResponse("page/2", Response::HTTP_FOUND);
            }
        }

        $this->addFlash(
            'warning',
            'La password no es correcta'
        );
        return $this->redirectToRoute("login_path");
    }

    /**
     * @Route("/logout", name="app_logout")
     * @throws Exception
     */
    public function logout(): void
    {
        throw new RuntimeException('Will be intercepted before getting here');
    }
}
