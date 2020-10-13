<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Page2Controller extends AbstractController
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    public function __construct(UserRepository $userRepository, SessionInterface $session)
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    public function __invoke(): Response
    {
        $user = $this->session->get("user");
        if (null === $user) {
            return new RedirectResponse("/", Response::HTTP_FOUND);
        }
        $isAdminUser = in_array("ADMIN", $user->getRoles());
        $isUser2 = in_array("PAGE_2", $user->getRoles());
        if (!$isAdminUser && !$isUser2) {
            throw new AccessDeniedHttpException();
        }
        return $this->render("/pages/page2.html.twig", [
            "user" => $user
        ]);
    }
}
