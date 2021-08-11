<?php

declare(strict_types=1);

namespace Apollo\Component\OpenAPI;

/**
 * Class Definition.
 */
class Definition
{
    public const DEFAULT_FORMAT = 'application/hal+json';

    /**
     * @param array $components
     * @param array $requestBody
     *
     * @return array
     */
    public function get(array $components, array $requestBody): array
    {
        $refRequestBody = $requestBody['$ref'] ?? '';

        if (empty($refRequestBody)) {
            return [];
        }

        $requestBodyDefinition = $this->getDefinition($refRequestBody, $components['requestBodies']);
        $refSchema = $requestBodyDefinition['content'][self::DEFAULT_FORMAT]['schema']['$ref'];

        return $this->getDefinition($refSchema, $components['schemas']);
    }

    private function getDefinition(string $ref, array $referenceDefinitions): array
    {
        $refExploded = explode('/', $ref);

        return $referenceDefinitions[end($refExploded)];
    }
}
