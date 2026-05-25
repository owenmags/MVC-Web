<?php

declare(strict_types=1);

namespace App\Repositories;

use Core\Database\Findable;
use Core\Database\Persistable;

interface TaskRepositoryInterface extends Findable, Persistable
{
    /** @return list<array<string, mixed>> */
    public function allWithProject(): array;
}
