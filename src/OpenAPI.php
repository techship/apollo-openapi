<?php

declare(strict_types=1);

namespace Apollo\Component\OpenAPI;

use JsonSchema\SchemaStorage;
use Symfony\Component\Yaml\Parser;

/**
 * Class OpenAPI.
 */
class OpenAPI
{
    /**
     * @var string
     */
    private $dirSchemas;

    /**
     * @var array
     */
    private $schema;

    /**
     * OpenApi constructor.
     *
     * @param string $dirSchemas
     * @param string $schemaFilename
     */
    public function __construct(string $dirSchemas, string $schemaFilename)
    {
        $this->dirSchemas = $dirSchemas;
        $this->schema = (new Parser())->parseFile($dirSchemas.$schemaFilename);
    }

    /**
     * @param mixed $jsonSchemaObject
     *
     * @return SchemaStorage
     */
    public function getSchemaStorage($jsonSchemaObject): SchemaStorage
    {
        $schemaStorage = new SchemaStorage();
        $schemaStorage->addSchema('file://'.__DIR__.$this->dirSchemas, $jsonSchemaObject);

        return $schemaStorage;
    }

    /**
     * In OpenAPI terms, paths are endpoints (resources),
     * such as /users or /reports/summary/, that your API exposes.
     * All paths are relative to the API server URL.
     *
     * @return array
     */
    public function getPaths(): array
    {
        return array_keys($this->schema['paths']) ?? [];
    }

    /**
     * In OpenAPI terms, paths are endpoints (resources),
     * such as /users or /reports/summary/, that your API exposes.
     * Operations are the HTTP methods used to manipulate these paths,
     * such as GET, POST or DELETE.
     *
     * @return array
     */
    public function getPathsWithOperations(): array
    {
        return $this->schema['paths'] ?? [];
    }

    /**
     * Get a specific path (i.e endpoint) and return its operations.
     *
     * @param string $path
     *
     * @return array
     */
    public function getPathWithOperations(string $path): array
    {
        return $this->schema['paths'][$path] ?? [];
    }

    /**
     * The request body usually contains the representation of the resource to be created.
     * OpenAPI 3.0 provides the requestBody keyword to describe request bodies.
     * Request bodies are optional by default.
     *
     * @param array $parameters
     *
     * @return array
     */
    public function getRequestBody(array $parameters): array
    {
        return $parameters['requestBody'] ?? [];
    }

    /**
     * @param string $path
     * @param string $operationId
     *
     * @return array
     */
    public function getDefinition(string $path, string $operationId): array
    {
        $operations = $this->getPathWithOperations($path);
        $operationKey = array_search($operationId, array_column($operations, 'operationId'), true);

        return false === $operationKey ? [] : (new Definition())->get(
            $this->schema['components'] ?? [],
            $this->getRequestBody($operations[array_keys($operations)[$operationKey]])
        );
    }
}
