<?php
/**
 * Settings repository backed by data/settings.json.
 */

namespace App\Repositories;

class SettingRepository extends BaseRepository
{
    protected string $table = 'settings';

    public function findByKey(string $key): ?array
    {
        $rows = $this->where('key', $key);
        return $rows[0] ?? null;
    }

    public function value(string $key, mixed $default = null): mixed
    {
        $row = $this->findByKey($key);
        return $row['value'] ?? $default;
    }

    public function upsert(string $key, mixed $value, string $type = 'string'): array
    {
        $row = $this->findByKey($key);
        $data = [
            'key'   => $key,
            'value' => $value,
            'type'  => $type,
        ];

        if ($row) {
            return $this->update((int) $row['id'], $data) ?? array_merge($row, $data);
        }

        return $this->create($data);
    }
}
