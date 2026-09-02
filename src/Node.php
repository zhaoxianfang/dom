<?php

declare(strict_types=1);

namespace zxf\Dom;

use DOMCdataSection;
use DOMComment;
use DOMDocument;
use DOMDocumentFragment;
use DOMElement;
use DOMNode;
use DOMText;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use zxf\Dom\Exceptions\InvalidSelectorException;
use zxf\Dom\Selectors\Query;

/**
 * DOM 节点基类
 * 
 * 提供对 DOM 节点的通用操作方法
 * 支持 DOM 元素、文本节点、注释节点等
 * 
 * 特性：
 * - PHP 8.2+ 类型系统
 * - 联合类型支持
 * - 只读属性访问
 * - 完整的节点操作 API
 * 
 * @package zxf\Dom
 */
abstract class Node
{
    /**
     * DOM 节点对象
     * 
     * @var DOMElement|DOMText|DOMComment|DOMCdataSection|DOMDocumentFragment
     */
    protected DOMElement|DOMText|DOMComment|DOMCdataSection|DOMDocumentFragment $node;

    /**
     * 构造函数
     * 
     * @param  DOMElement|DOMText|DOMComment|DOMCdataSection|DOMDocumentFragment  $node  DOM 节点
     */
    public function __construct(DOMElement|DOMText|DOMComment|DOMCdataSection|DOMDocumentFragment $node)
    {
        $this->node = $node;
    }

    /**
     * 获取 DOM 节点对象
     * 
     * @return DOMElement|DOMText|DOMComment|DOMCdataSection|DOMDocumentFragment
     */
    public function getNode(): DOMNode
    {
        return $this->node;
    }

    /**
     * 获取所属文档对象
     * 
     * @return Document
     */
    public function ownerDocument(): Document
    {
        $owner = $this->node->ownerDocument;
        if ($owner === null) {
            throw new RuntimeException('当前节点尚未挂载到文档。');
        }

        return Document::getFromDomDocument($owner);
    }

    /**
     * 设置 DOM 节点对象
     * 
     * @param  DOMElement|DOMText|DOMComment|DOMCdataSection|DOMDocumentFragment  $node  DOM 节点
     * @return void
     */
    public function setNode($node): void
    {
        if (! $node instanceof DOMNode) {
            throw new InvalidArgumentException('参数必须是 DOMNode 的实例。');
        }

        $this->node = $node;
    }

    /**
     * 检查是否为元素节点
     * 
     * @return bool
     */
    public function isElementNode(): bool
    {
        return $this->node->nodeType === XML_ELEMENT_NODE;
    }

    /**
     * 检查是否为文本节点
     * 
     * @return bool
     */
    public function isTextNode(): bool
    {
        return $this->node->nodeType === XML_TEXT_NODE;
    }

    /**
     * 检查是否为注释节点
     * 
     * @return bool
     */
    public function isCommentNode(): bool
    {
        return $this->node->nodeType === XML_COMMENT_NODE;
    }

    /**
     * 获取节点名称
     * 
     * @return string
     */
    public function getNodeName(): string
    {
        return $this->node->nodeName;
    }

    /**
     * 获取节点值
     * 
     * @return string|null
     */
    public function getNodeValue(): ?string
    {
        return $this->node->nodeValue;
    }

    /**
     * 设置节点值
     * 
     * @param  string|null  $value  节点值
     * @return self
     */
    public function setNodeValue(?string $value): self
    {
        $this->node->nodeValue = $value;
        return $this;
    }

    /**
     * 获取节点类型
     * 
     * @return int
     */
    public function getNodeType(): int
    {
        return $this->node->nodeType;
    }

    /**
     * 获取父节点
     * 
     * @return DOMNode|null
     */
    public function getParentNode(): ?DOMNode
    {
        return $this->node->parentNode;
    }

    /**
     * 获取子节点
     * 
     * @return \DOMNodeList
     */
    public function getChildNodes(): \DOMNodeList
    {
        return $this->node->childNodes;
    }

    /**
     * 获取第一个子节点
     * 
     * @return DOMNode|null
     */
    public function getFirstChild(): ?DOMNode
    {
        return $this->node->firstChild;
    }

    /**
     * 获取最后一个子节点
     * 
     * @return DOMNode|null
     */
    public function getLastChild(): ?DOMNode
    {
        return $this->node->lastChild;
    }

    /**
     * 获取下一个兄弟节点
     * 
     * @return DOMNode|null
     */
    public function getNextSibling(): ?DOMNode
    {
        return $this->node->nextSibling;
    }

    /**
     * 获取前一个兄弟节点
     * 
     * @return DOMNode|null
     */
    public function getPreviousSibling(): ?DOMNode
    {
        return $this->node->previousSibling;
    }

    /**
     * 获取文档对象
     * 
     * @return DOMDocument|null
     */
    public function getOwnerDocument(): ?DOMDocument
    {
        return $this->node->ownerDocument;
    }

    /**
     * 获取节点的 HTML 内容
     * 
     * @return string
     */
    public function html(): string
    {
        $doc = $this->node->ownerDocument;
        if ($doc === null) {
            return '';
        }

        return $doc->saveHTML($this->node);
    }

    /**
     * 获取节点的内部 HTML（不含自身标签）
     * 
     * @return string
     */
    public function innerHtml(): string
    {
        $doc = $this->node->ownerDocument;
        if ($doc === null || ! $this->node instanceof DOMElement) {
            return '';
        }

        $result = '';
        foreach ($this->node->childNodes as $child) {
            $result .= $doc->saveHTML($child);
        }

        return $result;
    }

    /**
     * innerHtml() 的别名
     */
    public function getInnerHtml(): string
    {
        return $this->innerHtml();
    }

    /**
     * 获取节点的文本内容
     * 
     * @return string
     */
    public function text(): string
    {
        return $this->node->textContent ?? '';
    }

    /**
     * 获取节点的规范化文本内容（去除多余空白）
     * 
     * @return string 规范化的文本内容
     */
    public function normalizedText(): string
    {
        $text = $this->text();
        // 替换多个空白为单个空格
        $text = preg_replace('/\s+/', ' ', $text);
        // 去除首尾空白
        return trim($text);
    }

    /**
     * 获取节点的HTML内容（格式化）
     * 
     * @param  bool  $format  是否格式化（美化输出）
     * @return string HTML内容
     */
    public function formattedHtml(bool $format = true): string
    {
        $doc = $this->node->ownerDocument;
        if ($doc === null) {
            return '';
        }

        // 保存原始格式设置
        $originalFormat = $doc->formatOutput;
        $doc->formatOutput = $format;
        
        $html = $doc->saveHTML($this->node);
        
        // 恢复原始格式设置
        $doc->formatOutput = $originalFormat;
        
        return $html;
    }

    /**
     * 获取节点的 outerHTML（包括自身）
     * 
     * @return string outerHTML 内容
     */
    public function outerHtml(): string
    {
        return $this->html();
    }

    /**
     * 获取节点在兄弟节点中的索引（从0开始）
     * 
     * @return int 索引值
     */
    public function index(): int
    {
        return $this->getNodeIndex();
    }

    /**
     * 获取节点路径（从根节点到当前节点的路径）
     * 
     * @param  string  $separator  路径分隔符
     * @return string 节点路径
     */
    public function getPath(string $separator = ' > '): string
    {
        $path = [];
        $current = $this->node;

        while ($current !== null) {
            if ($current->nodeType === XML_ELEMENT_NODE) {
                $nodeName = $current->nodeName;
                // 添加索引以区分同名兄弟节点
                $index = 1;
                $sibling = $current->previousSibling;
                while ($sibling !== null) {
                    if ($sibling->nodeName === $nodeName) {
                        $index++;
                    }
                    $sibling = $sibling->previousSibling;
                }
                array_unshift($path, $nodeName . ($index > 1 ? "[$index]" : ''));
            }
            $current = $current->parentNode;
        }

        return implode($separator, $path);
    }

    /**
     * 检查节点是否包含指定文本
     * 
     * @param  string  $text  要检查的文本
     * @param  bool  $caseSensitive  是否区分大小写
     * @return bool 如果包含返回 true
     */
    public function containsText(string $text, bool $caseSensitive = false): bool
    {
        $nodeText = $this->text();
        if (!$caseSensitive) {
            $nodeText = strtolower($nodeText);
            $text = strtolower($text);
        }
        return str_contains($nodeText, $text);
    }

    /**
     * 检查节点是否包含指定HTML
     * 
     * @param  string  $html  要检查的HTML
     * @return bool 如果包含返回 true
     */
    public function containsHtml(string $html): bool
    {
        return str_contains($this->html(), $html);
    }

    /**
     * 设置节点值
     * 
     * @param  string|int|float|bool  $value  节点值
     * @return self
     */
    public function setValue(string|int|float|bool $value): self
    {
        $this->node->nodeValue = (string) $value;
        return $this;
    }

    /**
     * 获取节点的「值」
     *
     * - 表单控件：input/textarea 取 value 属性或文本内容，select 取选中 option 的文本，
     *   checkbox/radio 取 value 属性，未选中返回空字符串；
     * - 其它元素：等同 textContent。
     *
     * @return string
     */
    public function value(): string
    {
        if (! $this->node instanceof DOMElement) {
            return $this->node->textContent ?? '';
        }

        $tag = strtolower($this->node->nodeName);

        if ($tag === 'input') {
            $type = strtolower($this->node->getAttribute('type') ?: 'text');
            if (in_array($type, ['checkbox', 'radio'], true)) {
                return $this->node->hasAttribute('checked') ? ($this->node->getAttribute('value') ?: 'on') : '';
            }
            return $this->node->getAttribute('value') ?? '';
        }

        if ($tag === 'textarea' || $tag === 'button') {
            return $this->node->textContent ?? '';
        }

        if ($tag === 'select') {
            foreach ($this->node->getElementsByTagName('option') as $option) {
                if ($option->hasAttribute('selected')) {
                    return $option->getAttribute('value') ?? $option->textContent ?? '';
                }
            }
            $first = $this->node->getElementsByTagName('option')->item(0);
            return $first !== null ? ($first->getAttribute('value') ?: $first->textContent ?? '') : '';
        }

        return $this->node->textContent ?? '';
    }

    /**
     * value() 的别名
     */
    public function getValue(): string
    {
        return $this->value();
    }

    /**
     * 设置节点的 HTML 内容
     * 
     * @param  string  $html  HTML 内容
     * @return self
     */
    public function setInnerHtml(string $html): self
    {
        if ($this->node->ownerDocument === null) {
            throw new LogicException('无法设置 HTML：节点没有所属文档。');
        }

        // 清空当前节点
        while ($this->node->firstChild !== null) {
            $this->node->removeChild($this->node->firstChild);
        }

        if ($html === '') {
            return $this;
        }

        // 创建文档片段并以 HTML 语义解析（兼容 HTML5 / void 元素 / 实体）
        $fragment = $this->createFragment($html);

        $this->node->appendChild($fragment);

        return $this;
    }

    /**
     * 设置节点的文本内容
     * 
     * @param  string  $text  文本内容
     * @return self
     */
    public function setText(string $text): self
    {
        // 清空当前节点
        while ($this->node->firstChild !== null) {
            $this->node->removeChild($this->node->firstChild);
        }

        if ($this->node->ownerDocument !== null) {
            $textNode = $this->node->ownerDocument->createTextNode($text);
            $this->node->appendChild($textNode);
        }

        return $this;
    }

    /**
     * 在当前节点前插入节点
     * 
     * @param  Node|DOMNode|array  $nodes  要插入的节点
     * @return Node|Node[]
     */
    public function before(Node|DOMNode|string|array $nodes): Node|array
    {
        $parent = $this->node->parentNode;
        
        if ($parent === null) {
            throw new RuntimeException('无法在节点前插入：节点没有父节点。');
        }

        $returnArray = is_array($nodes);
        if (! is_array($nodes)) {
            $nodes = [$nodes];
        }

        $result = [];
        $document = $this->node->ownerDocument;

        foreach (array_reverse($nodes) as $node) {
            if ($node instanceof Node) {
                $node = $node->getNode();
            } elseif (is_string($node)) {
                $node = $this->createFragment($node);
            }

            if (! $node instanceof DOMNode) {
                throw new InvalidArgumentException('参数必须是 Node、DOMNode 实例或 HTML 字符串。');
            }

            $node = $document->importNode($node, true);
            $parent->insertBefore($node, $this->node);
            $result[] = $this->wrapImportedNode($node);
        }

        return $returnArray ? $result : $result[0];
    }

    /**
     * 在当前节点后插入节点
     * 
     * 支持传入 Node/DOMNode 实例或 HTML 字符串（字符串会被解析为 DOM 片段后插入）。
     * 
     * @param  Node|DOMNode|string|array  $nodes  要插入的节点或 HTML 字符串
     * @return Node|Node[]
     */
    public function after(Node|DOMNode|string|array $nodes): Node|array
    {
        $parent = $this->node->parentNode;
        
        if ($parent === null) {
            throw new RuntimeException('无法在节点后插入：节点没有父节点。');
        }

        $returnArray = is_array($nodes);
        if (! is_array($nodes)) {
            $nodes = [$nodes];
        }

        $result = [];
        $document = $this->node->ownerDocument;
        $referenceNode = $this->node->nextSibling;

        foreach ($nodes as $node) {
            if ($node instanceof Node) {
                $node = $node->getNode();
            } elseif (is_string($node)) {
                $node = $this->createFragment($node);
            }

            if (! $node instanceof DOMNode) {
                throw new InvalidArgumentException('参数必须是 Node、DOMNode 实例或 HTML 字符串。');
            }

            $node = $document->importNode($node, true);
            
            if ($referenceNode === null) {
                $parent->appendChild($node);
            } else {
                $parent->insertBefore($node, $referenceNode);
            }

            $result[] = $this->wrapImportedNode($node);
        }

        return $returnArray ? $result : $result[0];
    }

    /**
     * 在当前节点开头插入子节点
     * 
     * @param  Node|DOMNode|string|array  $nodes  要插入的节点或 HTML 字符串
     * @return Node|Node[]
     */
    public function prepend(Node|DOMNode|string|array $nodes): Node|array
    {
        return $this->prependChild($nodes);
    }

    /**
     * 在当前节点末尾添加子节点
     * 
     * @param  Node|DOMNode|string|array  $nodes  要添加的节点或 HTML 字符串
     * @return Node|Node[]
     */
    public function append(Node|DOMNode|string|array $nodes): Node|array
    {
        return $this->appendChild($nodes);
    }

    /**
     * 添加子节点到开头
     * 
     * @param  Node|DOMNode|string|array  $nodes  要添加的节点或 HTML 字符串
     * @return Node|Node[]
     */
    public function prependChild(Node|DOMNode|string|array $nodes): Node|array
    {
        $returnArray = is_array($nodes);
        if (! is_array($nodes)) {
            $nodes = [$nodes];
        }

        $nodes = array_reverse($nodes);
        $result = [];
        $referenceNode = $this->node->firstChild;
        $document = $this->node->ownerDocument;

        foreach ($nodes as $node) {
            if ($node instanceof Node) {
                $node = $node->getNode();
            } elseif (is_string($node)) {
                $node = $this->createFragment($node);
            }

            if (! $node instanceof DOMNode) {
                throw new InvalidArgumentException('参数必须是 Node、DOMNode 实例或 HTML 字符串。');
            }

            $node = $document->importNode($node, true);
            
            if ($referenceNode === null) {
                $this->node->appendChild($node);
            } else {
                $this->node->insertBefore($node, $referenceNode);
            }

            $result[] = $this->wrapImportedNode($node);
            $referenceNode = $this->node->firstChild;
        }

        return $returnArray ? $result : $result[0];
    }

    /**
     * 添加子节点到末尾
     * 
     * @param  Node|DOMNode|string|array  $nodes  要添加的节点或 HTML 字符串
     * @return Node|Node[]
     */
    public function appendChild(Node|DOMNode|string|array $nodes): Node|array
    {
        $returnArray = is_array($nodes);
        if (! is_array($nodes)) {
            $nodes = [$nodes];
        }

        $result = [];
        $document = $this->node->ownerDocument;

        foreach ($nodes as $node) {
            if ($node instanceof Node) {
                $node = $node->getNode();
            } elseif (is_string($node)) {
                $node = $this->createFragment($node);
            }

            if (! $node instanceof DOMNode) {
                throw new InvalidArgumentException('参数必须是 Node、DOMNode 实例或 HTML 字符串。');
            }

            $node = $document->importNode($node, true);
            $this->node->appendChild($node);
            $result[] = $this->wrapImportedNode($node);
        }

        return $returnArray ? $result : $result[0];
    }

    /**
     * 替换当前节点
     * 
     * 支持传入 Node/DOMNode 实例或 HTML 字符串。传入数组时，当前节点会被替换为数组中所有节点的序列。
     * 
     * @param  Node|DOMNode|string|array  $nodes  新节点或 HTML 字符串
     * @return self
     */
    public function replaceWith(Node|DOMNode|string|array $nodes): self
    {
        $this->after($nodes);
        $this->remove();
        return $this;
    }

    /**
     * 创建当前文档下的 HTML 片段节点
     * 
     * 采用与文档 loadHtml 一致的解析参数，保证 HTML5/void 元素语义正确。
     * 
     * @param  string  $html  HTML 片段
     * @return DOMDocumentFragment
     */
    protected function createFragment(string $html): DOMDocumentFragment
    {
        $owner = $this->node->ownerDocument;
        if ($owner === null) {
            throw new RuntimeException('当前节点尚未挂载到文档，无法创建 HTML 片段。');
        }

        $document = Document::getFromDomDocument($owner);
        $fragment = $owner->createDocumentFragment();

        if ($html === '') {
            return $fragment;
        }

        // 以 HTML 语义解析片段（兼容 void 元素 / 实体 / 中文），
        // 再用 importNode 将解析结果并入当前文档片段，避免 appendXML 对 HTML 语法（<br> 等）失效。
        $encoding = $document->getEncoding();
        $tmp = new DOMDocument($encoding);
        $tmp->preserveWhiteSpace = false;
        $fragmentHtml = '<?xml encoding="' . $encoding . '" ?>'
            . '<div>' . $html . '</div>';
        @$tmp->loadHTML($fragmentHtml, Document::HTML_LOAD_OPTIONS);

        $root = $tmp->getElementsByTagName('div')->item(0);
        if ($root !== null) {
            foreach ($root->childNodes as $child) {
                $fragment->appendChild($owner->importNode($child, true));
            }
        }

        return $fragment;
    }

    /**
     * 移除当前节点
     * 
     * @return self
     */
    public function remove(): self
    {
        $parent = $this->node->parentNode;
        
        if ($parent !== null) {
            $parent->removeChild($this->node);
        }

        return $this;
    }

    /**
     * 克隆当前节点
     * 
     * @param  bool  $deep  是否深度克隆（包含子节点）
     * @return Node
     */
    public function clone(bool $deep = true): Node
    {
        $clonedNode = $this->node->cloneNode($deep);
        return $this->wrapImportedNode($clonedNode);
    }

    /**
     * 节点在父节点的子节点列表中的索引
     * 
     * @return int
     */
    public function getNodeIndex(): int
    {
        $index = 0;
        $node = $this->node->previousSibling;

        while ($node !== null) {
            if ($node->nodeType === XML_ELEMENT_NODE) {
                $index++;
            }
            $node = $node->previousSibling;
        }

        return $index;
    }

    /**
     * 检查节点是否匹配选择器
     * 
     * @param  string  $selector  选择器
     * @param  string|bool  $typeOrStrict  选择器类型或严格模式标志（子类可覆盖）
     * @return bool
     */
    public function matches(string $selector, string|bool $typeOrStrict = Query::TYPE_CSS): bool
    {
        if ($this->node->ownerDocument === null) {
            return false;
        }

        $document = Document::getFromDomDocument($this->node->ownerDocument);
        if ($document === null) {
            return false;
        }

        $type = is_string($typeOrStrict) ? $typeOrStrict : Query::TYPE_CSS;
        $context = ($this->node->parentNode instanceof DOMElement) ? $this->node->parentNode : null;
        $elements = $document->find($selector, $type, $context);

        // 通过比较底层 DOM 节点来判断是否匹配，避免因对象实例不同（in_array 严格比较）而误判
        foreach ($elements as $element) {
            if ($element instanceof Node && $element->getNode() === $this->node) {
                return true;
            }
        }

        return false;
    }

    /**
     * 查找最近的祖先元素（含自身）匹配给定选择器
     * 
     * @param  string  $selector  CSS 或 XPath 选择器
     * @param  string  $type  选择器类型
     * @return Element|null
     */
    public function closest(string $selector, string $type = Query::TYPE_CSS): ?Element
    {
        $node = $this->node;
        while ($node !== null) {
            if ($node instanceof DOMElement) {
                $element = new Element($node);
                if ($element->matches($selector, $type)) {
                    return $element;
                }
            }
            $node = $node->parentNode;
        }

        return null;
    }

    /**
     * 获取所有祖先元素（由近及远）
     * 
     * @return array<int, Element>
     */
    public function ancestors(): array
    {
        $result = [];
        $node = $this->node->parentNode;
        while ($node instanceof DOMElement) {
            $result[] = new Element($node);
            $node = $node->parentNode;
        }

        return $result;
    }

    /**
     * 获取所有祖先元素（由近及远），可指定选择器过滤
     * 
     * @param  string  $selector  CSS 或 XPath 选择器
     * @param  string  $type  选择器类型
     * @return array<int, Element>
     */
    public function parents(string $selector = '', string $type = Query::TYPE_CSS): array
    {
        $all = $this->ancestors();
        if ($selector === '') {
            return $all;
        }

        return array_values(array_filter($all, fn (Element $el) => $el->matches($selector, $type)));
    }

    /**
     * 获取所有兄弟元素（不含自身）
     * 
     * @return array<int, Element>
     */
    public function siblings(): array
    {
        $parent = $this->node->parentNode;
        if (! $parent instanceof DOMElement) {
            return [];
        }

        $result = [];
        foreach ($parent->childNodes as $child) {
            if ($child instanceof DOMElement && $child !== $this->node) {
                $result[] = new Element($child);
            }
        }

        return $result;
    }

    /**
     * 获取后一个兄弟元素
     * 
     * @return Element|null
     */
    public function next(): ?Element
    {
        $node = $this->node->nextSibling;
        while ($node !== null) {
            if ($node instanceof DOMElement) {
                return new Element($node);
            }
            $node = $node->nextSibling;
        }

        return null;
    }

    /**
     * 获取前一个兄弟元素
     * 
     * @return Element|null
     */
    public function previous(): ?Element
    {
        $node = $this->node->previousSibling;
        while ($node !== null) {
            if ($node instanceof DOMElement) {
                return new Element($node);
            }
            $node = $node->previousSibling;
        }

        return null;
    }

    /**
     * 相对于当前节点执行 CSS 选择器查询（等价于 querySelectorAll）
     * 
     * @param  string  $selector  CSS 选择器
     * @return array<int, Element>
     */
    public function querySelectorAll(string $selector): array
    {
        return $this->ownerDocument()->find($selector, Query::TYPE_CSS, $this->getNode());
    }

    /**
     * 相对于当前节点执行 CSS 选择器查询，返回第一个匹配
     * 
     * @param  string  $selector  CSS 选择器
     * @return Element|null
     */
    public function querySelector(string $selector): ?Element
    {
        return $this->ownerDocument()->first($selector, Query::TYPE_CSS, $this->getNode());
    }

    /**
     * 包装导入的节点
     * 
     * 元素节点统一包装为 Element，文本/注释等节点包装为对应的 Node 子类，
     * 保证返回对象具备完整的类型方法，避免链式调用因匿名类丢失方法而崩溃。
     * 
     * @param  DOMNode  $node  导入的节点
     * @return Node
     */
    protected function wrapImportedNode(DOMNode $node): Node
    {
        if ($node instanceof DOMElement) {
            return new Element($node);
        }

        if ($node instanceof DOMText) {
            return new Text($node);
        }

        if ($node instanceof DOMComment) {
            return new Comment($node);
        }

        if ($node instanceof DOMCdataSection) {
            return new Cdata($node);
        }

        return new class($node) extends Node {};
    }

    /**
     * 魔术方法：获取属性
     * 
     * @param  string  $name  属性名
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'tag' => strtolower($this->node->nodeName),
            'name' => $this->node->nodeName,
            'value' => $this->node->nodeValue,
            'text' => $this->text(),
            'html' => $this->html(),
            default => null,
        };
    }

    /**
     * 魔术方法：转换为字符串
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->html();
    }
}
