<?php

namespace App\Controller;

use App\Entity\Subscriber;
use App\Form\SubscriptionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionController extends Controller
{
    /**
     * @Route("/", name="subscription")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, ValidatorInterface $validator)
    {
        $datahandler = $this->container->get('app.datahandler');

        // It's mandatory inject app.datahandler service to SubscriptionType to get array for 'choices'
        $form = $this->createForm(SubscriptionType::class, null, ['datahandler' => $datahandler]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fields = $request->get('subscription');

            $subscriber = new Subscriber();
            $subscriber->setEmail($fields['email'] ?? null);
            $subscriber->setName($fields['name'] ?? null);
            $subscriber->setCategories($fields['categories'] ?? []);
            $subscriber->setUpdatedAt(date('Y-m-d H:m:s'));
            
            $errors = $validator->validate($subscriber);
            if (count($errors) > 0) {
                return $this->render('subscription/index.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $errors,
                ]);
            }

            $datahandler->saveSubscription('subscribers.json', $subscriber);

            return $this->render('subscription/success.html.twig', ['categories' => $fields['categories']]);

        } else {
            return $this->render('subscription/index.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }
}
