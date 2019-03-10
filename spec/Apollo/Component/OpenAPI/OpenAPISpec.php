<?php

declare(strict_types=1);

namespace spec\Apollo\Component\OpenAPI;

use Apollo\Component\OpenAPI\OpenAPI;
use JsonSchema\SchemaStorage;
use PhpSpec\ObjectBehavior;

/**
 * Class OpenAPISpec.
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 */
class OpenAPISpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(__DIR__.'/../../../fixtures/config/schemas/', 'apollo.yaml');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OpenAPI::class);
    }

    function it_should_return_paths()
    {
        $this->getPaths()->shouldBeEqualTo([
            '/articles',
            '/articles/{id}',
            '/images',
        ]);
    }

    function it_should_return_schema_storage()
    {
        $jsonSchemaObject = json_decode(json_encode([
            'type' => 'object',
            'required' => [
                'name',
                'headline',
                'article_body',
            ],
            'additionalProperties' => false,
            'properties' => [
                'name' => ['type' => 'string'],
                'headline' => ['type' => 'string'],
                'article_body' => ['type' => 'string'],
            ],
        ]));

        $this->getSchemaStorage($jsonSchemaObject)->shouldBeAnInstanceOf(SchemaStorage::class);
    }

    function it_should_return_paths_with_operations()
    {
        $pathsWithOperation = $this->getPathsWithOperations();
        $pathsWithOperation->shouldBeArray();
        $pathsWithOperation->shouldHaveKey('/articles');
        $pathsWithOperation->shouldHaveKey('/articles/{id}');
        $pathsWithOperation->shouldHaveKey('/images');
    }

    function it_should_return_path_with_operations_with_existing_path()
    {
        $pathsWithOperation = $this->getPathWithOperations('/articles');
        $pathsWithOperation->shouldBeArray();
        $pathsWithOperation->shouldHaveKey('get');
        $pathsWithOperation->shouldHaveKey('post');
    }

    function it_should_return_definition()
    {
        $definition = $this->getDefinition('/articles', 'postArticle');
        $definition->shouldBeArray();
        $definition->shouldHaveKey('type');
        $definition->shouldHaveKey('required');
        $definition->shouldHaveKey('properties');
        $definition->shouldHaveKey('additionalProperties');
    }
}
