<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    public $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST')])
            ->build();
    }

    public function indexAsset($asset){
        $params = [
            'index' => 'assets',
            'id' => $asset->id,
            'body' => [
                'name' => $asset->name,
                'brand' => $asset->brand,
                'status' => $asset->status,
                'category' => $asset->category->name,
                'created_at' => $asset->created_at,
            ]
        ];

         return $this->client->index($params);
    }

    public function searchAssets($query){
         return $this->client->search([
            'index' => 'assets',
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query'  => $query,
                        'fields' => ['name', 'brand', 'category']
                    ]
                ]
            ]
        ]);
    }

    public function delete($id){
        return $this->client->delete([
            'index' => 'assets',
            'id'    => $id
        ]);
    }

    public function deleteIndex(){
        $deleteParams = [
            'index' => 'assets'
        ];
        return $this->client->indices()->delete($deleteParams);
    }

    public function createIndex(){
        $params = [
            'index' => 'assets',
            'body' => [
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 0
                ],
                'mappings' => [
                    'properties' => [
                        'name' => [
                            'type' => 'text'
                        ],
                        'brand' => [
                            'type' => 'text'
                        ],
                        'status' => [
                            'type' => 'keyword'
                        ],
                        'category' => [
                            'type' => 'text'
                        ],
                        'created_at' => [
                            'type' => 'date'
                        ]
                    ]
                ]
            ]
        ];

        return $this->client->indices()->create($params);
    }

    public function searchDocument(){
        $params = [
            'index' => 'assets',
            'body'  => [
                'query' => [
                    'match' => [
                        'testField' => 'abc'
                    ]
                ]
            ]
        ];

        return $this->client->search($params);
    }
}