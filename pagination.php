<?php

class Pagination
{
    private $record_count;
    private $page_item_count = 10;
    private $max_pager_count = 5;
    private $current_page    = 1;

    public function __construct(int $record_count)
    {
        $this->setRecordCount($record_count);
    }

    public function setPageItemCount(int $count)
    {
        if ($page_item_count < 1) {
            throw new InvalidArgumentException();
        }

        $this->page_item_count = $page_item_count;
    }

    public function getPageItemCount()
    {
        return $this->page_item_count;
    }

    public function setMaxPagerCount(int $count)
    {
        // adv: setPageItemCountの方を直しちゃったけど、こっちも同様
        //      無駄なifのネストがなくなるように落とせるもの（return/Exception)は先に持ってきましょう
        //      早期リターンとかそんな感じの言葉
        // adv: $max_pager_count が 1 ってありえるの？
        //      良い悪いの話じゃなくて福田くんの設計したこのページャの仕様の話
        if ($max_pager_count >= 1) {
            $this->max_pager_count = $max_pager_count;
        } else {
            throw new InvalidArgumentException();
        }
    }

    public function setCurrentPage(int $page)
    {
        $last_page = $this->getLastPage();

        if (($page >= 1) && ($page <= $last_page)) {
            $this->current_page = $page;
        } elseif ($page > $last_page) {
            $this->current_page = $last_page;
        } else {
            $this->current_page = 1;
        }
    }

    public function getPreviousPageUrl($param_name)
    {
        $previous_page = $this->current_page - 1;

        return "{$_SERVER['SCRIPT_NAME']}?{$param_name}={$previous_page}";
    }

    public function getNextPageUrl($param_name)
    {
        $next_page = $this->current_page + 1;

        return "{$_SERVER['SCRIPT_NAME']}?{$param_name}={$next_page}";
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

    public function getRecordOffset()
    {
        return ($this->current_page - 1) * $this->page_item_count;
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

    private function setRecordCount($count)
    {
        // adv: 検索結果とか０件ってあり得ると思うけど
        if ($record_count >= 1) {
            $this->record_count = $record_count;
        } else {
            throw new InvalidArgumentException();
        }
    }

    private function getLastPage()
    {
        // adv: このifはなんのためにあるの？
        if ($this->record_count) {
            return (int) ceil($this->record_count / $this->page_item_count);
        } else {
            return 1;
        }
    }
}