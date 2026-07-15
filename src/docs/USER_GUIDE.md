# zxf/dom 完整使用指南

## 目录

- [简介](#简介)
- [安装](#安装)
- [快速开始](#快速开始)
- [核心概念](#核心概念)
- [文档操作](#文档操作)
- [元素查询](#元素查询)
- [元素操作](#元素操作)
- [属性操作](#属性操作)
- [类名操作](#类名操作)
- [样式操作](#样式操作)
- [节点操作](#节点操作)
- [文档片段](#文档片段)
- [编码处理](#编码处理)
- [错误处理](#错误处理)
- [高级用法](#高级用法)
- [选择器数组回退查找](#选择器数组回退查找)
- [智能选择器类型检测](#智能选择器类型检测)
- [最佳实践](#最佳实践)
- [常见问题](#常见问题)

---

## 简介

zxf/dom 是一个功能强大、易于使用的 PHP DOM 操作库，提供简洁的 API 来解析、查询和操作 HTML/XML 文档。

### 主要特性

- ✅ **完整的 CSS3 选择器支持** - 支持 70+ 种 CSS 选择器
- ✅ **原生 XPath 支持** - 可直接使用 XPath 表达式查询
- ✅ **丰富的伪类** - 支持 60+ 伪类选择器
- ✅ **伪元素支持** - 支持 `::text` 和 `::attr()` 伪元素
- ✅ **链式调用** - 流畅的 API 设计，支持链式操作
- ✅ **PHP 8.2+ 类型系统** - 完整的类型注解，更好的 IDE 支持
- ✅ **HTML/XML 双模式** - 同时支持 HTML 和 XML 文档处理
- ✅ **高性能** - 选择器编译缓存，提升查询速度
- ✅ **UTF-8 编码支持** - 完善的中文等多字节字符支持

---

## 安装

### 使用 Composer 安装

```bash
composer require zxf/dom
```

### 手动安装

下载源代码，然后引入文件：

```php
require_once 'path/to/Query.php';
require_once 'path/to/Document.php';
require_once 'path/to/Element.php';
require_once 'path/to/Node.php';
require_once 'path/to/Selectors/Query.php';
require_once 'path/to/Attributes/ClassAttribute.php';
require_once 'path/to/Attributes/StyleAttribute.php';
require_once 'path/to/Fragments/DocumentFragment.php';
require_once 'path/to/Utils/Encoder.php';
require_once 'path/to/Utils/Errors.php';
require_once 'path/to/Exceptions/InvalidSelectorException.php';

use zxf\Dom\Selectors\Query;
use zxf\Dom\Document;

// 初始化 Query
Query::initialize();
```

---

## 快速开始

### 基本用法

```php
use zxf\Dom\Document;

// 从 HTML 字符串创建文档
$doc = new Document('<div class="container"><p>Hello World</p></div>');

// 查找元素
$elements = $doc->find('.container p');
echo $elements[0]->text(); // 输出: Hello World

// 获取第一个元素
$element = $doc->first('.container');
echo $element->html(); // 输出: <p>Hello World</p>

// 使用伪元素获取文本
$text = $doc->text('.container p::text');
echo $text; // 输出: Hello World

// 使用伪元素获取属性
$html = '<a href="https://example.com">Link</a>';
$doc = new Document($html);
$url = $doc->text('a::attr(href)');
echo $url; // 输出: https://example.com
```

### JSON 数据处理
```php
$document = new Document('https://www.xxx.com/your/path/file.json', true);
// 直接返回 json 数组
/** var $json array|false */
$json = $document->json();

// 使用 queryWithFallback
/** var $json array|false */
$json = $document->queryWithFallback([
    [
        'type' => 'json', // 只需要指定 一个 type 为 json 即可
    ],
    [
        ...
    ],
]);
dd($json);
```
### XML 文档处理

```php
$xml = '<root><item id="1">Item 1</item><item id="2">Item 2</item></root>';
$doc = new Document($xml, false, 'UTF-8', Document::TYPE_XML);

$items = $doc->find('item');
foreach ($items as $item) {
    echo $item->attr('id') . ': ' . $item->text() . "\n";
}
// 输出:
// 1: Item 1
// 2: Item 2
```

### 链式调用

```php
$doc = new Document('<div class="container"><p>Text</p></div>');

// Document 链式调用
$doc->addClass('.container', 'active')
    ->addClass('.container', 'highlight')
    ->css('.container', 'color', 'red');

// Element 链式调用
$element = $doc->first('.container');
$element->addClass('class1')
        ->addClass('class2')
        ->css('background', 'blue')
        ->attr('data-id', '123');
```

---

## 核心概念

### Document（文档）

Document 类代表整个 HTML/XML 文档，是所有操作的入口点。

```php
use zxf\Dom\Document;

$doc = new Document('<div>内容</div>');
```

### Element（元素）

Element 类代表文档中的一个元素节点。

```php
$element = $doc->first('div');
echo $element->text();
```

### Node（节点）

Node 类是 Element 和其他节点类型的基类。

### Query（查询）

Query 类负责将 CSS 选择器转换为 XPath 表达式。

```php
use zxf\Dom\Selectors\Query;

Query::initialize();
$xpath = Query::compile('.item.active');
```

---

## 文档操作

### 创建文档

```php
// 从 HTML 字符串创建
$doc = new Document('<div>内容</div>');

// 从文件创建
$doc = new Document('path/to/file.html', true);

// 从 XML 字符串创建
$doc = new Document('<root><item>数据</item></root>', false, 'UTF-8', Document::TYPE_XML);

// 从 XML 文件创建
$doc = new Document('path/to/file.xml', true, 'UTF-8', Document::TYPE_XML);

// 创建空文档
$doc = new Document();
```

### 加载内容

```php
// 加载 HTML 字符串
$doc->load('<div>新内容</div>');

// 加载 HTML 文件
$doc->load('path/to/file.html', true);

// 加载 XML 字符串
$doc->load('<root><data>内容</data></root>', false, null, Document::TYPE_XML);

// 加载 XML 文件
$doc->load('path/to/file.xml', true, null, Document::TYPE_XML);
```

### 保存文档

```php
// 保存为 HTML 文件
$doc->save('path/to/output.html');

// 保存为 XML 文件
$doc->type = Document::TYPE_XML;
$doc->save('path/to/output.xml');
```

### 获取文档内容

```php
// 获取整个文档的 HTML
$html = $doc->html();

// 获取整个文档的文本
$text = $doc->text();

// 获取文档标题
$title = $doc->title();

// 获取文档元数据
$meta = $doc->meta();
```

---

## 元素查询

### 查找元素

```php
// 使用 CSS 选择器查找所有匹配元素
$elements = $doc->find('div');
$elements = $doc->find('.class');
$elements = $doc->find('#id');
$elements = $doc->find('div > p');

// 使用 XPath 查找元素
$elements = $doc->xpath('//div[@class="container"]');

// 获取第一个匹配元素
$element = $doc->first('div');
$element = $doc->first('.container');

// 获取最后一个匹配元素
$element = $doc->last('div');
```

### 使用伪元素

```php
// 获取元素的文本内容
$text = $doc->text('div::text');
$text = $doc->text('a::text');

// 获取元素的属性值
$href = $doc->text('a::attr(href)');
$src = $doc->text('img::attr(src)');
$dataId = $doc->text('div::attr(data-id)');
```

### 高级查询

```php
// 使用属性选择器
$elements = $doc->find('[href]'); // 有 href 属性
$elements = $doc->find('[data-id="123"]'); // 属性值等于
$elements = $doc->find('[class~="active"]'); // 类名包含
$elements = $doc->find('[href^="https"]'); // href 以 https 开头
$elements = $doc->find('[src$=".jpg"]'); // src 以 .jpg 结尾
$elements = $doc->find('[class*="nav"]'); // class 包含 nav

// 使用伪类
$elements = $doc->find('li:first-child'); // 第一个子元素
$elements = $doc->find('li:last-child'); // 最后一个子元素
$elements = $doc->find('li:nth-child(odd)'); // 奇数位置
$elements = $doc->find('li:contains(文本)'); // 包含文本
$elements = $doc->find('div:not(.active)'); // 不包含 active 类
$elements = $doc->find('div:has(a)'); // 包含 a 元素

// 组合选择器
$elements = $doc->find('div.container > p.highlight');
$elements = $doc->find('div#main ul.nav > li.item.active');
```

---

## 元素操作

### 获取内容

```php
$element = $doc->first('div');

// 获取文本内容
$text = $element->text();

// 获取 HTML 内容
$html = $element->html();

// 获取外部 HTML（包括自身）
$outerHtml = $element->toHtml();
```

### 修改内容

```php
$element = $doc->first('div');

// 设置文本内容
$element->setValue('新文本');

// 设置 HTML 内容
$element->setHtml('<p>新的 HTML</p>');

// 使用 Document 方法设置内容
$doc->setContent('div', '新内容');
```

### 创建元素

```php
// 创建新元素
$div = $doc->createElement('div', '内容');
$div = $doc->createElement('div', '内容', ['class' => 'container', 'id' => 'main']);
$div = $doc->createElement('a', '链接', ['href' => 'https://example.com', 'target' => '_blank']);

// 创建文本节点
$textNode = $doc->createTextNode('纯文本');
```

### 添加元素

```php
$container = $doc->first('.container');
$newElement = $doc->createElement('p', '新段落');

// 添加到末尾
$container->append($newElement);

// 添加到开头
$container->prepend($newElement);

// 在元素后插入
$element->after($newElement);

// 在元素前插入
$element->before($newElement);
```

### 克隆和删除

```php
// 克隆元素
$element = $doc->first('div');
$cloned = $element->clone();

// 移除元素
$element->remove();

// 清空元素内容
$element->empty();
```

---

## 属性操作

### 获取属性

```php
$element = $doc->first('a');

// 获取属性
$href = $element->attr('href');
$class = $element->attr('class');
$dataId = $element->getAttribute('data-id');

// 获取所有属性
$allAttrs = $element->attributes();
```

### 设置属性

```php
$element = $doc->first('div');

// 设置属性
$element->attr('class', 'new-class');
$element->attr('data-id', '123');
$element->setAttribute('title', '提示信息');

// 设置多个属性
$element->attrs([
    'class' => 'container',
    'id' => 'main',
    'data-value' => 'test'
]);
```

### 删除属性

```php
$element = $doc->first('div');

// 删除属性
$element->removeAttr('class');
$element->removeAttribute('data-id');

// 使用 Document 方法删除
$doc->removeAttr('div', 'title');
```

### 检查属性

```php
$element = $doc->first('div');

// 检查属性是否存在
$hasId = $element->hasAttribute('id');
$hasClass = $element->hasAttribute('class');

// 检查特定属性值
$isRequired = $element->attr('required') !== null;
```

---

## 类名操作

### 添加类名

```php
$element = $doc->first('div');

// 使用便捷方法
$element->addClass('active');
$element->addClass('highlight', 'large');

// 使用 ClassAttribute
$element->classes()->add('active');
$element->classes()->add('highlight', 'large');

// 使用 Document 方法
$doc->addClass('div', 'active');
```

### 移除类名

```php
$element = $doc->first('div');

// 使用便捷方法
$element->removeClass('active');

// 使用 ClassAttribute
$element->classes()->remove('active');

// 使用 Document 方法
$doc->removeClass('div', 'active');
```

### 检查类名

```php
$element = $doc->first('div');

// 使用便捷方法
$hasClass = $element->hasClass('active');

// 使用 ClassAttribute
$hasClass = $element->classes()->has('active');

// 使用 Document 方法
$hasClass = $doc->hasClass('div', 'active');
```

### 切换类名

```php
$element = $doc->first('div');

// 使用 ClassAttribute
$element->classes()->toggle('active');
```

### 获取所有类名

```php
$element = $doc->first('div');

// 获取所有类名数组
$classes = $element->classes()->all();
// ['class1', 'class2', 'active']

// 获取类名字符串
$classString = $element->attr('class');
// 'class1 class2 active'
```

### 清空类名

```php
$element = $doc->first('div');

// 使用 ClassAttribute
$element->classes()->clear();
```

---

## 样式操作

### 设置样式

```php
$element = $doc->first('div');

// 使用便捷方法
$element->css('color', 'red');
$element->css('font-size', '16px');

// 使用 StyleAttribute
$element->style()->set('color', 'red');
$element->style()->set('font-size', '16px');

// 设置多个样式
$element->style()->set([
    'color' => 'red',
    'background' => 'blue',
    'font-size' => '16px'
]);

// 使用 Document 方法
$doc->css('div', 'color', 'red');
```

### 获取样式

```php
$element = $doc->first('div');

// 使用便捷方法
$color = $element->css('color');

// 使用 StyleAttribute
$color = $element->style()->get('color');

// 使用 Document 方法
$color = $doc->css('div', 'color');

// 获取所有样式
$allStyles = $element->style()->all();
```

### 删除样式

```php
$element = $doc->first('div');

// 使用 StyleAttribute
$element->style()->remove('color');

// 设置为 null 来删除
$element->style()->set('color', null);
```

### 驼峰命名

StyleAttribute 支持驼峰命名，会自动转换为短横线命名：

```php
$element->style()->set('backgroundColor', 'red');
$element->style()->set('fontSize', '16px');
$element->style()->set('borderRadius', '5px');
```

---

## 节点操作

### 遍历节点

```php
$element = $doc->first('div');

// 获取父元素
$parent = $element->parent();

// 获取第一个子元素
$firstChild = $element->firstChild();

// 获取最后一个子元素
$lastChild = $element->lastChild();

// 获取下一个兄弟元素
$nextSibling = $element->nextSibling();

// 获取前一个兄弟元素
$previousSibling = $element->previousSibling();

// 获取所有兄弟元素
$siblings = $element->siblings();

// 获取所有子元素
$children = $element->children();
```

### 节点位置

```php
$element = $doc->first('div');

// 获取节点在兄弟节点中的索引（从 0 开始）
$index = $element->index();

// 获取文档根元素
$root = $doc->root();
```

### 节点信息

```php
$element = $doc->first('div');

// 获取标签名
$tagName = $element->tagName();

// 获取节点类型
$isElement = $element->isElementNode();
$isText = $element->isTextNode();
$isComment = $element->isCommentNode();

// 检查节点是否匹配选择器
$matches = $element->matches('.active');
```

---

## 文档片段

DocumentFragment 允许你创建和操作文档片段，然后一次性插入到文档中。

### 创建片段

```php
use zxf\Dom\Fragments\DocumentFragment;

$fragment = new DocumentFragment($doc);

// 添加内容
$fragment->append('<p>段落 1</p>');
$fragment->append('<p>段落 2</p>');

// 添加元素
$div = $doc->createElement('div', '内容');
$fragment->append($div);
```

### 插入片段

```php
$container = $doc->first('.container');
$container->append($fragment);
```

---

## 编码处理

### UTF-8 编码

zxf/dom 默认使用 UTF-8 编码处理所有文档。

```php
// 创建 UTF-8 文档
$doc = new Document('<div>中文内容</div>', false, 'UTF-8');

// 处理中文
$text = $doc->text('.div');
echo $text; // 输出: 中文内容
```

### 编码转换

```php
use zxf\Dom\Utils\Encoder;

// HTML 实体编码
$html = Encoder::encodeHtml('<script>alert("XSS")</script>');
// &lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;

// HTML 实体解码
$html = Encoder::decodeHtml('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;');
// <script>alert("XSS")</script>

// URL 编码
$url = Encoder::encodeUrl('中文内容');
// %E4%B8%AD%E6%96%87%E5%86%85%E5%AE%B9

// URL 解码
$url = Encoder::decodeUrl('%E4%B8%AD%E6%96%87%E5%86%85%E5%AE%B9');
// 中文内容
```

---

## 错误处理

### 异常处理

zxf/dom 使用异常来报告错误。

```php
use zxf\Dom\Exceptions\InvalidSelectorException;

try {
    $elements = $doc->find('invalid::selector');
} catch (InvalidSelectorException $e) {
    echo '选择器错误: ' . $e->getMessage();
}

try {
    $doc->load('non-existent-file.html', true);
} catch (\RuntimeException $e) {
    echo '加载错误: ' . $e->getMessage();
}
```

### 错误配置

```php
use zxf\Dom\Utils\Errors;

// 静默处理错误
Errors::silence();

// 启用日志
Errors::setLoggingEnabled(true);

// 设置日志文件
Errors::setLogFile('/path/to/log.txt');

// 设置自定义错误处理器
Errors::setErrorHandler(function($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile:$errline");
});
```

---

## 高级用法

### 复杂选择器组合

```php
// 多条件选择器
$elements = $doc->find('div.container > p.highlight.active');

// 使用伪类
$elements = $doc->find('ul > li:nth-child(odd):not(.disabled)');

// 组合使用
$elements = $doc->find('div:has(a[href^="https"])');
```

### 数据提取

```php
$html = '
<table>
    <tr><td>ID</td><td>名称</td><td>价格</td></tr>
    <tr><td>1</td><td>产品 A</td><td>100</td></tr>
    <tr><td>2</td><td>产品 B</td><td>200</td></tr>
</table>';

$doc = new Document($html);
$rows = $doc->find('table tr:not(:first-child)');

$data = [];
foreach ($rows as $row) {
    $cells = $row->find('td');
    $data[] = [
        'id' => $cells[0]->text(),
        'name' => $cells[1]->text(),
        'price' => $cells[2]->text()
    ];
}

print_r($data);
// [
//     ['id' => '1', 'name' => '产品 A', 'price' => '100'],
//     ['id' => '2', 'name' => '产品 B', 'price' => '200']
// ]
```

### 元素匹配 matches

`matches()` 用于判断**当前元素（或节点）**是否匹配给定的 CSS 选择器，常用于遍历 `find()` 结果后做二次过滤。

- `Element::matches(string $selector, bool|string $strict = false): bool`
- `Node::matches(string $selector, bool|string $typeOrStrict = Query::TYPE_CSS): bool`

**严格模式（推荐，第二个参数传 `true`）**：直接基于已解析的 DOM 节点做精确匹配，性能更好，且语义正确：

- **标签**：必须与元素标签一致（或选择器为 `*`）。
- **ID**：仅当选择器显式写了 `#id` 才校验 id；元素自身是否带 id 不影响匹配。
- **类名**：选择器要求的**每一个**类名都必须在元素中存在（元素可包含额外类名）。
- **属性**：
  - `[attr]` —— 仅要求属性存在；
  - `[attr=value]` —— 要求属性存在且值相等；
  - `[attr!=value]` —— 要求属性存在且值**不**等于给定值（不存在该属性的元素同样不匹配）；
  - 元素拥有选择器未提及的额外属性时，不影响匹配结果。

```php
$el = $doc->first('div.box#main');
$el->matches('div.box', true);       // true（元素额外带 id 也匹配）
$el->matches('div.missing', true);   // false
$el->matches('div[title]', true);    // true（title 属性存在）
```

> 注意：严格模式只校验选择器**第一段**的标签/ID/类名/属性；若选择器包含组合器（如 `div.foo > span`），请使用非严格模式（`false`，默认），内部会临时序列化并用 `Document::has()` 整段匹配，但开销更大。

### 矩阵数据提取 queryMatrix

`queryMatrix()` 用于提取“看起来像表格但不是 `<table>` 标签”的矩阵型数据（如用 `div` 行/单元格布局的网格），按行列返回二维数组。

```php
$matrix = $doc->queryMatrix('.data-grid', [
    'rowSelector'  => '.data-row',   // 行选择器；null 表示使用直接子元素
    'cellSelector' => '.data-cell',  // 单元格选择器；null 表示使用直接子元素
    'selectorType' => 'css',         // 仅支持 css / xpath（'auto' 视为 css）
    'trimText'     => true,          // 是否修剪单元格文本空白
    'removeEmpty'  => true,          // 是否移除空行 / 空单元格
]);
// 返回：[['张三','男'], ['李四','女']]
```

> `queryMatrix()` 在 `Document` 与 `Element` 上均可调用；当不传容器选择器时，`Element::queryMatrix()` 以自身作为矩阵容器。

### 列表与定义列表提取

`extractList()` 提取 `<ul>/<ol>` 列表文本，自动识别直接子元素 `<li>` 并正确处理嵌套列表（嵌套的 `<li>` 不会误当作顶层项）；`extractDefinitionList()` 提取 `<dl>` 的术语/描述对。

```php
$list = $doc->extractList();                           // 默认提取页面第一个列表
$list = $doc->extractList('ul.items');                 // 指定列表
$list = $doc->extractList(null, ['recursive' => true]); // 嵌套列表作为子数组返回

$dl = $doc->extractDefinitionList();                   // ['姓名' => '张三', '年龄' => '30']
```

### JSON 辅助函数 parse_json

本扩展通过 Composer 的 `files` 自动加载提供全局函数 `parse_json(mixed $data): array|false`，用于在数据形态不确定时安全地把 JSON 字符串/数组/对象归一化为数组：

```php
parse_json('{"a":1}');        // ['a' => 1]
parse_json(['b' => 2]);       // ['b' => 2]（已是数组直接返回）
parse_json($someObject);      // 对象经 json_encode / json_decode 后返回数组
parse_json('');               // false（空串 / 无效 JSON）
```

`Document::json($content)` 内部即使用该函数；通过 `find(..., Query::TYPE_JSON)` 使用 JSON 选择器时，解析同样依赖它。

### 网页爬虫

```php
$html = file_get_contents('https://example.com');
$doc = new Document($html);

// 提取所有链接
$links = $doc->find('a[href]');
foreach ($links as $link) {
    echo $link->text() . ': ' . $link->attr('href') . "\n";
}

// 提取所有图片
$images = $doc->find('img[src]');
foreach ($images as $img) {
    echo $img->attr('alt') . ': ' . $img->attr('src') . "\n";
}
```

### XPath 高级用法

```php
// 使用 XPath 函数
$elements = $doc->xpath('//div[contains(@class, "item")]');
$elements = $doc->xpath('//a[starts-with(@href, "https")]');

// 使用位置函数
$firstElement = $doc->xpath('(//div)[1]');
$lastElement = $doc->xpath('(//div)[last()]');

// 复杂 XPath 查询
$elements = $doc->xpath('//div[@class="container" and count(.//p) > 2]');
```

### 选择器数组回退查找

选择器数组回退查找是一项强大的功能，允许您传入多个选择器，按顺序尝试，找到第一个非空结果即返回：

```php
// 应对不同网页结构
$titles = $doc->findWithFallback([
    ['selector' => '.main-content > h1.title'],      // 新版结构
    ['selector' => '#content > h1.article-title'],    // 旧版结构
    ['selector' => '//h1[contains(@class, "title")]', 'type' => 'xpath']
]);

if (!empty($titles)) {
    echo "标题: " . $titles[0]->text() . "\n";
}

// 使用 findFirstWithFallback 获取单个元素
$element = $doc->findFirstWithFallback([
    ['selector' => '.main-title'],
    ['selector' => 'h1.title'],
    ['selector' => '//h1[1]', 'type' => 'xpath']
]);

if ($element !== null) {
    echo $element->text();
}

// 混合使用 CSS、XPath 和正则表达式
$dates = $doc->findWithFallback([
    ['selector' => 'time.date'],
    ['selector' => '[data-date]'],
    ['selector' => '.date'],
    ['selector' => '/\d{4}-\d{2}-\d{2}/', 'type' => 'regex']
]);

// 提取表格数据（table 类型）
$tableData = $doc->findWithFallback([
    ['selector' => 'table.data-table', 'type' => 'table'],
    ['selector' => 'table.old-table', 'type' => 'table'],
]);
// 返回结构化表格数据：['thead' => [...], 'tbody' => [...]]

// 提取列表数据（list 类型）
$listData = $doc->findWithFallback([
    ['selector' => 'ul.product-list', 'type' => 'list'],
    ['selector' => 'ol.old-list', 'type' => 'list'],
]);

// 提取表单数据（form 类型）
$formData = $doc->findWithFallback([
    ['selector' => 'form#login', 'type' => 'form'],
]);

// 提取链接数据（link 类型）
$links = $doc->findWithFallback([
    ['selector' => 'a.external', 'type' => 'link'],
]);

// 提取图片数据（image 类型）
$images = $doc->findWithFallback([
    ['selector' => 'img.thumbnail', 'type' => 'image'],
]);

// 提取文本内容（text 类型）
$texts = $doc->findWithFallback([
    ['selector' => 'p.description', 'type' => 'text'],
]);
```

### 智能选择器类型检测

库提供了智能的选择器类型检测功能：

```php
use zxf\Dom\Selectors\Query;

// 自动检测选择器类型
$type = Query::detectSelectorType('div.container');           // 'css'
$type = Query::detectSelectorType('//div[@class="item"]');      // 'xpath'
$type = Query::detectSelectorType('/\d{4}-\d{2}-\d{2}/');      // 'regex'

// 检测 XPath 路径类型
$isAbsolute = Query::isXPathAbsolute('/html/body/div');         // true
$isRelative = Query::isXPathRelative('//div[@class="item"]');   // true
```

---

## 选择器数组回退查找

选择器数组回退查找是一项强大的功能，允许您传入多个选择器，按顺序尝试，找到第一个非空结果即返回。这为处理不同结构的网页提供了极大的灵活性。

### 支持的选择器类型

| 类型  | 说明      | 返回值类型           | 示例                                               |
|------|---------|------------------|--------------------------------------------------|
| css  | CSS 选择器 | `Element[]`      | `['selector' => 'div.item']`                       |
| xpath | XPath   | `Element[]`      | `['selector' => '//div[@class="item"]', 'type' => 'xpath']` |
| regex | 正则表达式  | `Element[]` 或 `string[]` | `['selector' => '/\d+/', 'type' => 'regex']`        |
| table | 表格数据   | `array[]`        | `['selector' => 'table.data', 'type' => 'table']`   |
| list  | 列表数据   | `array[]`        | `['selector' => 'ul.products', 'type' => 'list']`   |
| form  | 表单数据   | `array`          | `['selector' => 'form#login', 'type' => 'form']`    |
| link  | 链接数据   | `array[]`        | `['selector' => 'a.external', 'type' => 'link']`    |
| image | 图片数据   | `array[]`        | `['selector' => 'img.thumb', 'type' => 'image']`    |
| text  | 文本内容   | `string[]`       | `['selector' => 'p.desc', 'type' => 'text']`        |
| json  | JSON 数据 | `array`          | `['selector' => '', 'type' => 'json']`              |

### 参数说明

| 参数            | 类型     | 必需 | 说明                                                                     |
|---------------|--------|----|------------------------------------------------------------------------|
| selector      | string | 是  | 选择器表达式                                                                 |
| type          | string | 否  | 选择器类型，默认 'css'                                                         |
| attribute     | string | 否  | 仅 type='regex' 时使用，指定要匹配的属性名                                          |
| extractMode   | string | 否  | 仅 type='regex' 时使用，提取模式：'elements'、'text'、'attr'、'match'                |
| group         | int    | 否  | 仅 extractMode='match' 时使用，指定分组索引                                        |
| location      | array  | 否  | 仅 type='regex' 时使用，提取多个分组并返回关联数组                                        |
| extractOptions | array  | 否  | 提取选项，用于 table/list/text 类型                                             |

### extractOptions 选项说明

**table 类型选项：**
- `headerRow` (int): 表头行索引，默认 0
- `skipRows` (int): 跳过的行数，默认 0
- `includeHeader` (bool): 是否包含表头，默认 true
- `trimText` (bool): 是否修剪空白，默认 true
- `removeEmpty` (bool): 是否移除空行，默认 true
- `returnFormat` (string): 返回格式，'structured' / 'associative' / 'indexed'
- `cellSelector` (string): 单元格选择器，默认 'td, th'
- `rowSelector` (string): 行选择器，默认 'tr'
- `tableIndex` (int|null): 指定表格索引，null 表示返回所有

**list 类型选项：**
- `recursive` (bool): 是否递归提取嵌套列表，默认 false
- `trimText` (bool): 是否修剪空白，默认 true
- `includeIndex` (bool): 是否包含索引，默认 false

### 重要：返回数据结构

**findWithFallback** 方法有两种返回模式：

1. **默认模式 (`$getFirst = true`)**：返回第一个匹配的选择器结果（一维数组）
2. **查询模式 (`$getFirst = false`)**：返回所有选择器的结果（二维数组）

```php
// 默认模式 - 返回第一个非空结果（一维数组）
$result = $doc->findWithFallback($rules);
// 返回: [Element1, Element2, ...] 或 ['text1', 'text2', ...]

// 查询模式 - 返回所有选择器结果（二维数组）
$result = $doc->findWithFallback($rules, null, false);
// 返回: [[结果1], [结果2], [结果3], ...]
// 其中结果1、结果2、结果3 分别对应每个选择器的查询结果
```

### 基本用法

```php
// 应对不同网页结构
$titles = $doc->findWithFallback([
    ['selector' => '.main-content > h1.title'],      // 新版结构
    ['selector' => '#content > h1.article-title'],    // 旧版结构
    ['selector' => '//h1[contains(@class, "title")]', 'type' => 'xpath']
]);

if (!empty($titles)) {
    echo "标题: " . $titles[0]->text() . "\n";
}
```

### findFirstWithFallback

```php
// 使用 findFirstWithFallback 获取单个元素
$element = $doc->findFirstWithFallback([
    ['selector' => '.main-title'],
    ['selector' => 'h1.title'],
    ['selector' => '//h1[1]', 'type' => 'xpath']
]);

if ($element !== null) {
    echo $element->text();
}
```

### 混合使用多种选择器

```php
// 混合使用 CSS、XPath 和正则表达式
$dates = $doc->findWithFallback([
    ['selector' => 'time.date'],
    ['selector' => '[data-date]'],
    ['selector' => '.date'],
    ['selector' => '/\d{4}-\d{2}-\d{2}/', 'type' => 'regex']
]);
```

### 提取表格数据（table 类型）

```php
// 从不同结构的表格中提取数据
$html = '<div>
    <table class="data-table">
        <thead><tr><th>姓名</th><th>年龄</th></tr></thead>
        <tbody><tr><td>张三</td><td>30</td></tr></tbody>
    </table>
    <table class="old-table">
        <tr><td>姓名</td><td>年龄</td></tr>
        <tr><td>李四</td><td>25</td></tr>
    </table>
</div>';
$doc = new Document($html);

// 优先使用新表格结构，回退到旧表格结构
$results = $doc->findWithFallback([
    ['selector' => 'table.data-table', 'type' => 'table'],
    ['selector' => 'table.old-table', 'type' => 'table'],
]);

// 使用 extractOptions 自定义提取选项
$results = $doc->findWithFallback([
    [
        'selector' => 'table.data-table',
        'type' => 'table',
        'extractOptions' => [
            'returnFormat' => 'associative',
            'includeHeader' => true,
        ],
    ],
]);
```

### 提取列表数据（list 类型）

```php
// 提取列表数据
$items = $doc->findWithFallback([
    ['selector' => 'ul.product-list', 'type' => 'list'],
    ['selector' => 'ol.old-product-list', 'type' => 'list'],
]);

// 带选项提取嵌套列表
$items = $doc->findWithFallback([
    [
        'selector' => 'ul.category-menu',
        'type' => 'list',
        'extractOptions' => [
            'recursive' => true,
            'includeIndex' => true,
        ],
    ],
]);
```

### 提取表单、链接、图片数据

```php
// 表单数据提取
$formData = $doc->findWithFallback([
    ['selector' => 'form#login', 'type' => 'form'],
]);

// 链接数据提取
$links = $doc->findWithFallback([
    ['selector' => 'a.external-link', 'type' => 'link'],
]);

// 图片数据提取
$images = $doc->findWithFallback([
    ['selector' => 'img.thumbnail', 'type' => 'image'],
]);

// 文本内容提取
$texts = $doc->findWithFallback([
    ['selector' => 'p.description', 'type' => 'text'],
]);
```

### 混合使用不同的提取类型

```php
// 根据页面结构灵活选择提取方式
$result = $doc->findWithFallback([
    // 优先提取表格数据
    ['selector' => 'table.products', 'type' => 'table'],
    // 回退到提取列表数据
    ['selector' => 'ul.products', 'type' => 'list'],
    // 最后尝试提取链接
    ['selector' => 'a.product', 'type' => 'link'],
]);
```

```php
// 获取所有选择器的结果（即使有的返回空）
$allResults = $doc->findWithFallback([
    ['selector' => '.primary-title'],
    ['selector' => '.secondary-title'],
    ['selector' => '//h1[@class="title"]', 'type' => 'xpath']
], null, false);  // 注意第三个参数为 false

// 遍历结果
foreach ($allResults as $index => $result) {
    echo "选择器 $index 找到 " . count($result) . " 个结果\n";
    foreach ($result as $item) {
        echo "  - " . (is_object($item) ? $item->text() : $item) . "\n";
    }
}
```

---

## 智能选择器类型检测

库提供了智能的选择器类型检测功能，可以自动识别 CSS、XPath 和正则表达式。

### 检测选择器类型

```php
use zxf\Dom\Selectors\Query;

// 自动检测选择器类型
$type = Query::detectSelectorType('div.container');           // 'css'
$type = Query::detectSelectorType('//div[@class="item"]');      // 'xpath'
$type = Query::detectSelectorType('/\d{4}-\d{2}-\d{2}/');      // 'regex'
```

### 检测 XPath 路径类型

```php
// 检测 XPath 绝对路径
$isAbsolute = Query::isXPathAbsolute('/html/body/div');         // true
$isAbsolute = Query::isXPathAbsolute('//div');                 // false

// 检测 XPath 相对路径
$isRelative = Query::isXPathRelative('//div[@class="item"]');   // true
$isRelative = Query::isXPathRelative('/html/body');           // false
```

---
---

## 最佳实践

### 1. 使用更具体的选择器

```php
// ❌ 不好：选择器太宽泛
$elements = $doc->find('div p');

// ✅ 好：选择器更具体
$elements = $doc->find('div.container > p.content');
```

### 2. 缓存查询结果

```php
// ❌ 不好：重复查询
$doc->find('.item')[0]->addClass('active');
$doc->find('.item')[0]->text();
$doc->find('.item')[0]->attr('data-id');

// ✅ 好：缓存查询结果
$item = $doc->first('.item');
$item->addClass('active');
echo $item->text();
echo $item->attr('data-id');
```

### 3. 使用链式调用

```php
// ❌ 不好：多次调用
$doc->addClass('.container', 'active');
$doc->addClass('.container', 'highlight');
$doc->css('.container', 'color', 'red');

// ✅ 好：使用链式调用
$doc->addClass('.container', 'active')
    ->addClass('.container', 'highlight')
    ->css('.container', 'color', 'red');
```

### 4. 始终初始化 Query

```php
use zxf\Dom\Selectors\Query;

// 在应用启动时初始化一次
Query::initialize();
```

### 5. 正确处理编码

```php
// 始终指定 UTF-8 编码
$doc = new Document($html, false, 'UTF-8');

// 处理中文时使用 UTF-8
$chineseText = '中文内容';
$doc = new Document("<div>$chineseText</div>");
```

### 6. 使用异常处理

```php
try {
    $doc = new Document($htmlString);
    $elements = $doc->find('.selector');
} catch (\Exception $e) {
    error_log('错误: ' . $e->getMessage());
    // 处理错误
}
```

---

## 常见问题

### Q1: 如何处理包含特殊字符的 HTML？

**A:** 使用 Encoder 类进行编码：

```php
$html = Encoder::encodeHtml('<script>alert("XSS")</script>');
$doc = new Document("<div>$html</div>");
```

### Q2: 如何查找包含特定文本的元素？

**A:** 使用 `:contains` 伪类：

```php
$elements = $doc->find('div:contains(Hello)');
```

### Q3: 如何获取元素的纯文本，不包括子元素？

**A:** 使用 `:parent-only-text` 伪类：

```php
$elements = $doc->find('div:parent-only-text');
```

### Q4: 如何处理大型 HTML 文档？

**A:** 使用更具体的选择器，并缓存查询结果：

```php
// 使用具体的选择器减少查询范围
$container = $doc->first('div#main');
$items = $container->find('.item');
```

### Q5: 如何同时查询多个选择器？

**A:** 使用逗号分隔：

```php
$elements = $doc->find('div, p, span');
```

### Q6: 如何处理 XML 文档？

**A:** 指定文档类型为 XML：

```php
$doc = new Document($xmlString, false, 'UTF-8', Document::TYPE_XML);
```

### Q7: 如何获取元素的所有属性？

**A:** 使用 `attributes()` 方法：

```php
$attrs = $element->attributes();
foreach ($attrs as $name => $value) {
    echo "$name: $value\n";
}
```

### Q8: 如何判断元素是否可见？

**A:** 使用 `:visible` 伪类：

```php
$visible = $doc->find('div:visible');
$hidden = $doc->find('div:hidden');
```

### Q9: 如何同时添加多个类名？

**A:** 使用 `addClass` 方法的可变参数：

```php
$element->addClass('class1', 'class2', 'class3');
```

### Q10: 如何删除元素的所有内容？

**A:** 使用 `empty()` 方法：

```php
$element->empty();
```

---

## 总结

zxf/dom 是一个功能强大的 PHP DOM 操作库，提供了：

- 70+ 种 CSS 选择器
- 完整的 XPath 支持
- 60+ 种伪类
- 流畅的链式 API
- 完整的类型注解
- UTF-8 编码支持
- HTML/XML 双模式

通过遵循本指南的最佳实践，你可以高效地操作 HTML/XML 文档。

---

*文档版本: 2.0*  
*最后更新: 2026-05-14*

---

## 附录：完整 API 参数参考

### A. 选择器类型常量（`zxf\Dom\Selectors\Query::TYPE_*`）

| 常量 | 值 | 返回类型 | 说明 |
|------|-----|---------|------|
| `TYPE_CSS` | `'css'` | `Element[]` | CSS 选择器（默认） |
| `TYPE_XPATH` | `'xpath'` | `Element[]` | XPath 表达式 |
| `TYPE_REGEX` | `'regex'` | `Element[]` 或 `string[]` | 正则表达式 |
| `TYPE_TABLE` | `'table'` | `array[]` | 表格数据提取 |
| `TYPE_LIST` | `'list'` | `array[]` | 列表数据提取 |
| `TYPE_FORM` | `'form'` | `array` | 表单数据提取 |
| `TYPE_LINK` | `'link'` | `array[]` | 链接数据提取 |
| `TYPE_IMAGE` | `'image'` | `array[]` | 图片数据提取 |
| `TYPE_TEXT` | `'text'` | `string[]` | 文本内容提取 |
| `TYPE_JSON` | `'json'` | `array` | JSON 数据解析 |

### B. `extractTable()` 选项完整枚举

| 参数 | 类型 | 默认值 | 所有可选值 | 说明 |
|------|------|--------|-----------|------|
| `selectorType` | `string` | `'auto'` | `'auto'` `'css'` `'xpath'` `'regex'` | 选择器类型自动检测或手动指定 |
| `headerRow` | `int` | `0` | `0,1,2,...` | 表头行索引（0-based） |
| `skipRows` | `int` | `0` | `0,1,2,...` | 跳过表格开头的行数 |
| `includeHeader` | `bool` | `true` | `true` `false` | 是否提取表头数据 |
| `includeHeaderAsFirstRow` | `bool` | `false` | `true` `false` | 是否将表头作为第一行 |
| `trimText` | `bool` | `true` | `true` `false` | 是否修剪单元格空白 |
| `removeEmpty` | `bool` | `true` | `true` `false` | 是否移除空行 |
| `cellSelector` | `string` | `'td, th'` | 任意CSS选择器 | 单元格选择器 |
| `rowSelector` | `string` | `'tr'` | 任意CSS选择器 | 行选择器 |
| `returnFormat` | `string` | `'structured'` | `'structured'` `'associative'` `'indexed'` `'both'` | 返回格式 |
| `preserveStructure` | `bool` | `true` | `true` `false` | 是否保留 thead/tbody/tfoot |
| `returnAllTables` | `bool` | `true` | `true` `false` | 是否返回所有匹配表格 |
| `tableIndex` | `int|null` | `null` | `null,0,1,2,...` | 指定返回第几个表格 |

**`returnFormat` 返回格式**：
- `'structured'`: `[['thead'=>[...], 'tbody'=>[[...],...]]]`
- `'associative'`: `[['姓名'=>'张三','年龄'=>'30'],...]`
- `'indexed'`: `[['张三','30'],...]`
- `'both'`: `['headers'=>[...], 'rows'=>[[...],...]]`

### C. `extractList()` 选项完整枚举

| 参数 | 类型 | 默认值 | 可选值 | 说明 |
|------|------|--------|-------|------|
| `recursive` | `bool` | `false` | `true` `false` | 递归提取嵌套列表 |
| `trimText` | `bool` | `true` | `true` `false` | 修剪文本空白 |
| `includeIndex` | `bool` | `false` | `true` `false` | 包含序号索引 |

### D. `queryMatrix()` 选项完整枚举

| 参数 | 类型 | 默认值 | 可选值 | 说明 |
|------|------|--------|-------|------|
| `rowSelector` | `string|null` | `null` | null 或任意CSS选择器 | 行选择器，null=直接子元素 |
| `cellSelector` | `string|null` | `null` | null 或任意CSS选择器 | 单元格选择器，null=直接子元素 |
| `trimText` | `bool` | `true` | `true` `false` | 修剪文本空白 |
| `removeEmpty` | `bool` | `true` | `true` `false` | 移除空行/空单元格 |
| `selectorType` | `string` | `'auto'` | `'auto'` `'css'` `'xpath'` `'regex'` | 选择器类型 |

### E. `findWithFallback()` 选择器配置完整枚举

| 参数 | 类型 | 必需 | 适用type | 说明 |
|------|------|------|---------|------|
| `selector` | `string` | 是 | 全部 | 选择器表达式 |
| `type` | `string` | 否 | 全部 | 参见常量表，默认 'css' |
| `attribute` | `string|null` | 否 | `regex` | 要匹配的属性名 |
| `extractMode` | `string|null` | 否 | `regex` | 提取模式：`'elements'` `'text'` `'attr'` `'match'` |
| `group` | `int|null` | 否 | `regex`+`match` | 正则分组索引 |
| `location` | `array|null` | 否 | `regex` | 多分组提取配置 |
| `extractOptions` | `array|null` | 否 | `table` `list` `text` | 提取选项，参见 B/C 表 |

### F. Document 类便捷查询方法一览

| 方法 | 功能 | 参数 | 返回值 |
|------|------|------|--------|
| `find()` | 查找匹配元素 | selector, type, contextNode | `Element[]|string[]|array[]` |
| `first()` | 查找第一个匹配 | selector, type, contextNode | `Element|string|array|null` |
| `has()` | 检查是否存在 | selector | `bool` |
| `count()` | 统计匹配数量 | selector | `int` |
| `xpath()` | XPath 查询 | xpathExpression | `Element[]` |
| `xpathFirst()` | XPath 首个 | xpathExpression | `?Element` |
| `xpathTexts()` | XPath 文本 | xpathExpression | `string[]` |
| `xpathAttrs()` | XPath 属性值 | xpathExpression, attribute | `string[]` |
| `regex()` | 正则查找 | pattern, contextNode, attribute | `Element[]` |
| `regexMatch()` | 正则匹配文本 | pattern, contextNode, attribute | `string[]|array[]` |
| `regexMulti()` | 多正则匹配 | patterns, contextNode, attribute | `array` |
| `regexReplace()` | 正则替换 | pattern, replacement, contextNode, attribute | `self` |
| `findByText()` | 按文本查找 | text, selector | `Element[]` |
| `findByAttribute()` | 按属性查找 | attribute, value, selector | `Element[]` |
| `findByData()` | 按 data 属性查找 | dataName, value, selector | `Element[]` |
| `findByHtml()` | 按 HTML 查找 | html, selector | `Element[]` |
| `findByPath()` | 按路径查找 | path, relative | `Element[]` |
| `findByIndex()` | 按索引查找 | selector, index | `?Element` |
| `findLast()` | 查找最后一个 | selector | `?Element` |
| `findByRange()` | 范围查找 | selector, start, end | `Element[]` |
| `text()` | 获取文本 | selector | `string|?Element` |
| `html()` | 获取 HTML | selector | `string` |
| `links()` | 获取所有链接 | selector | `array[]` |
| `images()` | 获取所有图片 | selector | `array[]` |
| `forms()` | 获取所有表单 | selector | `array[]` |
| `inputs()` | 获取所有输入元素 | selector | `array[]` |
| `extractTable()` | 提取表格 | table, options | `array[]` |
| `extractList()` | 提取列表 | list, options | `array[]` |
| `extractFormData()` | 提取表单 | form | `array` |
| `extractLinks()` | 提取链接 | selector | `array[]` |
| `extractImages()` | 提取图片 | selector | `array[]` |
| `extractMetaData()` | 提取 meta | ...names | `array` |
| `queryMatrix()` | 矩阵数据提取 | container, options | `array[]` |
| `createElement()` | 创建元素 | tagName, value, attributes | `Element` |
| `toHtml()` | 获取文档 HTML | — | `string` |
| `save()` | 保存到文件 | filename | `self` |

### G. Element 类便捷方法一览

| 方法 | 功能 | 参数 | 返回值 |
|------|------|------|--------|
| `find()` | 查找后代 | selector, type | `Element[]` |
| `first()` | 查找首个后代 | selector, type | `?Element` |
| `matches()` | 是否匹配选择器 | selector | `bool` |
| `children()` | 获取子元素 | — | `Element[]` |
| `parent()` | 获取父元素 | — | `?Element` |
| `siblings()` | 获取兄弟元素 | — | `Element[]` |
| `firstChild()` | 首个子元素 | — | `?Element` |
| `lastChild()` | 最后子元素 | — | `?Element` |
| `tagName()` | 标签名 | — | `string` |
| `text()` | 文本内容 | — | `string` |
| `html()` | HTML 内容 | — | `string` |
| `attr()` | 获取/设置属性 | name, value | `?string|self` |
| `classes()` | 类管理对象 | — | `ClassAttribute` |
| `style()` | 样式管理对象 | — | `StyleAttribute` |
| `css()` | 获取/设置样式 | name, value | `?string|self` |
| `setAttribute()` | 设置属性 | name, value | `self` |
| `getAttribute()` | 获取属性 | name, default | `?string` |
| `hasAttribute()` | 检查属性 | name | `bool` |
| `removeAttribute()` | 移除属性 | name | `self` |
| `addClass()` | 添加类 | ...classNames | `self` |
| `removeClass()` | 移除类 | ...classNames | `self` |
| `toggleClass()` | 切换类 | className | `self` |
| `hasClass()` | 检查类 | className | `bool` |
| `setHtml()` | 设置 HTML | html | `self` |
| `setText()` | 设置文本 | text | `self` |
| `setValue()` | 设置值 | value | `self` |
| `append()` | 追加子节点 | nodes | `Node|Node[]` |
| `prepend()` | 开头插入子节点 | nodes | `Node|Node[]` |
| `before()` | 之前插入 | nodes | `Node|Node[]` |
| `after()` | 之后插入 | nodes | `Node|Node[]` |
| `replaceWith()` | 替换 | nodes | `self` |
| `remove()` | 移除 | — | `self` |
| `clone()` | 克隆 | deep | `Node` |
| `index()` | 兄弟索引 | — | `int` |
| `getPath()` | 节点路径 | separator | `string` |
| `extractTable()` | 提取表格 | selector, options | `array` |
| `extractTableData()` | 自身为表格提取 | options | `array` |
| `extractList()` | 提取列表 | selector, options | `array` |
| `extractFormData()` | 提取表单 | selector | `array` |
| `extractLinks()` | 提取链接 | selector | `array` |
| `extractImages()` | 提取图片 | selector | `array` |
| `extractTexts()` | 提取文本 | selector, trim | `string[]` |
| `extractTableHeaders()` | 提取表头 | options | `string[]` |
| `extractTableRows()` | 提取行 | options | `array[]` |
| `extractTableColumn()` | 提取列 | column, options | `string[]` |
| `extractTableAsAssociative()` | 关联数组格式 | options | `array[]` |
| `extractNestedTables()` | 嵌套表格 | selector, options | `array[]` |
| `queryMatrix()` | 矩阵数据 | containerSelector, options | `array[]` |
| `regex()` | 正则查找 | pattern, attribute | `Element[]` |
| `regexMatch()` | 正则匹配 | pattern, attribute | `array` |
| `regexReplace()` | 正则替换 | pattern, replacement, attribute | `self` |
| `findByText()` | 文本查找 | text, selector | `Element[]` |
| `findByAttribute()` | 属性查找 | attribute, value, selector | `Element[]` |

### H. Query 类静态方法一览

| 方法 | 功能 | 参数 | 返回值 |
|------|------|------|--------|
| `compile()` | 编译选择器 | expression, type | `string`（XPath） |
| `detectSelectorType()` | 检测选择器类型 | selector | `'css'` `'xpath'` `'regex'` |
| `isXPathAbsolute()` | 是否 XPath 绝对路径 | expression | `bool` |
| `isXPathRelative()` | 是否 XPath 相对路径 | expression | `bool` |
| `cssToXpath()` | CSS 转 XPath | selector | `string` |
| `parseSelector()` | 解析选择器 | selector | `array` |
| `initialize()` | 初始化 | — | `void` |
| `getCacheStats()` | 缓存统计 | — | `array` |
| `clearCompiled()` | 清空缓存 | — | `void` |
| `reset()` | 重置 | — | `void` |
