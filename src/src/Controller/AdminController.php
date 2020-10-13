<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
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

    /**
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        //adminpassword
        $userAdmin = $this->session->get("user");
        if (null === $userAdmin) {
            return new RedirectResponse("/", Response::HTTP_FOUND);
        }
        if (!in_array("ADMIN", $userAdmin->getRoles(), true)) {
             throw new AccessDeniedHttpException();
        }
        return $this->render("/admin/index.html.twig", ["user" => $userAdmin]);
    }


}
