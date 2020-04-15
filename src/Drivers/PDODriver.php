<?php
declare(strict_types=1);
namespace Apteles\Paginator\Drivers;

use Apteles\Paginator\Contracts\PaginationInterface;

class PDODriver implements PaginationInterface
{
    public function data(object $data = null): array
    {
        return [];
    }
    public function total(): int
    {
        return 1;
    }
}
