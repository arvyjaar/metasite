<?php

namespace App\Controller;

use App\Entity\Subscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Validator\ValidatorInterface;
Use App\Form\SubscriptionType;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     * @Method("GET")
     * @return Response
     */
    public function index()
    {
        $datahandler = $this->container->get('app.datahandler');
        $subscribers = $datahandler->getContent('subscribers.json');

        return $this->render('admin/index.html.twig', [
            'subscribers' => $subscribers,
        ]);
    }

    /**
     * @Route("/admin/delete/{id}", name="delete")
     * @param string $id
     * @Method("POST")
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request, $id)
    {
        $submittedToken = $request->request->get('token');

        if ($submittedToken && $this->isCsrfTokenValid('adm_delete', $submittedToken)) {
            $datahandler = $this->container->get('app.datahandler');
            $datahandler->delete('subscribers.json', $id);

            $this->addFlash(
                'notice',
                'Subscriber deleted!'
            );

            return $this->redirectToRoute('admin');
        }
        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/edit/{id}", name="edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param string $id
     * @param ValidatorInterface $validator
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, $id, ValidatorInterface $validator)
    {
        $datahandler = $this->container->get('app.datahandler');
        $subscriber = $datahandler->getItem('subscribers.json', $id);

        $form = $this->createForm(SubscriptionType::class, $subscriber, ['datahandler' => $datahandler]);
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
                return $this->render('admin/edit.html.twig', [
                    'form' => $form->createView(),
                    'subscriber' => $subscriber,
                    'errors' => $errors,
                ]);
            }

            $datahandler->saveSubscription('subscribers.json', $subscriber);

            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('admin');

        } else {
            return $this->render('admin/edit.html.twig', [
                'subscriber' => $subscriber,
                'form' => $form->createView()
            ]);
        }

//form
/*        $submittedToken = $request->request->get('token');

        if ($submittedToken && $this->isCsrfTokenValid('adm_edit', $submittedToken)) {
            $fields = $request->get('subscription');

            $subscriber = new Subscriber();
            $subscriber->setEmail($fields['email'] ?? null);
            $subscriber->setName($fields['name'] ?? null);
            $subscriber->setCategories($fields['categories'] ?? []);
            $subscriber->setUpdatedAt(date('Y-m-d H:m:s'));
            
            $errors = $validator->validate($subscriber);
            $existingCategories = $datahandler->getContent('categories.json');
            foreach($fields['categories'] as $category) {
                if (! in_array($category, $existingCategories)) {
                    throw new \Exception('Submited value is not from categores array!');
                }
            }
            if (count($errors) > 0) {
                return $this->render('admin/edit.html.twig', [
                    'id' => $id,
                    'subscriber' => $subscriber,
                    'errors' => $errors,
                ]);
            }
            // Validation passed
            $datahandler->saveSubscription('subscribers.json', $subscriber);

            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('admin');
        } else {
            return $this->render('admin/edit.html.twig', [
                'id' => $id,
                'subscriber' => $subscriber,
            ]);
        }*/
    }
}
