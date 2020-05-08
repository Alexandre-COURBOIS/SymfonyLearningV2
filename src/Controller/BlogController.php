<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;


class BlogController extends AbstractController
{

    /**
     * @Route("/blog", name="blog")
     */

    public function index(ArticleRepository $repository)
    {

        $articles = $repository->findAll();


        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/", name="home")
     */

    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'titre' => 'Bienvenue sur mon blog',
        ]);
    }


    /**
     * @Route ("/blog/new", name="blog_create")
     * @Route ("/blog/{id}/edit", name="blog_edit")
     */

    public function form(Article $article = null, Request $request,EntityManagerInterface $manager)
    {

        if (!$article) {
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()){
            $article->setCreatedAt(new \DateTime());
            } elseif ($article->getId()){
                $article->setModifiedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        dump($article);

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null,
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */

    public function show(UserInterface $user = null,Article $article, Comment $comment = null, Request $request, EntityManagerInterface $manager)
    {

        dump($comment);

        if (!$comment) {
            $comment = new Comment();
        }

        $form2 = $this->createForm(CommentType::class, $comment);

        $form2->handleRequest($request);

        if($form2->isSubmitted() && $form2->isValid()) {
            if (!$comment->getId()) {
                $comment->setCreatedAt(new \DateTime())
                        ->setArticle($article)
                        ->setAuthor($user->getUsername());
            }

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }


        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'formComment' => $form2->createView(),
        ]);
    }

}
