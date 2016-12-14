<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Infrastructure;

use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Entities\Node;
use Webaccess\ProjectSquarePayment\Repositories\NodeRepository;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Infrastructure\CreateInfrastructureRequest;
use Webaccess\ProjectSquarePayment\Responses\Infrastructure\CreateInfrastructureResponse;
use Webaccess\ProjectSquarePayment\Contracts\RemoteInfrastructureService;

class CreateInfrastructureInteractor
{
    private $nodeRepository;
    private $platformRepository;
    private $remoteInfrastructureService;
    private $logger;

    /**
     * @param NodeRepository $nodeRepository
     * @param PlatformRepository $platformRepository
     * @param RemoteInfrastructureService $remoteInfrastructureService
     * @param Logger $logger
     */
    public function __construct(NodeRepository $nodeRepository, PlatformRepository $platformRepository, RemoteInfrastructureService $remoteInfrastructureService, Logger $logger)
    {
        $this->nodeRepository = $nodeRepository;
        $this->platformRepository = $platformRepository;
        $this->remoteInfrastructureService = $remoteInfrastructureService;
        $this->logger = $logger;
    }

    /**
     * @param CreateInfrastructureRequest $request
     * @return CreateInfrastructureResponse
     */
    public function execute(CreateInfrastructureRequest $request)
    {
        $this->logger->logRequest(self::class, $request);

        $errorCode = null;
        $nodeIdentifier = $this->nodeRepository->getAvailableNodeIdentifier();

        if (!$nodeIdentifier) {
            $nodeIdentifier = $this->createNewNode();
            $this->remoteInfrastructureService->launchEnvCreation($nodeIdentifier, $request->slug, $request->administratorEmail, $request->usersLimit);
        } else {
            $this->remoteInfrastructureService->launchAppCreation($nodeIdentifier, $request->slug, $request->administratorEmail, $request->usersLimit);
            $this->nodeRepository->setNodeUnavailable($nodeIdentifier);
        }
        $this->platformRepository->updatePlatformNodeIdentifier($request->platformID, $nodeIdentifier);

        $nodeIdentifier = $this->createNewNode();
        $this->remoteInfrastructureService->launchNodeCreation($nodeIdentifier);

        $response = ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);

        $this->logger->logResponse(self::class, $response);

        return $response;
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

    /**
     * @return string
     */
    private function generateNodeIdentifier()
    {
        return uniqid();
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
}