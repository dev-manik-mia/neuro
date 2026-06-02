<?php

namespace Manik\Neuro\Memory\Drivers;

use Illuminate\Support\Facades\DB;
use Manik\Neuro\Contracts\MemoryDriver;

class PersistentMemoryDriver implements MemoryDriver
{
    protected string $table;

    protected int $limit;

    public function __construct(
        protected array $config = [],
    ) {
        $this->table = $config['table'] ?? 'ai_memories';
        $this->limit = $config['limit'] ?? 100;
    }

    public function add(string $sessionId, array $message): void
    {
        DB::table($this->table)->insert([
            'session_id' => $sessionId,
            'role' => $message['role'] ?? 'user',
            'content' => $message['content'] ?? '',
            'created_at' => now(),
        ]);
    }

    public function get(string $sessionId, int $limit = 10): array
    {
        $rows = DB::table($this->table)
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit($this->limit)
            ->get();

        return $rows->map(fn ($row) => [
            'role' => $row->role,
            'content' => $row->content,
        ])->reverse()->values()->toArray();
    }

    public function clear(string $sessionId): void
    {
        DB::table($this->table)->where('session_id', $sessionId)->delete();
    }

    public function delete(string $sessionId): void
    {
        DB::table($this->table)->where('session_id', $sessionId)->delete();
    }
}
