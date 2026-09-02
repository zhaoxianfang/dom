<?php

declare(strict_types=1);

namespace zxf\Dom;

use DOMComment;

/**
 * 注释节点包装类
 *
 * @package zxf\Dom
 */
class Comment extends Node
{
    public function __construct(DOMComment $node)
    {
        parent::__construct($node);
    }

    /**
     * 获取注释内容（不含 <!-- -->）
     */
    public function text(): string
    {
        return $this->node->textContent ?? '';
    }

    /**
     * 还原为完整注释标记
     */
    public function html(): string
    {
        return '<!--' . ($this->node->textContent ?? '') . '-->';
    }
}
