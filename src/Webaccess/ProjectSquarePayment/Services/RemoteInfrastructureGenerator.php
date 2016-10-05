<?php

namespace Webaccess\ProjectSquarePayment\Services;

interface RemoteInfrastructureGenerator
{
    public function launchEnvCreation($nodeIdentifier, $slug, $administratorEmail, $usersLimit);

    public function launchAppCreation($nodeIdentifier, $slug, $administratorEmail, $usersLimit);

    public function launchNodeCreation($nodeIdentifier);
}