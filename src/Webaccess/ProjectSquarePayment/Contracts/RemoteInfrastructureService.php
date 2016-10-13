<?php

namespace Webaccess\ProjectSquarePayment\Contracts;

interface RemoteInfrastructureService
{
    public function launchEnvCreation($nodeIdentifier, $slug, $administratorEmail, $usersLimit);

    public function launchAppCreation($nodeIdentifier, $slug, $administratorEmail, $usersLimit);

    public function launchNodeCreation($nodeIdentifier);
}