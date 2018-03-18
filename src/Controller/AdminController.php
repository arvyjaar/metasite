<?php

namespace App\Controller;

use App\Jaar\DataFile;
use App\Jaar\JaarValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     * @Method("GET")
     * @return Response
     */
    public function index()
    {
        $user = $this->getUser();
        if (! $user) {
            throw new AccessDeniedException();
        }
        $dataFile = new DataFile();
        $subscribers = $dataFile->getContent('subscribers.json');

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
        $user = $this->getUser();
        if (! $user) {
            throw new AccessDeniedException();
        }

        $submittedToken = $request->request->get('token');

        if ($submittedToken && $this->isCsrfTokenValid('adm_delete', $submittedToken)) {
            $dataFile = new DataFile();
            $dataFile->delete('subscribers.json', $id);

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
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $user = $this->getUser();
        if (! $user) {
            throw new AccessDeniedException();
        }

        $dataFile = new DataFile();
        $subscriber = $dataFile->getItem('subscribers.json', $id);

        $submittedToken = $request->request->get('token');

        if ($submittedToken && $this->isCsrfTokenValid('adm_edit', $submittedToken)) {
            // Check if parameter categories is defined in HTML form
            $request = $request->get('subscription');
            $categories = (isset($request['categories'])) ?? null;
            // Validate
            $validator = new JaarValidator($request['email'], $request['name'], $categories);
            $formErrors = $validator->validate();
            if (null !== $formErrors) {
                return $this->render('admin/edit.html.twig', [
                    'id' => $id,
                    'subscriber' => $subscriber,
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

            $dataFile->saveSubscription('subscribers.json', $request);

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
        }
    }
}
