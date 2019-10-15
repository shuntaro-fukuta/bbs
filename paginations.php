<?php

function get_total_record_count($db_object, $table_name) {
    $results = $db_object->query("SELECT COUNT(*) AS 'count' FROM {$table_name}")->fetch_assoc();

    return (int) $results['count'];
}

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

function get_page_records($current_page, $db_instance, $table_name, $record_count) {
    $offset  = ($current_page - 1) * $record_count;
    $records = $db_instance->query("SELECT * FROM {$table_name} ORDER BY id DESC LIMIT {$record_count} OFFSET {$offset}")->fetch_all(MYSQLI_ASSOC);

    return $records;
}

function create_pagers($current_page, $pager_count, $last_page) {
    // 偶数のときは左寄り
    if (($pager_count % 2) === 0) {
        $offset_left  = ($pager_count / 2) - 1;
        $offset_right = $offset_left + 1;
    } else {
        $offset_left  = ($pager_count - 1) / 2;
        $offset_right = $offset_left;
    }

    if (($current_page - $offset_left) < 1) {
        $start_pager = 1;
        $end_pager   = $pager_count;
    } elseif (($current_page + $offset_right) > $last_page) {
        $end_pager   = $last_page;
        $start_pager = $end_pager - $pager_count + 1;
    } else {
        $start_pager = $current_page - $offset_left;
        $end_pager   = $current_page + $offset_right;
    }

    return range($start_pager, $end_pager);
}