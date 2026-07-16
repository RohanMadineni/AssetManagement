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
        $contentString = mb_strtolower($asset->name.' '.$asset->brand.' '.$asset->price.' '.$asset->status.' '.optional($asset->category)->name);

        $params = [
            'index' => 'assets',
            'id' => $asset->id,
            'body' => [
                // 'content' => $asset->name.' '.$asset->brand.' '.$asset->price.' '.$asset->status.' '.optional($asset->category)->name,
                'content' => $contentString
            ]
        ];

         return $this->client->index($params);
    }

    public function searchAssets($query){
        $lowercaseQuery = mb_strtolower($query);
         return $this->client->search([
            'index' => 'assets',
            
            'body'  => [
                'query' => [
                    'bool' => [
                        'should' => [
                            // Clause 1: Exact Match gets a higher score boost
                            [ 'match' => [ 'content' => [ 'query' => $query, 'boost' => 2 ] ] ],
                            // Clause 2: Partial Match ensures we catch incomplete text
                            [ 'wildcard' => [ 
                                    'content' => "*$lowercaseQuery*", 
                                ] ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function delete($id){
        try{
            return $this->client->delete([
                'index' => 'assets',
                'id'    => $id
            ]);
        } catch (\Elastic\Elasticsearch\Exception\ClientResponseException $e) {
            if ($e->getCode() === 404) {
                return null; // already deleted, safe to ignore
            }
            throw $e;
        }
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
                        'content' => [
                            'type' => 'text'
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