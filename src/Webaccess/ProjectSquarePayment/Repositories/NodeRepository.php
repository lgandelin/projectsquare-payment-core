<?php

namespace Webaccess\ProjectSquarePayment\Repositories;

use Webaccess\ProjectSquarePayment\Entities\Node;

interface NodeRepository
{
    public function getByID($node);

    public function persist(Node $node);

    public function getAvailableNodeIdentifier();
}