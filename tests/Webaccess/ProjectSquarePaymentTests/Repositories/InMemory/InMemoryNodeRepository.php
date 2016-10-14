<?php

namespace Webaccess\ProjectSquarePaymentTests\Repositories\InMemory;

use Webaccess\ProjectSquarePayment\Entities\Node;
use Webaccess\ProjectSquarePayment\Repositories\NodeRepository;

class InMemoryNodeRepository implements NodeRepository
{
    public $objects;

    public function __construct()
    {
        $this->objects = [];
    }

    private function getNextID()
    {
        return sizeof($this->objects) + 1;
    }

    public function getByID($nodeID)
    {
        return (isset($this->objects[$nodeID])) ? clone $this->objects[$nodeID] : false;
    }

    public function persist(Node $node)
    {
        if (!$node->getID()) {
            $node->setId($this->getNextID());
        }
        $this->objects[$node->getID()] = $node;

        return true;
    }

    public function getAvailableNodeIdentifier()
    {
        foreach ($this->objects as $node) {
            if ($node->isAvailable()) {
                return $node->getIdentifier();
            }
        }

        return false;
    }

    public function setNodeUnavailable($nodeIdentifier)
    {
        foreach ($this->objects as $node) {
            if ($node->getIdentifier() == $nodeIdentifier) {
                $node->setAvailable(false);
            }
        }
    }
}