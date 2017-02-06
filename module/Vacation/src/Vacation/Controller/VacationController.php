<?php

namespace Vacation\Controller;

use Zend;
use Doctrine\ORM\EntityManager;
use Vacation\Entity\Requests;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Vacation\Model\VacationRequest;
use Vacation\Form\VacationRequestForm;


class VacationController extends AbstractActionController
{
    private $name;
    private $year;

    private $settings;

    private $VACATIONS_ANNUAL_AMOUNT;
    private $ROL_ANNUAL_AMOUNT;
    private $TOT_DAYSPERMONTH;
    private $TOT_SUPPRESSED_VACATIONS;
    //$TOT_ROL_LAST_YEAR_HOURS = 82;
    private $TOT_ROL_LAST_YEAR_HOURS;
    private $TOT_VACATIONS_LAST_YEAR_HOURS;


    /**
     * @var DoctrineORMEntityManager
     */
    private $em;


    public function indexAction()
    {
        $this->loadSettings();

        $TOT_VACATIONSHOURS = $this->VACATIONS_ANNUAL_AMOUNT + $this->TOT_VACATIONS_LAST_YEAR_HOURS;
        $TOT_ROLHOURS = $this->ROL_ANNUAL_AMOUNT + $this->TOT_SUPPRESSED_VACATIONS + $this->TOT_ROL_LAST_YEAR_HOURS;

        $TOT_VACATIONDAYS = $TOT_VACATIONSHOURS / 8;
        $TOT_ROLDAYS = $TOT_ROLHOURS / 8;

        $totAnnualHours = $TOT_VACATIONSHOURS + $TOT_ROLHOURS;
        $totAnnualDays = $TOT_VACATIONDAYS + $TOT_ROLDAYS;

        $this->name = 'mvichi';
        $this->year = date("Y");

        $requestsModel = array();

        $requestsModel['year'] = $this->year;

        $requestsModel["totDaysPerMonth"] = $this->TOT_DAYSPERMONTH;

        $requestsModel["goneVacationsHours"] = $this->getGoneVacations($this->name, $this->year);
        $requestsModel["goneRolsHours"] = $this->getGoneRols($this->name, $this->year);

        $requestsModel["goneVacationsDays"] = $this->toDecimal( $requestsModel["goneVacationsHours"] / 8 );
        $requestsModel["goneRolsDays"] = $this->toDecimal( $requestsModel["goneRolsHours"] / 8 );

        $requestsModel["vacationResidualHours" ]= $TOT_VACATIONSHOURS - $requestsModel["goneVacationsHours"];
        $requestsModel["rolResidualHours"] = $TOT_ROLHOURS - $requestsModel["goneRolsHours"];

        $requestsModel["vacationResidualDays"] = $this->toDecimal( $TOT_VACATIONDAYS - $requestsModel["goneVacationsDays"] );
        $requestsModel["rolResidualDays"] = $this->toDecimal( $TOT_ROLDAYS - $requestsModel["goneRolsDays"] );

        $requestsModel["totHoursResidual"] = $totAnnualHours - ($requestsModel["goneVacationsHours"] + $requestsModel["goneRolsHours"]);
        $requestsModel["totDaysResidual"] = $this->toDecimal( $totAnnualDays - ($requestsModel["goneVacationsDays"] + $requestsModel["goneRolsDays"]) );


        return new ViewModel($requestsModel);
    }

    public function addAction()
    {
        $this->name = 'mvichi';
        $this->year = date("Y");

        $form = new VacationRequestForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $vacationRequest = new VacationRequest();
            $form->setInputFilter($vacationRequest->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $vacationRequest->exchangeArray($form->getData());
                $requestsEntity = $this->toEntity($vacationRequest);
                $requestsEntity->setYear($this->year);
                $requestsEntity->setUser($this->name);

                $em = $this->getEntityManager();
                $em->persist($requestsEntity);
                $em->flush();

                return $this->redirect()->toRoute('vacation');
            }
        }
        return array('form' => $form);
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
        return $this->toDecimal($hours);

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
        return $this->toDecimal($hours);
    }

    private function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    /*
     * @param @var Album\Model\VacationRequest
     * @return @var Album\Model\VacationRequest
     */
    private function toEntity($obj)
    {
        $out = new Requests();
        $out->setType($obj->getType());
        $out->setHours($obj->getHours());
        $out->setDay($obj->getDay());
        $out->setMonth($obj->getMonth());
        return $out;
    }

    private function loadSettings()
    {
        $config = $this->getServiceLocator()->get('config');
        $this->settings = $config['vacationsconfig'];

        $this->VACATIONS_ANNUAL_AMOUNT = $this->settings['VACATIONS_ANNUAL_AMOUNT'];
        $this->ROL_ANNUAL_AMOUNT = $this->settings['ROL_ANNUAL_AMOUNT'];
        $this->TOT_DAYSPERMONTH = $this->settings['TOT_DAYSPERMONTH'];
        $this->TOT_SUPPRESSED_VACATIONS = $this->settings['TOT_SUPPRESSED_VACATIONS'];
        $this->TOT_ROL_LAST_YEAR_HOURS = $this->settings['TOT_ROL_LAST_YEAR_HOURS'];
        $this->TOT_VACATIONS_LAST_YEAR_HOURS = $this->settings['TOT_VACATIONS_LAST_YEAR_HOURS'];
    }

    private function toDecimal($number)
    {
        return number_format((float)$number, 2, '.', '');
    }

}

