<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\Manager;
use App\Entity\Species;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $species = new Species();
        $species->setName("Mammals");
        $species->setAbout("Mammals include humans and all other animals that are warm-blooded vertebrates (vertebrates have backbones) with hair. They feed their young with milk and have a more well-developed brain than other types of animals.");
        $manager->persist($species);

        $specialist = new Manager();
        $specialist->setName("John");
        $specialist->setSurname("Smith");
        $specialist->setAbout("A zookeeper is someone who works in a zoo. More specifically, it is someone who cares for animals in the zoo. They are responsible for the feeding and daily care of the animals. They clean out the exhibitions and report health problems. They answer questions about the animals. They sometimes give a demonstration of how to care for or feed the animals.");
        $specialist->setSpecies($species);
        $manager->persist($specialist);

        $animal = new Animal();
        $animal->setName("Amur Leopard");
        $date = new DateTime('2000-01-01');
        $animal->setBirthYear($date);
        $animal->setAnimalBook("Slowly stalking down the snowy hillside, the Amur leopard watches its prey through the trees. In the clearing below, a sika deer munches on tree bark, one of its few remaining food sources during the cold Russian winter. The leopard crouches, its body so low to the ground that its belly fur brushes the snow. Suddenly it bounds and springs forward, tackling the deer from 10 feet away. It’s dinnertime.");
        $animal->setSpecies($species);
        $animal->setManager($specialist);
        $manager->persist($animal);

        $animal = new Animal();
        $animal->setName("Asiatic Lion");
        $date = new DateTime('2010-01-01');
        $animal->setBirthYear($date);
        $animal->setAnimalBook("Lion prides can be as small as 3 or as big as 40 animals. In a pride, lions hunt prey, raise cubs, and defend their territory together. In prides the females do most of the hunting and cub rearing. Usually all the lionesses in the pride are related—mothers, daughters, grandmothers, and sisters.");
        $animal->setSpecies($species);
        $animal->setManager($specialist);
        $manager->persist($animal);

        $animal = new Animal();
        $animal->setName("African Elephant");
        $date = new DateTime('2011-01-01');
        $animal->setBirthYear($date);
        $animal->setAnimalBook("When an elephant drinks, it sucks as much as 2 gallons (7.5 liters) of water into its trunk at a time. Then it curls its trunk under, sticks the tip of its trunk into its mouth, and blows. Out comes the water, right down the elephant's throat. Since African elephants live where the sun is usually blazing hot, they use their trunks to help them keep cool. First they squirt a trunkful of cool water over their bodies. Then they often follow that with a sprinkling of dust to create a protective layer of dirt on their skin. Elephants pick up and spray dust the same way they do water—with their trunks.");
        $animal->setSpecies($species);
        $animal->setManager($specialist);
        $manager->persist($animal);

        ///////

        $species = new Species();
        $species->setName("Birds");
        $species->setAbout("Birds are warm-blooded vertebrates (vertebrates have backbones) and are the only animals with feathers. Although all birds have wings, a few species can't fly.");
        $manager->persist($species);

        $specialist = new Manager();
        $specialist->setName("Marry");
        $specialist->setSurname("Jane");
        $specialist->setAbout("A zookeeper is someone who works in a zoo. More specifically, it is someone who cares for animals in the zoo. They are responsible for the feeding and daily care of the animals. They clean out the exhibitions and report health problems. They answer questions about the animals. They sometimes give a demonstration of how to care for or feed the animals.");
        $specialist->setSpecies($species);
        $manager->persist($specialist);

        $animal = new Animal();
        $animal->setName("Bald Eagle");
        $date = new DateTime('2013-01-01');
        $animal->setBirthYear($date);
        $animal->setAnimalBook('A bald eagles white head may make it look bald. But actually the name comes from an old English word, balde, meaning white. These graceful birds have been the national symbol of the United States since 1782.');
        $animal->setSpecies($species);
        $animal->setManager($specialist);
        $manager->persist($animal);

        $animal = new Animal();
        $animal->setName("American Goldfinch");
        $date = new DateTime('2008-01-01');
        $animal->setBirthYear($date);
        $animal->setAnimalBook("An American goldfinch soars through the warm spring air, it’s yellow feathers reflecting the sun. Suddenly the bird opens its mouth and chirps a call that sounds like “po-ta-to-chip.” This flier isn’t looking for a salty snack. It’s using this vocalization to communicate with its flock. The bird flies on, continuing its delicious call.");
        $animal->setSpecies($species);
        $animal->setManager($specialist);
        $manager->persist($animal);

        $animal = new Animal();
        $animal->setName("American Crow");
        $date = new DateTime('2001-01-01');
        $animal->setBirthYear($date);
        $animal->setAnimalBook("American crows range from southern Canada throughout the United States. As an adult, this bird is entirely black from bill to tail, except for its brown eyes. Adult crow feathers have a glossy sheen. These noisy birds are often recognizable by their distinctive, loud cry, called a caw. They are often mistaken for the common raven, but ravens are larger, have differently shaped bills, pointed wings and tails, and hoarser cries.");
        $animal->setSpecies($species);
        $animal->setManager($specialist);
        $manager->persist($animal);

        ///////

        $species = new Species();
        $species->setName("Reptiles");
        $species->setAbout("Reptiles are cold-blooded vertebrates. (Vertebrates have backbones.) They have dry skin covered with scales or bony plates and usually lay soft-shelled eggs.");
        $manager->persist($species);

        $specialist = new Manager();
        $specialist->setName("Steve");
        $specialist->setSurname("Ho");
        $specialist->setAbout("A zookeeper is someone who works in a zoo. More specifically, it is someone who cares for animals in the zoo. They are responsible for the feeding and daily care of the animals. They clean out the exhibitions and report health problems. They answer questions about the animals. They sometimes give a demonstration of how to care for or feed the animals.");
        $specialist->setSpecies($species);
        $manager->persist($specialist);

        $animal = new Animal();
        $animal->setName("Anaconda");
        $date = new DateTime('2013-01-01');
        $animal->setBirthYear($date);
        $animal->setAnimalBook('The green anaconda is the largest snake in the world, when both weight and length are considered. It can reach a length of 30 feet (9 meters) and weigh up to 550 pounds (227 kilograms). To picture how big that is, if about five ten-year-olds lie down head to foot, they be about the length of this huge snake.');
        $animal->setSpecies($species);
        $animal->setManager($specialist);
        $manager->persist($animal);

        $animal = new Animal();
        $animal->setName("Chameleon");
        $date = new DateTime('2009-01-01');
        $animal->setBirthYear($date);
        $animal->setAnimalBook("A chameleon sits motionlessly on a tree branch. Suddenly its sticky, two-foot-long tongue snaps out at 13 miles an hour, wrapping around a cricket and whipping the yummy snack back into the reptile’s mouth. Now that’s fast food dining! And the chameleon’s swift eating style is just one of its many features that’ll leave you tongue-tied.");
        $animal->setSpecies($species);
        $animal->setManager($specialist);
        $manager->persist($animal);

        $animal = new Animal();
        $animal->setName("Burmese Python");
        $date = new DateTime('2000-01-01');
        $animal->setBirthYear($date);
        $animal->setAnimalBook("Burmese pythons, one of the largest snakes in the world, are best known for the way they catch and eat their food. The snake uses its sharp rearward-pointing teeth to seize prey, and then coils its body around the animal, squeezing a little tighter with each exhale until the animal suffocates. Stretchy ligaments in their jaws allow them to swallow animals up to five times as wide as their head!");
        $animal->setSpecies($species);
        $animal->setManager($specialist);
        $manager->persist($animal);

        ///////

        $species = new Species();
        $species->setName("Fishes");
        $species->setAbout("Fish are vertebrates (vertebrates have backbones) that live in water. They breathe using special organs called gills.");
        $manager->persist($species);

        $specialist = new Manager();
        $specialist->setName("Sara");
        $specialist->setSurname("Dow");
        $specialist->setAbout("A zookeeper is someone who works in a zoo. More specifically, it is someone who cares for animals in the zoo. They are responsible for the feeding and daily care of the animals. They clean out the exhibitions and report health problems. They answer questions about the animals. They sometimes give a demonstration of how to care for or feed the animals.");
        $specialist->setSpecies($species);
        $manager->persist($specialist);

        $animal = new Animal();
        $animal->setName("Clown Anemonefish");
        $date = new DateTime('2014-04-04');
        $animal->setBirthYear($date);
        $animal->setAnimalBook('This 4-inch-long (10-centimeter-long) fish shares an amazing partnership with another sea creature: the anemone (pronounced: uh-NEM-uh-NEE). The partnership benefits both participants, and the close relationship led to the fish being named an anemonefish.');
        $animal->setSpecies($species);
        $animal->setManager($specialist);
        $manager->persist($animal);

        $animal = new Animal();
        $animal->setName("Bull Shark");
        $date = new DateTime('2004-08-01');
        $animal->setBirthYear($date);
        $animal->setAnimalBook("Bull sharks are the most dangerous sharks in the world, according to many experts. This is because they're an aggressive species of shark, and they tend to hunt in waters where people often swim: along tropical shorelines.");
        $animal->setSpecies($species);
        $animal->setManager($specialist);
        $manager->persist($animal);

        $animal = new Animal();
        $animal->setName("Electric Eel");
        $date = new DateTime('1990-05-05');
        $animal->setBirthYear($date);
        $animal->setAnimalBook("The electric eel gets its name from its shocking abilities! Special organs in the eel’s body release powerful electric charges of up to 650 volts—that’s more than five times the power of a standard United States wall socket!");
        $animal->setSpecies($species);
        $animal->setManager($specialist);
        $manager->persist($animal);


        $manager->flush();
    }
}
