<?php
declare(strict_types=1);
namespace Apteles\Paginator;

use stdClass;
use Apteles\Paginator\Exceptions\PageNotFoundException;

class Paginator
{
    private array $config = [];

    private int $currentPage;

    private int $offset = 0;

    private int $total = 0;

    private string $prefix = '';

    public function __construct(?array $config = null)
    {
        $this->init($config);
    }

    public function init(?array $config = null): void
    {
        $this->loadConfigFile($config);
        $this->defineCurrentPage();
        // $this->defineInitialPage();
    }

    public function loadConfigFile(?array $config = null): void
    {
        if (!$config) {
            $this->config = require __DIR__ . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR .'config'. DIRECTORY_SEPARATOR.'paginator.php';
        } else {
            $this->config = $config;
        }
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setOffset(?int $offset = null): void
    {
        $this->offset = $this->defineInitialPage($offset);
    }

    private function defineCurrentPage(): void
    {
        $this->currentPage = \intval($this->config['handlerUrl']());
    }


    private function defineInitialPage(?int $offset = null): int
    {
        $offset = ($this->currentPage * $offset) - $offset;
        return $offset >= 1 ? $offset : 0;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    private function getTotalPages(): float
    {
        return \ceil($this->total/$this->config['max_per_page']);
    }

    public function getDataWithCallable(callable $func, ?int $maxPerPage = null): array
    {
        $maxPerPage = $maxPerPage ? $this->config['max_per_page'] = $maxPerPage : $this->config['max_per_page'];

        $this->setOffset($maxPerPage);
        $stdClass = new stdClass;
        $stdClass->limit = $maxPerPage;
        $stdClass->offset = $this->offset;

        return $func($stdClass);
    }

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

        $urlQuery = function (?int $page = null) {
            $page = $page ?? $this->currentPage;
            return $this->config['host'] . '?' .\http_build_query([$this->config['urlKey'] => $page]);
        };

        $urlFriendly = function (?int $page = null) {
            $page = $page ?? $this->currentPage;
            return $this->config['host'] . "/{$this->prefix}/{$this->config['urlKey']}/{$page}";
        };

        $buildUrl = $this->config['useURLFriendly'] ? $urlFriendly :  $urlQuery ;

        if ($this->total > $this->config['max_per_page']) {
            if ($this->config['show_first_last_page']) {
            }

            if ($this->currentPage > 1) {
                print "<a href='{$buildUrl(1)}'>{$this->config['links']['labels']['first_page']}</a>";
            }

            for ($i = $this->currentPage - $this->config['links']['max_links']; $i <= $this->currentPage -1 ;$i++) {
                if ($i >= 2) {
                    print "<a href='{$buildUrl((int) $i)}'>{$i}</a>";
                }
            }

            print "<span>{$this->currentPage}</span>";

            for ($i = $this->currentPage + 1; $i <= $this->currentPage + $this->config['links']['max_links'];$i++) {
                if ($i <= $this->getTotalPages()) {
                    print "<a href='{$buildUrl((int) $i)}'>{$i}</a>";
                }
            }

            if ($this->currentPage != $this->getTotalPages()) {
                print "<a href='{$buildUrl((int)  $this->getTotalPages())}'>{$this->config['links']['labels']['last_page']}</a>";
            }
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
