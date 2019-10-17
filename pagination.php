<?php

class Pagination
{
    private $record_count;
    private $page_item_count = 10;
    private $max_pager_count = 5;

    public function __construct(int $record_count)
    {
        $this->record_count = $record_count;
    }

    public function setPageItemCount(int $count)
    {
        if ($count) {
            $this->page_item_count = $count;
        }
    }

    public function getPageItemCount()
    {
        return $this->page_item_count;
    }

    public function setMaxPagerCount(int $count)
    {
        if ($count) {
            $this->max_pager_count = $count;
        }
    }

    public function getPreviousPage()
    {
        return ($this->getCurrentPage() - 1);
    }

    public function getNextPage()
    {
        return ($this->getCurrentPage() + 1);
    }

    public function getPageNumbers()
    {
        $last_page = $this->getLastPage();

        if ($last_page === 1) {
            return;
        }

        $current_page    = $this->getCurrentPage();
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

    public function getRecordOffset()
    {
        return ($this->getCurrentPage() - 1) * $this->page_item_count;
    }

    public function isFirstPage()
    {
        return ($this->getCurrentPage() === 1);
    }

    public function isLastPage()
    {
        return ($this->getCurrentPage() === $this->getLastPage());
    }

    public function isCurrentPage($page)
    {
        return ($page === $this->getCurrentPage());
    }

    private function getCurrentPage()
    {
        $page = (int) filter_input(INPUT_GET, 'page');

        $last_page = $this->getLastPage();

        if (($page >= 1) && ($page <= $last_page)) {
            $current_page = $page;
        } elseif ($page > $last_page) {
            $current_page = $last_page;
        } else {
            $current_page = 1;
        }

        return $current_page;
    }

    private function getLastPage()
    {
        if ($this->record_count) {
            return (int) ceil($this->record_count / $this->page_item_count);
        } else {
            return 1;
        }
    }
}