<?php

namespace Manik\Neuro\Vector\Drivers;

use Illuminate\Support\Facades\DB;
use Manik\Neuro\Contracts\VectorDriver;
use Manik\Neuro\Vector\Concerns\HasDefaultCollection;

class PgvectorDriver implements VectorDriver
{
    use HasDefaultCollection;

    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function createCollection(string $name, int $dimensions, array $options = []): array
    {
        $table = $this->tableName($name);

        $schema = $this->config['schema'] ?? 'public';

        DB::statement("CREATE TABLE IF NOT EXISTS {$schema}.{$table} (
            {$table}_id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
            collection varchar(255) NOT NULL,
            external_id varchar(255) NOT NULL,
            embedding vector({$dimensions}) NOT NULL,
            metadata jsonb DEFAULT '{}'::jsonb,
            created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP
        )");

        DB::statement("CREATE INDEX IF NOT EXISTS {$table}_collection_idx ON {$schema}.{$table} (collection)");
        DB::statement("CREATE INDEX IF NOT EXISTS {$table}_external_id_idx ON {$schema}.{$table} (external_id)");

        return ['status' => 'created', 'table' => $table];
    }

    public function deleteCollection(string $name): bool
    {
        $table = $this->tableName($name);
        $schema = $this->config['schema'] ?? 'public';

        DB::statement("DROP TABLE IF EXISTS {$schema}.{$table}");

        return true;
    }

    public function listCollections(): array
    {
        $schema = $this->config['schema'] ?? 'public';

        $tables = DB::select(
            "SELECT table_name FROM information_schema.tables WHERE table_schema = ? AND table_type = 'BASE TABLE'",
            [$schema]
        );

        return array_map(fn ($row) => $row->table_name, $tables);
    }

    public function upsert(string $collection, array $records): array
    {
        $table = $this->tableName($collection);
        $schema = $this->config['schema'] ?? 'public';

        $ids = [];

        foreach ($records as $record) {
            $exists = DB::table("{$schema}.{$table}")
                ->where('external_id', $record['id'])
                ->where('collection', $collection)
                ->first();

            if ($exists) {
                DB::table("{$schema}.{$table}")
                    ->where("{$table}_id", $exists->{$table.'_id'})
                    ->update([
                        'embedding' => '['.implode(',', $record['values'] ?? $record['vector']).']',
                        'metadata' => json_encode($record['metadata'] ?? $record['payload'] ?? []),
                    ]);

                $ids[] = $exists->{$table.'_id'};
            } else {
                $id = DB::table("{$schema}.{$table}")
                    ->insertGetId([
                        'collection' => $collection,
                        'external_id' => $record['id'],
                        'embedding' => '['.implode(',', $record['values'] ?? $record['vector']).']',
                        'metadata' => json_encode($record['metadata'] ?? $record['payload'] ?? []),
                    ]);

                $ids[] = $id;
            }
        }

        return ['status' => 'upserted', 'ids' => $ids];
    }

    public function search(string $collection, array $vector, array $options = []): array
    {
        $table = $this->tableName($collection);
        $schema = $this->config['schema'] ?? 'public';
        $limit = $options['top_k'] ?? 10;

        $vectorStr = '['.implode(',', $vector).']';

        $rows = DB::select(
            "SELECT *, embedding <-> ? AS distance
             FROM {$schema}.{$table}
             WHERE collection = ?
             ORDER BY embedding <-> ?
             LIMIT ?",
            [$vectorStr, $collection, $vectorStr, $limit]
        );

        return array_map(fn ($row) => [
            'id' => $row->{$table.'_id'},
            'external_id' => $row->external_id,
            'metadata' => json_decode($row->metadata ?? '{}', true),
            'distance' => $row->distance,
            'score' => 1 - $row->distance,
        ], $rows);
    }

    public function delete(string $collection, string|array $id): bool
    {
        $table = $this->tableName($collection);
        $schema = $this->config['schema'] ?? 'public';

        $ids = is_array($id) ? $id : [$id];

        DB::table("{$schema}.{$table}")
            ->where('collection', $collection)
            ->whereIn('external_id', $ids)
            ->delete();

        return true;
    }

    public function count(string $collection): int
    {
        $table = $this->tableName($collection);
        $schema = $this->config['schema'] ?? 'public';

        return DB::table("{$schema}.{$table}")
            ->where('collection', $collection)
            ->count();
    }

    protected function tableName(string $name): string
    {
        $prefix = $this->config['table_prefix'] ?? 'vector_';

        return $prefix.preg_replace('/[^a-zA-Z0-9_]/', '_', $name);
    }
}
