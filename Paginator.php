<?php

// ページ総数が1ページのときはページャを非表示
class Paginator
{
    private $record_count;
    private $page_item_count = 10;
    private $max_pager_count = 5;
    private $current_page    = 1;

    public function __construct(int $record_count)
    {
        $this->setRecordCount($record_count);
    }

    private function setRecordCount(int $record_count)
    {
        if ($record_count < 0) {
            throw new InvalidArgumentException();
        }

        $this->record_count = $record_count;
    }

    public function setPageItemCount(int $page_item_count)
    {
        if ($page_item_count < 1) {
            throw new InvalidArgumentException();
        }

        $this->page_item_count = $page_item_count;
    }

    public function setMaxPagerCount(int $count)
    {
        if ($count < 1) {
            throw new InvalidArgumentException();
        }

        $this->max_pager_count = $count;
    }

    public function setCurrentPage(int $page)
    {
        $last_page = $this->getLastPage();

        if ($page >= 1 && $page <= $last_page) {
            $this->current_page = $page;
        } elseif ($page > $last_page) {
            $this->current_page = $last_page;
        } else {
            $this->current_page = 1;
        }
    }

    private function getLastPage()
    {
        if ($this->record_count > 0) {
            return (int) ceil($this->record_count / $this->page_item_count);
        } else {
            return 1;
        }
    }

    public function getPageItemCount()
    {
        return $this->page_item_count;
    }

    public function getCurrentPage()
    {
        return $this->current_page;
    }

    public function getPaginationParamName()
    {
        return $this->pagination_param_name;
    }

    public function getRecordOffset()
    {
        return ($this->current_page - 1) * $this->page_item_count;
    }

    public function getPreviousPageUrl()
    {
        $previous_page = $this->current_page - 1;

        return "{$_SERVER['SCRIPT_NAME']}?page={$previous_page}";
    }

    public function getNextPageUrl($param_name)
    {
        $next_page = $this->current_page + 1;

        return "{$_SERVER['SCRIPT_NAME']}?page={$next_page}";
    }

    public function getPageNumbers()
    {
        $last_page = $this->getLastPage();

        if ($last_page === 1) {
            return;
        }

        $current_page    = $this->current_page;
        $max_pager_count = $this->max_pager_count;

        if ($last_page > $max_pager_count) {
            $pager_count = $max_pager_count;
        } else {
            $pager_count = $last_page;
        }

        // 偶数のときは右寄り
        if (($pager_count % 2) === 0) {
            $offset_left  = ($pager_count / 2);
            $offset_right = $offset_left - 1;
        } else {
            $offset_left  = ($pager_count - 1) / 2;
            $offset_right = $offset_left;
        }

        if (($current_page - $offset_left) < 1) {
            $start_page = 1;
            $end_page   = $pager_count;
        } elseif (($current_page + $offset_right) > $last_page) {
            $end_page   = $last_page;
            $start_page = $end_page - $pager_count + 1;
        } else {
            $start_page = $current_page - $offset_left;
            $end_page   = $current_page + $offset_right;
        }

        return range($start_page, $end_page);
    }

    public function buildPageUrl($param_name, $page)
    {
        return "{$_SERVER['SCRIPT_NAME']}?{$param_name}={$page}";
    }

    public function isFirstPage()
    {
        return ($this->current_page === 1);
    }

    public function isLastPage()
    {
        return ($this->current_page === $this->getLastPage());
    }

    public function isCurrentPage($page)
    {
        return ($page === $this->current_page);
    }
}