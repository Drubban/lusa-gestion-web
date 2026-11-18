<?php

namespace App\Models\Concerns;

use App\Support\Hashid;
use Illuminate\Database\Eloquent\Model;

/**
 * Expone el modelo con un id ofuscado en URLs y respuestas.
 * Las rutas reciben el hash y el route model binding lo resuelve.
 */
trait HasHashid
{
    public function getHashidAttribute(): ?string
    {
        return $this->getKey() ? Hashid::encode((int) $this->getKey(), static::hashidContexto()) : null;
    }

    public function getRouteKey(): mixed
    {
        return $this->hashid;
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if ($field !== null) {
            return parent::resolveRouteBinding($value, $field);
        }
        $id = Hashid::decode((string) $value, static::hashidContexto());

        return $id ? $this->newQuery()->whereKey($id)->firstOrFail() : abort(404);
    }

    public static function decodeHashid(?string $hash): ?int
    {
        return Hashid::decode($hash, static::hashidContexto());
    }

    public static function findByHashid(?string $hash): ?static
    {
        $id = static::decodeHashid($hash);

        return $id ? static::find($id) : null;
    }

    public static function findByHashidOrFail(?string $hash): static
    {
        return static::findByHashid($hash) ?? abort(404);
    }

    public static function hashidContexto(): string
    {
        return (new static)->getTable();
    }
}
