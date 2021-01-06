<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $video = new Video();
        $video->setUser($this->getUser());

        $form = $this->createForm(VideoType::class, $video);
        $form->add('create', SubmitType::class, ['attr' => ['class' => 'btn-success']]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->get('create')->isClicked() and $form->isValid()) {
                $em->persist($form->getData());
                $em->flush();

                $this->addFlash('success', 'Видео добавлено');

                //$this->eventDispatcher->dispatch($video, 'my_event_name');

                return $this->redirectToRoute('profile');
            }
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
            'videos' => $em->getRepository(Video::class)->findAllByUser($this->getUser()),
        ]);
    }
}
