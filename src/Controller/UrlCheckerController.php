<?php

namespace App\Controller;

use App\Form\UrlInputFormType;
use App\Service\UrlChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UrlCheckerController extends AbstractController
{
    #[Route('/check', name: 'app_check')]
    public function index(Request $request, UrlChecker $urlChecker): Response
    {
        $form = $this->createForm(UrlInputFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $form->get('url-string')->getData();
            $keyword = $form->get('keyword')->getData();
            $urlResponseInfo = $urlChecker->checkUrl($url, $keyword);

            return $this->redirectToRoute('app_url_response_info',
                ['urlResponseInfo' => json_encode($urlResponseInfo), 'keyword' => $keyword]);
        }

        return $this->render('default/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/url-response-info/{urlResponseInfo}', name: 'app_url_response_info')]
    public function responseInfo(string $urlResponseInfo): Response
    {
        $urlResponseInfo = json_decode($urlResponseInfo, true);

        return $this->render('default/url_response_info.html.twig', [
            'response_info' => $urlResponseInfo
        ]);
    }
}