<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Trick;
use App\Utils\Strings;
use App\Entity\Picture;
use App\Entity\Category;
use App\Entity\Video;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private string $picturesUploadDirectory
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_Fr');

        $user = new User;
        $roles = new ArrayCollection($user->getRoles());
        $roles->add('ROLE_ADMIN');

        $picture = new Picture($this->picturesUploadDirectory);
        $picture->setName('default_profile.jpg')
            ->setFilepath($this->picturesUploadDirectory . '/' . $picture->getName());
        $manager->persist($picture);

        $user->setEmail('admin@snowtrick.com')
            ->setFirstname($faker->firstname)
            ->setLastname($faker->lastname)
            ->setValidationToken('null')
            ->setPassword($this->passwordHasher->hashPassword($user, 'admin'))
            ->setRoles($roles->toArray())
            ->setPictureProfile($picture);

        $manager->persist($user);


        $categoryNames = new ArrayCollection(['Grabs', 'Rotations', 'Flips', 'Rotations désaxées', 'Slides', 'One foot', 'Old school']);
        $trickNames = new ArrayCollection(['Mute', 'Indy', '360', '720', 'Backflip', 'Misty', 'Tail slide', 'Method air', 'Backside air']);
        $categorys = new ArrayCollection();

        foreach ($categoryNames as $name) {
            $category = new Category;
            $category->setName($name);
            $manager->persist($category);
            $categorys->add($category);
        }

        foreach ($trickNames as $key => $name) {
            $picture = new Picture($this->picturesUploadDirectory);
            $picture->setName('default_pictures-' . $key . '.jpg')
                ->setFilepath($this->picturesUploadDirectory . '/' . $picture->getName());
            $manager->persist($picture);

            $video = new Video;
            $video->setLink('<iframe src="https://www.youtube.com/embed/SDdfIqJLrq4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
            $manager->persist($video);

            $trick = new Trick;
            $trick->setName($name)
                ->setSlug(Strings::slug($trick->getName()))
                ->setDescription($faker->text)
                ->addPicture($picture)
                ->addVideo($video)
                ->setCategory($categorys->get(mt_rand(1, $categorys->count() - 1)))
                ->setUser($user);

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
