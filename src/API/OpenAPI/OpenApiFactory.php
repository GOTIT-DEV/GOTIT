<?php

namespace App\API\OpenAPI;

use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Exception\InvalidResourceException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\Operation\UnderscorePathSegmentNameGenerator;
use ApiPlatform\Core\PathResolver\OperationPathResolver;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

class OpenApiFactory implements OpenApiFactoryInterface {
  private $operationPathResolver;

  private $path_prefix = '';

  public function __construct(
    private OpenApiFactoryInterface $decorated,
    private ResourceMetadataFactoryInterface $resMetaFactory,
    private ResourceNameCollectionFactoryInterface $resNameFactory,
    private KernelInterface $kernel
  ) {
    $config = Yaml::parseFile("{$this->kernel->getProjectDir()}/config/routes/api_platform.yaml");
    $this->path_prefix = $config['api_platform']['prefix'] ?? '';
    $this->operationPathResolver = new OperationPathResolver(
      new UnderscorePathSegmentNameGenerator()
    );
  }

  public function __invoke(array $context = []): OpenApi {
    $openApi = $this->decorated->__invoke($context);

    /**
     * Re-use generated schema definition from the GET collection operation for the output of the collection "import"
     * custom operation.
     */
    $paths = $openApi->getPaths();
    foreach ($this->resNameFactory->create() as $resourceClass) {
      $resMetadata = $this->resMetaFactory->create($resourceClass);
      $resourceShortName = $resMetadata->getShortName();
      $collectionOperations = $resMetadata->getCollectionOperations();
      $importOp = $collectionOperations['import'] ?? null;
      $collectionGetOp = $collectionOperations['get'] ?? null;

      $importContext = $importOp['openapi_context'] ?? null;

      if (null !== $importOp && null !== $collectionGetOp) {
        if (!($importOp['path'] ?? null)) {
          throw new InvalidResourceException(
            "Custom import operation defined for {$resourceClass} must define its path."
          );
        }
        $pathImport = $this->path_prefix . $importOp['path'];

        $pathGET = $this->path_prefix . $this->getPath(
          $resourceShortName,
          'get',
          $collectionGetOp,
          OperationType::COLLECTION
        );

        $responseContent = $paths->getPath($pathGET)->getGet()->getResponses()[200]->getContent();

        $importPathItem = $paths->getPath($pathImport);
        $importOperation = $importPathItem->getPost();
        $requestBody = $importOperation->getRequestBody();
        $requestBody = $requestBody->withDescription(
          $importContext['request_body']['description'] ??
          "The {$resourceShortName} records as a CSV string"
        );

        $responses = $importOperation->getResponses();
        $responses[201] = $responses[201]
          ->withContent($importContext['responses'][201]['content'] ?? $responseContent)
          ->withDescription(
            $importContext['responses'][201]['description'] ??
            "{$resourceShortName} resources imported"
          );
        $paths->addPath(
          $pathImport,
          $importPathItem->withPost(
            $importOperation
              ->withSummary(
                $importContext['summary'] ?? "Imports {$resourceShortName} resources"
              )
              ->withDescription(
                $importContext['description'] ?? "Imports {$resourceShortName} resources from CSV."
              )
              ->withResponses($responses)
              ->withRequestBody($requestBody)
          )
        );
      }
    }

    return $openApi->withPaths($paths);
  }

  /**
   * Gets the path for an operation.
   *
   * If the path ends with the optional _format parameter, it is removed as optional path parameters are not yet
   * supported.
   *
   * @see https://github.com/OAI/OpenAPI-Specification/issues/93
   */
  private function getPath(
    string $resourceShortName,
    string $operationName,
    array $operation,
    string $operationType
  ): string {
    $path = $this->operationPathResolver
      ->resolveOperationPath($resourceShortName, $operation, $operationType, $operationName);
    if ('.{_format}' === substr($path, -10)) {
      $path = substr($path, 0, -10);
    }

    return 0 === strpos($path, '/') ? $path : '/' . $path;
  }
}
