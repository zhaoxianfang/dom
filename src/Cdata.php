<?php

declare(strict_types=1);

namespace zxf\Dom;

use DOMCdataSection;

/**
 * CDATA 节点包装类
 *
 * @package zxf\Dom
 */
class Cdata extends Node
{
    public function __construct(DOMCdataSection $node)
    {
        parent::__construct($node);
    }

    /**
     * 获取 CDATA 内容
     */
    public function text(): string
    {
        return $this->node->textContent ?? '';
    }

    /**
     * 还原为完整 CDATA 标记
     */
    public function html(): string
    {
        return '<![CDATA[' . ($this->node->textContent ?? '') . ']]>';
    }
}
