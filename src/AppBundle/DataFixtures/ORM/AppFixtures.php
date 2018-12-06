<?php
namespace App\DataFixtures\ORM;


use AppBundle\Entity\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Doctrine\Common\DataFixtures\FixtureInterface;
class AppFixtures extends Fixture implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $em = $this->container->get('doctrine')->getEntityManager('default');
        for ($i = 0; $i < 20; $i++) {

            $parent = $em->getRepository("AppBundle:Task")->find(mt_rand(0, 20));
            $employee = new Employee();
            $empdate='-08-2018';
            $employee->setSurname('employee surname '.$i);
            $employee->setName('employee name '.$i);
            $employee->setPosition('position '.$i);
            $employee->setSalary(mt_rand(1000, 2000));
            $employee->setEmploymentDate(new \DateTime($i.$empdate));
            $employee->setParent($parent);
            $employee->setImage('4afb36bdc60d9dc5cdd4fbb4eca7f173.jpeg');
            $manager->persist($employee);
            $manager->flush();
        }


    }
}