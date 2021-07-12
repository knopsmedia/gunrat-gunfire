<?php declare(strict_types=1);

namespace Gunratbe\App\Repository;

final class LocalJsonKeyValueRepository implements KeyValueRepository
{
    private array $keyValueStore = [];

    public function __construct(private string $filename)
    {
        if (!file_exists($this->filename)) {
            return;
        }

        $contents = file_get_contents($this->filename);

        if (!$contents) {
            $contents = '{}';
        }

        $json = json_decode($contents, true);
        if (!is_array($json)) {
            $json = [];
        }

        $this->keyValueStore = $json;
    }

    public function __destruct()
    {
        file_put_contents($this->filename, json_encode($this->keyValueStore, JSON_PRETTY_PRINT));
    }

    public function has(string $key): bool
    {
        return isset($this->keyValueStore[$key]);
    }

    public function get(string $key): mixed
    {
        if (!$this->has($key)) {
            return null;
        }

        $value = $this->keyValueStore[$key]['value'];
        $type = $this->keyValueStore[$key]['type'];

        if ($type === 'datetime') {
            $value = \DateTimeImmutable::createFromFormat(DATE_ATOM, $value);
        }

        return $value;
    }

    public function set(string $key, $value): void
    {
        $type = gettype($value);

        if ($value instanceof \DateTimeInterface) {
            $value = $value->format(DATE_ATOM);
            $type = 'datetime';
        }

        $this->keyValueStore[$key] = compact('value', 'type');
    }
}