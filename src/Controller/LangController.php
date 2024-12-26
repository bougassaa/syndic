<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class LangController extends AbstractController
{
    private SessionInterface $session;

    public function __construct(private RequestStack $requestStack)
    {
        $this->session = $this->requestStack->getSession();
    }

    #[Route('/lang', name: 'app_lang')]
    public function index(Request $request, #[MapQueryParameter] string $lang): Response
    {
        $this->session->set('_locale', $lang);
        return $this->redirect(
            $request->headers->get('referer') ?? $this->generateUrl('app_home')
        );
    }
}
