<?php

namespace App\Controller;

//use App\Jaar\DataFile;
use App\Form\SubscriptionType;
use App\Jaar\JaarValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SubscriptionController extends Controller
{
    /**
     * @Route("/", name="subscription")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $datahandler = $this->container->get('app.datahandler');

        // Necessary inject app.datahandler service to SubscriptionType to get array for 'choices'
        $form = $this->createForm(SubscriptionType::class, null, ['datahandler' => $datahandler]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $request = $request->get('subscription');

            // Check if parameter categories is defined in HTML form
            $categories = (isset($request['categories'])) ?? null;
            // Validate
            $validator = new JaarValidator($request['email'], $request['name'], $categories);
            $formErrors = $validator->validate();
            if (null !== $formErrors) {
                return $this->render('subscription/index.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $formErrors,
                ]);
            }
            // If form data is valid, prepare array for saving
            $request = [
                'email' => $request['email'],
                'name' => $request['name'],
                'categories' => $request['categories'],
                'updated_at' => date('Y-m-d H:m:s')
            ];

            $datahandler->saveSubscription('subscribers.json', $request);

            return $this->render('subscription/success.html.twig', ['categories' => $request['categories']]);

        } else {
            return $this->render('subscription/index.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }
}
