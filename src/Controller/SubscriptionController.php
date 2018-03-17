<?php

namespace App\Controller;

use App\Jaar\DataFile;
use App\Form\SubscriptionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SubscriptionController extends Controller
{
    /**
     * @Route("/", name="subscription")
     * @Method("GET")
     * @return Response
     */
    public function index()
    {
        $form = $this->createForm(SubscriptionType::class);

        return $this->render('subscription/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/subscription/save", name="saveSubscription")
     * @Method("POST")
     * @param Request $request
     * @return RedirectResponse
     */
    public function saveSubscription(Request $request)
    {
        $dataFile = new DataFile();

        $request = $request->get('subscription');
        $dataFile->saveSubscription('subscribers.json', $request);

        return $this->redirectToRoute('subscription');
    }
}
