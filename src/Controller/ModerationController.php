<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostModerationType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MODERATOR')]
class ModerationController extends AbstractController
{
    #[Route('/moderation_panel', name: 'app_post_moderation_panel', methods: ['GET'])]
    public function list(PostRepository $postRepository): Response
    {
        return $this->render('post/moderation.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/post/{id}/checkup', name: 'app_post_checkup', methods: ['GET', 'POST'])]
    public function checkup(Request $request, Post $post, PostRepository $postRepository): Response
    {
        $form = $this->createForm(PostModerationType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postRepository->save($post, true);

            return $this->redirectToRoute('app_post_moderation_panel', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/checkup.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }
}
