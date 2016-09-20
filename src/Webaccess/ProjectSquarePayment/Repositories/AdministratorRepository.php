<?php
namespace Webaccess\ProjectSquarePayment\Repositories;

use Webaccess\ProjectSquarePayment\Entities\Administrator;

interface AdministratorRepository
{
    public function getByID($administratorID);

    public function persist(Administrator $administrator);
}