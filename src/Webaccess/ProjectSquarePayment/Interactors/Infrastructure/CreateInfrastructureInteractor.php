<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Infrastructure;

use Webaccess\ProjectSquarePayment\Entities\Node;
use Webaccess\ProjectSquarePayment\Repositories\NodeRepository;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Infrastructure\CreateInfrastructureRequest;
use Webaccess\ProjectSquarePayment\Responses\Infrastructure\CreateInfrastructureResponse;
use Webaccess\ProjectSquarePayment\Services\RemoteInfrastructureGenerator;

class CreateInfrastructureInteractor
{
    private $nodeRepository;
    private $remoteInfrastructureGenerator;

    public function __construct(NodeRepository $nodeRepository, PlatformRepository $platformRepository, RemoteInfrastructureGenerator $remoteInfrastructureGenerator)
    {
        $this->nodeRepository = $nodeRepository;
        $this->platformRepository = $platformRepository;
        $this->remoteInfrastructureGenerator = $remoteInfrastructureGenerator;
    }

    public function execute(CreateInfrastructureRequest $request)
    {
        $errorCode = null;
        $nodeIdentifier = $this->nodeRepository->getAvailableNodeIdentifier();

        if (!$nodeIdentifier) {
            $nodeIdentifier = $this->createNewNode();
            $this->remoteInfrastructureGenerator->launchEnvCreation($nodeIdentifier, $request->slug, $request->administratorEmail, $request->usersLimit);
        } else {
            $this->remoteInfrastructureGenerator->launchAppCreation($nodeIdentifier, $request->slug, $request->administratorEmail, $request->usersLimit);
            $this->nodeRepository->setNodeUnavailable($nodeIdentifier);
        }
        $this->platformRepository->updatePlatformNodeIdentifier($request->platformID, $nodeIdentifier);

        $nodeIdentifier = $this->createNewNode();
        $this->remoteInfrastructureGenerator->launchNodeCreation($nodeIdentifier);

        return ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);
    }

    /**
     * @return CreateInfrastructureResponse
     */
    private function createSuccessResponse()
    {
        return new CreateInfrastructureResponse([
            'success' => true,
        ]);
    }

    /**
     * @param $errorCode
     * @return CreateInfrastructureResponse
     */
    private function createErrorResponse($errorCode)
    {
        return new CreateInfrastructureResponse([
            'success' => false,
            'errorCode' => $errorCode
        ]);
    }

    /**
     * @return string
     */
    private function generateNodeIdentifier()
    {
        return uniqid();
    }

    /**
     * @return string
     */
    private function createNewNode()
    {
        $node = new Node();
        $nodeIdentifier = $this->generateNodeIdentifier();
        $node->setIdentifier($nodeIdentifier);
        $node->setAvailable(false);
        $this->nodeRepository->persist($node);

        return $nodeIdentifier;
    }
}