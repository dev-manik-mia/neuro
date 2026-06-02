<?php

namespace Manik\Neuro\Vector\Drivers;

use Illuminate\Support\Facades\Http;
use Manik\Neuro\Contracts\VectorDriver;

class MilvusDriver implements VectorDriver
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function createCollection(string $name, int $dimensions, array $options = []): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout($this->config['timeout'] ?? 30)
            ->post($this->baseUrl().'/api/v1/collection', [
                'collection_name' => $name,
                'description' => $options['description'] ?? '',
                'fields' => [
                    [
                        'field_name' => 'id',
                        'data_type' => 'VarChar',
                        'is_primary' => true,
                        'max_length' => 255,
                    ],
                    [
                        'field_name' => 'vector',
                        'data_type' => 'FloatVector',
                        'type_params' => [
                            'dim' => (string) $dimensions,
                        ],
                    ],
                    [
                        'field_name' => 'metadata',
                        'data_type' => 'JSON',
                    ],
                ],
            ]);

        return $response->throw()->json();
    }

    public function deleteCollection(string $name): bool
    {
        $response = Http::withHeaders($this->headers())
            ->timeout($this->config['timeout'] ?? 30)
            ->delete($this->baseUrl().'/api/v1/collection', [
                'collection_name' => $name,
            ]);

        return $response->throw()->successful();
    }

    public function listCollections(): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout($this->config['timeout'] ?? 30)
            ->get($this->baseUrl().'/api/v1/collection');

        $data = $response->throw()->json();

        return $data['data'] ?? [];
    }

    public function upsert(string $collection, array $records): array
    {
        $rows = [];

        foreach ($records as $record) {
            $rows[] = [
                'id' => $record['id'],
                'vector' => $record['values'] ?? $record['vector'],
                'metadata' => $record['metadata'] ?? $record['payload'] ?? [],
            ];
        }

        $response = Http::withHeaders($this->headers())
            ->timeout($this->config['timeout'] ?? 30)
            ->post($this->baseUrl().'/api/v1/insert', [
                'collection_name' => $collection,
                'data' => $rows,
            ]);

        return $response->throw()->json();
    }

    public function search(string $collection, array $vector, array $options = []): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout($this->config['timeout'] ?? 30)
            ->post($this->baseUrl().'/api/v1/search', [
                'collection_name' => $collection,
                'vector' => $vector,
                'limit' => $options['top_k'] ?? 10,
                'params' => $options['params'] ?? [
                    'metric_type' => $options['metric_type'] ?? 'COSINE',
                ],
            ]);

        return $response->throw()->json();
    }

    public function delete(string $collection, string|array $id): bool
    {
        $ids = is_array($id) ? $id : [$id];

        $response = Http::withHeaders($this->headers())
            ->timeout($this->config['timeout'] ?? 30)
            ->post($this->baseUrl().'/api/v1/delete', [
                'collection_name' => $collection,
                'filter' => 'id in ['.implode(',', array_map(fn ($v) => "'{$v}'", $ids)).']',
            ]);

        return $response->throw()->successful();
    }

    public function count(string $collection): int
    {
        $response = Http::withHeaders($this->headers())
            ->timeout($this->config['timeout'] ?? 30)
            ->post($this->baseUrl().'/api/v1/collection/stats', [
                'collection_name' => $collection,
            ]);

        $data = $response->throw()->json();

        return $data['data']['row_count'] ?? $data['row_count'] ?? 0;
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer '.($this->config['api_key'] ?? ''),
        ];
    }

    protected function baseUrl(): string
    {
        return rtrim($this->config['host'] ?? 'http://localhost:19530', '/');
    }
}
