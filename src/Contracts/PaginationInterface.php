<?php
declare(strict_types=1);
namespace Apteles\Paginator\Contracts;

interface PaginationInterface
{
    public function data(object $data = null): array;
    public function total(): int;
}
