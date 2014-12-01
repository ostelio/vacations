<?php

namespace Vacation\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class VacationController extends AbstractActionController
{
    /**
     * @var DoctrineORMEntityManager
     */
    private $em;

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function indexAction()
    {
        $TOT_VACATIONSHOURS = 176;
        $TOT_ROLHOURS = 53;

        $TOT_VACATIONDAYS = 22;
        $TOT_ROLDAYS = 6.6;

        $name = 'mvichi';
        $year = date("Y");

        $totAnnualHours = $TOT_VACATIONSHOURS + $TOT_ROLHOURS;
        $totAnnualDays = $TOT_VACATIONDAYS + $TOT_ROLDAYS;

        $requestsModel = array();
        $requestsModel['year'] = $year;
        $requestsModel["goneVacationsHours"] = $this->getGoneVacations($name, $year);
        $requestsModel["goneRolsHours"] = $this->getGoneRols($name, $year);

        $requestsModel["goneVacationsDays"] = $requestsModel["goneVacationsHours"] / 8;
        $requestsModel["goneRolsDays"] = $requestsModel["goneRolsHours"] / 8;

        $requestsModel["vacationResidualHours" ]= $TOT_VACATIONSHOURS - $requestsModel["goneVacationsHours"];
        $requestsModel["vacationResidualDays"] = $TOT_VACATIONDAYS - $requestsModel["goneVacationsDays"];
        $requestsModel["rolResidualHours"] = $TOT_ROLHOURS - $requestsModel["goneRolsHours"];
        $requestsModel["rolResidualDays"] = $TOT_ROLDAYS - $requestsModel["goneRolsDays"];

        $requestsModel["totHoursResidual"] = $totAnnualHours - ($requestsModel["goneVacationsHours"] + $requestsModel["goneRolsHours"]);
        $requestsModel["totDaysResidual"] = $totAnnualDays - ($requestsModel["goneVacationsDays"] + $requestsModel["goneRolsDays"]);


        // ma avere un oggetto che estende view model(invece di chiamare model quello che viene da db)? che cagata!
        return new ViewModel($requestsModel);
    }

    private function getGoneVacations($name, $year)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT sum(r.hours) as goneVacationHours from Vacation\Entity\Requests r WHERE r.user = :name AND r.year = :year AND r.type = :type');
        $query->setParameters(array(
            'name' => $name,
            'year' => $year,
            'type' => 'vacation'
        ));
        $hours = $query->getSingleScalarResult();
        return (float)$hours;

    }

    private function getGoneRols($name, $year)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT sum(r.hours) as goneVacationHours from Vacation\Entity\Requests r WHERE r.user = :name AND r.year = :year AND r.type = :type');
        $query->setParameters(array(
            'name' => $name,
            'year' => $year,
            'type' => 'paidLeave'
        ));
        $hours = $query->getSingleScalarResult();
        return (float)$hours;
    }

}

