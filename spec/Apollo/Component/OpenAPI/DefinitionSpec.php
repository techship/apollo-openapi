<?php

declare(strict_types=1);

namespace spec\Apollo\Component\OpenAPI;

use Apollo\Component\OpenAPI\Definition;
use PhpSpec\ObjectBehavior;

/**
 * Class DefinitionSpec.
 */
class DefinitionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Definition::class);
    }

    function it_should_return_empty_definition()
    {
        $response = $this->get([], []);
        $response->shouldBeEqualTo([]);
    }

    function it_should_return_definition()
    {
        $parameters = [
            '$ref' => '#/components/requestBodies/ArticleBody',
        ];

        $articleBody = [
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
        ];

        $config = [
            'schemas' => [
                'ArticleBody' => $articleBody,
            ],
            'requestBodies' => [
                'ArticleBody' => [
                    'content' => [
                        'application/hal+json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/ArticleBody',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->get($config, $parameters);
        $response->shouldBeEqualTo($articleBody);
    }
}
