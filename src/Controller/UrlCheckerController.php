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
        //creating form
        $form = $this->createForm(UrlInputFormType::class);
        $form->handleRequest($request);


        //Checking if form was submitted and data is valid for next steps
        if ($form->isSubmitted() && $form->isValid()) {
            $url = $form->get('url')->getData();
            $keyword = $form->get('keyword')->getData();
            $urlResponseInfo = $urlChecker->checkUrl($url, $keyword);
            if(empty($urlResponseInfo)){
                $this->addFlash('empty', 'Please provide valid URL');
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            }

            //passing data in json
            return $this->render('app_url_response_info',
                ['urlResponseInfo' => json_encode($urlResponseInfo)]);
        }

        //passing data to the template
        return $this->render('url/index.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/url-response-info/{urlResponseInfo}', name: 'app_url_response_info')]
    public function responseInfo(string $urlResponseInfo): Response
    {
        //decoding received data and sending to template
        $urlResponseInfo = json_decode($urlResponseInfo, true);

        return $this->render('url/url_response_info.html.twig', [
            'response_info' => $urlResponseInfo
        ]);
    }
}