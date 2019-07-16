<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(100,'main_comments', function () {
            $comment = new Comment();

            $comment
                ->setContent(
                    $this->faker->boolean ? $this->faker->paragraph : $this->faker->sentences(2, true)
                )
                ->setAuthorName($this->faker->name)
                ->setCreatedAt($this->faker->dateTimeBetween('-1 months', 'now'))
                ->setIsDeleted($this->faker->boolean(20))
                ->setArticle($this->getRandomReference('main_articles'));

            return $comment;
        });

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            ArticleFixtures::class,
        ];
    }
}
