<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        // créer trois catégories
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence())
                ->setDescription($faker->paragraph());

            $manager->persist($category);

            // créer des articles entre 4 et 6 par exemple

            $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

            for ($j = 1; $j <= mt_rand(4, 6); $j++) {
                $article = new Article();
                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-7 days'))
                    ->setCategory($category);

                $manager->persist($article);


                // Créer des commentaires

                $contentComment = join($faker->paragraphs(2), '</p><p>') . '</p>';

                $now = new \DateTime();
                $interval = $now->diff($article->getCreatedAt());
                $days = $interval->days;
                $min = '-' . $days . 'days';

                for ($k = 1; $k <= mt_rand(4, 10); $k++) {
                    $comment = new Comment();
                    $comment->setAuthor($faker->name())
                        ->setContent($contentComment)
                        ->setCreatedAt($faker->dateTimeBetween($min))
                        ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }


        $manager->flush();
    }
}
