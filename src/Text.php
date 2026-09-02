<?php

declare(strict_types=1);

namespace zxf\Dom;

use DOMText;

/**
 * 文本节点包装类
 *
 * 提供对 DOM 文本节点的语义化操作，行为与原生 DOMText 一致，
 * 但对外暴露统一的 Node/Element 风格 API（html()/text()/remove() 等）。
 *
 * @package zxf\Dom
 */
class Text extends Node
{
    public function __construct(DOMText $node)
    {
        parent::__construct($node);
    }

    /**
     * 文本内容
     */
    public function text(): string
    {
        return $this->node->textContent ?? '';
    }

    /**
     * 文本节点渲染即其文本内容
     */
    public function html(): string
    {
        return $this->node->textContent ?? '';
    }

    /**
     * 设置文本内容
     */
    public function setText(string $text): self
    {
        $this->node->textContent = $text;

        return $this;
    }
}
