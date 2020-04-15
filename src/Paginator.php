<?php
declare(strict_types=1);
namespace Apteles\Paginator;

use stdClass;
use Apteles\Paginator\Exceptions\PageNotFoundException;

class Paginator
{
    private array $config = [];

    private int $currentPage;

    private int $start = 1;

    private int $total = 0;

    public function __construct()
    {
        $this->init();
    }

    public function init(): void
    {
        $this->loadConfigFile();
        $this->defineCurrentPage();
        $this->defineInitialPage();
    }

    public function loadConfigFile(): void
    {
        $this->config = require __DIR__ . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR .'config'. DIRECTORY_SEPARATOR.'paginator.php';
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    private function defineCurrentPage(): void
    {
        $this->currentPage = \intval(\filter_input(INPUT_GET, $this->config['query'], FILTER_SANITIZE_SPECIAL_CHARS)) ?: 1;
    }

    private function defineInitialPage(): void
    {
        $this->start = ($this->config['max_per_page'] * $this->currentPage) - $this->config['max_per_page'];
        $this->start = $this->start >= 1 ? $this->start : 1;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    private function getTotalPages(): float
    {
        return \ceil($this->total/$this->config['max_per_page']);
    }

    public function getDataWithCallable(callable $func, ?int $maxPerPage = null): array
    {
        $stdClass = new stdClass;
        $stdClass->maxPerPage = $this->start;
        $stdClass->start = $maxPerPage ?? $this->config['max_per_page'];

        return $func($stdClass);
    }

    // public function createPagination(object $conn, ?string $where = null, ?string $orderBy = null): object
    // {
    //     return $func($this->start, $this->config);
    //     if (!$where && !$orderBy) {
    //         return  $conn->query("SELECT * FROM users LIMIT {$this->start}, {$this->config['max_per_page']}");
    //     }
    // }

    private function isValidPage(): void
    {
        if ($this->currentPage > $this->total) {
            throw new PageNotFoundException('Page Not Found', 404);
        }
    }

    public function render(): void
    {
        $this->isValidPage();

        <<<TEMPLATE

        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav>

        TEMPLATE;

        if ($this->total > $this->config['max_per_page']) {
            if ($this->currentPage > 1) {
                print "<a href='?{$this->config['query']}=1'>{$this->config['links']['labels']['first_page']}</a>";
            }

            for ($i = $this->currentPage - $this->config['links']['max_links']; $i <= $this->currentPage -1 ;$i++) {
                if ($this->currentPage === $i) {
                    continue;
                }

                if ($i >= 2) {
                    print "<a href='?{$this->config['query']}={$i}'>{$i}</a>";
                }
            }

            print "<span>{$this->currentPage}</span>";

            for ($i = $this->currentPage + 1; $i <= $this->getTotalPages() + $this->config['links']['max_links'];$i++) {
                if ($i <= $this->getTotalPages() && $this->currentPage != $this->getTotalPages()) {
                    print "<a href='?{$this->config['query']}={$i}'>{$i}</a>";
                }
            }

            // if ($this->currentPage == $this->getTotalPages()) {
            //     print "<span>{$this->currentPage}</span>";
            //     return;
            // }

            //  print "<a href='?{$this->config['query']}=". ($this->currentPage + 1) ."'>{$this->config['links']['labels']['next_page']}</a>";

            print "<a href='?{$this->config['query']}=". $this->getTotalPages() ."'>{$this->config['links']['labels']['last_page']}</a>";
        }
    }

    public function __toString()
    {
        \ob_start();
        $this->render();
        $links = \ob_get_clean();
        return $links;
    }
}
