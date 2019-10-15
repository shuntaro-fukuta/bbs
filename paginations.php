<?php

function paginate($settings) {
    $db_instance       = $settings['db_instance'];
    $table_name        = $settings['table_name'];
    $page_record_count = $settings['page_record_count'];
    $max_pager_count   = $settings['max_pager_count'];

    $total_record_count = get_total_record_count($db_instance, $table_name);

    if ($total_record_count === 0) {
        return;
    }

    $first_page = 1;
    $last_page  = (int) ceil($total_record_count / $page_record_count);

    $current_page = get_current_page($first_page, $last_page);

    $results = [];

    $results['records'] = get_page_records($current_page, $db_instance, $table_name, $page_record_count);

    if ($last_page === $first_page) {
        return $results;
    }

    $results['pagers'] = [];

    if ($last_page > $max_pager_count) {
        $pager_count = $max_pager_count;
    } else {
        $pager_count = $last_page;
    }

    $results['pagers'] = create_pagers($current_page, $pager_count, $first_page, $last_page);

    return $results;
}

function get_total_record_count($db_object, $table_name) {
    $results = $db_object->query("SELECT COUNT(*) AS 'count' FROM {$table_name}")->fetch_assoc();

    return (int) $results['count'];
}

function get_current_page($first_page, $last_page) {
    if (isset($_GET['page'])) {
        $page = (int) mb_convert_kana($_GET['page'], 'n');
        if ($page >= $first_page && $page <= $last_page) {
            $current_page = $page;
        } elseif ($page < $first_page) {
            $current_page = $first_page;
        } elseif ($page > $last_page) {
            $current_page = $last_page;
        }
    } else {
        $current_page = $first_page;
    }

    return $current_page;
}

function get_page_records($current_page, $db_instance, $table_name, $record_count) {
    $offset  = ($current_page - 1) * 10;
    $records = $db_instance->query("SELECT * FROM {$table_name} ORDER BY id DESC LIMIT {$record_count} OFFSET {$offset}")->fetch_all(MYSQLI_ASSOC);

    return $records;
}

function create_pagers($current_page, $pager_count, $first_page, $last_page) {
    $pagers = [];

    if ($current_page !== $first_page) {
        $prev_page = $current_page - 1;
        $pagers[]  = "<a href='{$_SERVER['SCRIPT_NAME']}?page={$prev_page}'>&lt;</a>";
    }

    // 偶数のときは左寄り
    if ($pager_count % 2 === 0) {
        $offset_left  = ($pager_count / 2) - 1;
        $offset_right = $offset_left + 1;
    } else {
        $offset_left  = ($pager_count - 1) / 2;
        $offset_right = $offset_left;
    }

    if ($current_page - $offset_left < 1) {
        $start_pager = $first_page;
        $end_pager   = $pager_count;
    } elseif ($current_page + $offset_right > $last_page) {
        $end_pager   = $last_page;
        $start_pager = $end_pager - $pager_count + 1;
    } else {
        $start_pager = $current_page - $offset_left;
        $end_pager   = $current_page + $offset_right;
    }

    for ($pager = $start_pager; $pager <= $end_pager; $pager++) {
        if ($pager === $current_page) {
            $pagers[] = $pager;
        } else {
            $pagers[] = "<a href='{$_SERVER['SCRIPT_NAME']}?page={$pager}'>{$pager}</a>";
        }
    }

    if ($current_page !== $last_page) {
        $next_page = $current_page + 1;
        $pagers[]  = "<a href='{$_SERVER['SCRIPT_NAME']}?page={$next_page}'>&gt;</a>";
    }

    return $pagers;
}