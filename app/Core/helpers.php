<?php
function base_url(string $path = ''): string {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $base = ($scriptDir === '/') ? '' : rtrim($scriptDir, '/');
    return $base . '/' . ltrim($path, '/');
}

function format_vnd(int $amount): string {
    return number_format($amount, 0, ',', '.') . 'đ';
}

function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
