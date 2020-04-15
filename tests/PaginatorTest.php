<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Apteles\Paginator\Paginator;

class PaginatorTest extends TestCase
{
    private Paginator $paginator;

    public function setUp(): void
    {
        $this->paginator = new Paginator;
    }

    public function test_configurations_is_loaded(): void
    {
        $this->assertIsArray($this->paginator->getConfig());
    }
}
