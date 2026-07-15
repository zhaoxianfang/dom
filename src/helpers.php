<?php

declare(strict_types=1);

/**
 * 全局辅助函数
 *
 * 提供本库需要的全局函数（通过 composer 的 files 自动加载）。
 */

if (!function_exists('parse_json')) {
    /**
     * 解析 JSON 数据为数组
     *
     * 支持以下输入：
     * - 数组 / 对象：直接规范化返回数组
     * - JSON 字符串：成功解码为关联数组；解码失败返回 false
     * - 空字符串 / null：返回 false
     *
     * @param  mixed  $data  待解析的数据
     * @return array|false 解析成功返回数组，失败返回 false
     */
    function parse_json(mixed $data): array|false
    {
        if (is_array($data)) {
            return $data;
        }

        if (is_object($data)) {
            $decoded = json_decode(json_encode($data), true);
            return is_array($decoded) ? $decoded : false;
        }

        if (!is_string($data) || trim($data) === '') {
            return false;
        }

        $decoded = json_decode($data, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return false;
    }
}
