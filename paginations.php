<?php

function get_current_page($last_page) {
    if (isset($_GET['page'])) {
        $page = (int) mb_convert_kana($_GET['page'], 'n');
        if ($page >= 1 && $page <= $last_page) {
            $current_page = $page;
        } elseif ($page < 1) {
            $current_page = 1;
        } elseif ($page > $last_page) {
            $current_page = $last_page;
        }
    } else {
        $current_page = 1;
    }

    return $current_page;
}

function get_page_numbers($current_page, $max_pager_count, $last_page) {
    if ($last_page > $max_pager_count) {
        $pager_count = $max_pager_count;
    } else {
        $pager_count = $last_page;
    }

    // 偶数のときは左寄り
    if (($pager_count % 2) === 0) {
        $offset_left  = ($pager_count / 2) - 1;
        $offset_right = $offset_left + 1;
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