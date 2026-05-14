# zxf/dom - 强大的 PHP DOM 操作库

一个功能强大、易于使用的 PHP DOM 操作库，提供简洁的 API 来解析、查询和操作 HTML/XML 文档。

[🇺🇸 Read English Documentation (README_EN.md)](README_EN.md)

## 特性

- ✅ **完整的 CSS3 选择器支持** - 支持 150+ 种 CSS 选择器类型
- ✅ **原生 XPath 支持** - 可直接使用 XPath 表达式查询
- ✅ **丰富的伪类** - 支持 100+ 种伪类选择器
- ✅ **伪元素支持** - 支持 `::text` 和 `::attr()` 伪元素
- ✅ **扩展选择器功能** - 文本长度匹配、属性长度/数量选择器、基于深度的选择器
- ✅ **正则表达式支持** - 强大的正则表达式匹配和数据提取功能
- ✅ **表格数据提取** - 重构后的表格处理，严格分离表头表体，避免数据混杂
- ✅ **列表数据提取** - 支持嵌套列表递归提取数据
- ✅ **矩阵数据提取** - 重构后的矩阵数据处理，给出每行每列数据 queryMatrix
- ✅ **表单数据提取** - 提取表单字段数据，返回字段名与值的关联数组
- ✅ **链接/图片数据提取** - 提取链接和图片的结构化数据
- ✅ **智能选择器类型** - findWithFallback 支持 css/xpath/regex/table/list/form/link/image/text/json 10种选择器类型
- ✅ **JSON 数据处理** - 支持 JSON 字符串/数组/对象的解析和提取
- ✅ **链式调用** - 流畅的 API 设计，支持链式操作
- ✅ **PHP 8.2+ 类型系统** - 完整的类型注解，更好的 IDE 支持
- ✅ **HTML/XML 双模式** - 同时支持 HTML 和 XML 文档处理
- ✅ **高性能** - 选择器编译缓存，提升查询速度
- ✅ **UTF-8 编码支持** - 完善的中文等多字节字符支持
- ✅ **表单元素操作** - 专门的表单选择器和操作方法
- ✅ **错误处理机制** - 统一的异常处理和错误报告
- ✅ **完整的测试覆盖** - 230+ 个测试用例，确保代码质量

## 系统要求

- PHP >= 8.2（支持 8.2、8.3、8.4）
- libxml 扩展
- cURL 扩展（用于从远程 URL 加载文档）

## 安装

### 使用 Composer 安装

```bash
composer require zxf/dom
```

### 手动安装

```php
require_once 'path/to/Query.php';
require_once 'path/to/Document.php';
// ... 其他文件

use zxf\Dom\Selectors\Query;
use zxf\Dom\Document;

Query::initialize();
```

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

### 从远程 URL 加载文档

```php
use zxf\Dom\Document;

// 从 HTTP/HTTPS URL 加载网页
$doc = new Document('https://example.com', true);

// 查找页面元素
$title = $doc->title();
echo "页面标题: {$title}\n";

// 获取所有链接
$links = $doc->links();
foreach ($links as $link) {
    echo "链接: {$link['text']} -> {$link['href']}\n";
}

// 提取特定内容
$articles = $doc->find('article');
foreach ($articles as $article) {
    $title = $article->first('h2')->text();
    $content = $article->first('p')->text();
    echo "文章: {$title}\n内容: {$content}\n";
}

// 加载远程 XML
$xmlDoc = new Document('https://example.com/data.xml', true, 'UTF-8', Document::TYPE_XML);
$items = $xmlDoc->find('item');
```

**注意：** 从远程 URL 加载需要启用 cURL 扩展。

### XML 文档处理

```php
$xml = '<root><item id="1">Item 1</item><item id="2">Item 2</item></root>';
$doc = new Document($xml, false, 'UTF-8', Document::TYPE_XML);

$items = $doc->find('item');
foreach ($items as $item) {
    echo $item->attr('id') . ': ' . $item->text() . "\n";
}
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

## 支持的选择器

### CSS 选择器（70+ 种）

**基础选择器：**
- `*` - 通配符选择器
- `tag` - 标签选择器
- `.class` - 类选择器
- `#id` - ID 选择器
- `s1, s2` - 多选择器
- `s1 s2` - 后代选择器
- `s1 > s2` - 子选择器
- `s1 + s2` - 相邻兄弟选择器
- `s1 ~ s2` - 通用兄弟选择器

**属性选择器：**
- `[attr]` - 包含属性
- `[attr=value]` - 属性等于
- `[attr~=value]` - 属性包含单词
- `[attr|=value]` - 属性等于或以...开头
- `[attr^=value]` - 属性以...开头
- `[attr$=value]` - 属性以...结尾
- `[attr*=value]` - 属性包含

**伪类（60+ 种）：**
- 结构伪类：`:first-child`, `:last-child`, `:nth-child(n)` 等
- 内容伪类：`:contains(text)`, `:has(selector)`, `:empty` 等
- 表单伪类：`:enabled`, `:disabled`, `:checked`, `:required` 等
- 表单元素伪类：`:text`, `:password`, `:checkbox`, `:radio` 等
- HTML 元素伪类：`:header`, `:input`, `:button`, `:link` 等
- 位置伪类：`:first`, `:last`, `:even`, `:odd`, `:eq(n)` 等
- 可见性伪类：`:visible`, `:hidden`

**伪元素：**
- `::text` - 获取元素文本内容
- `::attr(name)` - 获取元素属性值

### XPath 选择器

- 完整的 XPath 1.0 支持
- 所有 XPath 函数：`contains()`, `starts-with()`, `position()`, `last()` 等
- 所有 XPath 轴和运算符

```php
// XPath 示例
$elements = $doc->xpath('//div[@class="container"]');
$elements = $doc->xpath('//a[contains(@href, "example.com")]');
$elements = $doc->xpath('(//div[@class="item"])[1]');
```

## API 参考

### Document

代表 HTML/XML 文档的主文档类。

```php
use zxf\Dom\Document;

// 创建文档
$doc = new Document($htmlString);
$doc = new Document($htmlString, false, 'UTF-8', Document::TYPE_XML);

// 加载内容
$doc->load($string);
$doc->load($file, true); // 支持本地文件和远程 URL

// 保存文档
$doc->save($filename);

// 查找元素
$elements = $doc->find('div');
$element = $doc->first('div');

// 获取内容
$html = $doc->html();
$text = $doc->text();
$title = $doc->title();

// 元素操作
$doc->addClass('.selector', 'class-name');
$doc->removeClass('.selector', 'class-name');
$doc->hasClass('.selector', 'class-name');
$doc->css('.selector', 'property', 'value');
$doc->attr('.selector', 'attribute', 'value');
$doc->removeAttr('.selector', 'attribute');

// 正则表达式功能
$elements = $doc->regex('/\d{4}-\d{2}-\d{2}/');  // 查找匹配的元素
$matches = $doc->regexMatch('/(\w+)\s*[:：]\s*(\d+)/');  // 提取匹配数据
$data = $doc->regexMulti(['dates' => '/.../', 'emails' => '/.../']);  // 多列数据提取
$doc->regexReplace('/\s+/', ' ');  // 正则替换

// 数据提取功能
$tableData = $doc->extractTable();  // 提取表格数据
$listData = $doc->extractList('ul');  // 提取列表数据
$formData = $doc->extractFormData('form');  // 提取表单数据
$links = $doc->extractLinks();  // 提取链接数据
$images = $doc->extractImages();  // 提取图片数据

// 表格数据提取详细示例（重构后）
// CSS选择器提取
$tableData = $doc->extractTable('table.data-table');
// 返回格式：['thead' => ['姓名', '年龄'], 'tbody' => [['张三', '25'], ...]]

// XPath选择器提取
$tableData = $doc->extractTable('//table[@id="myTable"]');

// 通过类名提取
$tableData = $doc->extractTableByClass('data-table');

// 通过ID提取
$tableData = $doc->extractTableById('myTable');

// 通过属性提取
$tableData = $doc->extractTableByAttribute('data-type', 'user-list');

// 批量提取所有表格
$allTables = $doc->extractAllTables();

// Element类表格方法
$tableElement = $doc->first('table');
$headers = $tableElement->extractTableHeaders();  // ['姓名', '年龄']
$rows = $tableElement->extractTableRows();       // [['张三', '25'], ...]
$column = $tableElement->extractTableColumn(0);  // ['张三', '李四', ...]
$column = $tableElement->extractTableColumn('姓名');  // ['张三', '李四', ...]

// 正则表达式提取
$tableData = $doc->extractTable('/<table[^>]*class="data"[^>]*>/is');

// 自定义选项提取
$tableData = $doc->extractTable('table', [
    'headerRow' => 0,              // 表头行索引
    'skipRows' => 1,                // 跳过1行
    'includeHeader' => true,         // 包含表头
    'returnFormat' => 'indexed'      // 返回索引格式
]);

// 提取所有表格
$allTables = $doc->extractTable(null);

// Element对象提取
$tableElement = $doc->first('table');
$tableData = $doc->extractTable($tableElement);

// findWithFallback 回退查找（支持 10 种选择器类型）
$titles = $doc->findWithFallback([
    ['selector' => 'h1.title'],
    ['selector' => '//h1[@class="title"]', 'type' => 'xpath'],
    ['selector' => '/<h1[^>]*class="title"[^>]*>/i', 'type' => 'regex', 'extractMode' => 'text'],
]);

// 提取表格数据（table 类型）
$tableData = $doc->findWithFallback([
    ['selector' => 'table.data-table', 'type' => 'table'],
    ['selector' => 'table.old-table', 'type' => 'table'],
]);

// 提取列表数据（list 类型）
$listData = $doc->findWithFallback([
    ['selector' => 'ul.product-list', 'type' => 'list'],
    ['selector' => 'ol.old-product-list', 'type' => 'list'],
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

// JSON 数据提取（json 类型）
$jsonData = $doc->findWithFallback([
    ['selector' => '', 'type' => 'json'],
]);

// XPath 查询
$elements = $doc->xpath('//div[@class="item"]');
```

#### 从远程 URL 加载文档

```php
// 从 HTTP/HTTPS URL 加载
$doc = new Document();
$doc->load('https://example.com', true); // 自动识别并使用 HTTP 请求

// 或者在构造时指定
$doc = new Document('https://example.com', true);

// 加载远程 XML
$doc = new Document('https://example.com/data.xml', true, 'UTF-8', Document::TYPE_XML);
```

**注意：** 从远程 URL 加载需要启用 cURL 扩展。

### Element

代表文档中的一个元素。

```php
$element = $doc->first('div');

// 内容操作
$text = $element->text();
$html = $element->html();
$element->setValue('new text');
$element->setHtml('<p>new html</p>');

// 属性操作
$value = $element->attr('name');
$element->attr('name', 'value');
$allAttrs = $element->attributes();
$element->removeAttr('name');

// 类名操作
$element->addClass('class1', 'class2');
$element->removeClass('class1');
$element->hasClass('class1');
$classes = $element->classes()->all();

// 样式操作
$element->css('color', 'red');
$color = $element->css('color');
$styles = $element->style()->all();

// 节点操作
$parent = $element->parent();
$children = $element->children();
$firstChild = $element->firstChild();
$lastChild = $element->lastChild();
$siblings = $element->siblings();
$index = $element->index();

// 元素操作
$element->append($newElement);
$element->prepend($newElement);
$element->before($newElement);
$element->after($newElement);
$element->remove();
$element->empty();
$cloned = $element->clone();
```

### ClassAttribute

管理元素的类属性。

```php
$classes = $element->classes();

// 添加类名
$classes->add('class1', 'class2');

// 移除类名
$classes->remove('class1');

// 检查类名
$has = $classes->has('class1');

// 获取所有类名
$all = $classes->all();

// 清空所有类名
$classes->clear();

// 切换类名
$classes->toggle('active');
```

### StyleAttribute

管理元素的样式属性。

```php
$style = $element->style();

// 设置样式
$style->set('color', 'red');
$style->set(['color' => 'red', 'background' => 'blue']);

// 获取样式
$color = $style->get('color');
$all = $style->all();

// 移除样式
$style->remove('color');

// 驼峰命名支持
$style->set('backgroundColor', 'red');
```

### Encoder

编码/解码工具类。

```php
use zxf\Dom\Utils\Encoder;

// HTML 编码
$html = Encoder::encodeHtml('<script>alert("XSS")</script>');

// HTML 解码
$html = Encoder::decodeHtml('&lt;script&gt;');

// URL 编码
$url = Encoder::encodeUrl('中文内容');

// URL 解码
$url = Encoder::decodeUrl('%E4%B8%AD%E6%96%87');
```

### Errors

错误处理工具。

```php
use zxf\Dom\Utils\Errors;

// 静默处理错误
Errors::silence();

// 启用日志
Errors::setLoggingEnabled(true);
Errors::setLogFile('/path/to/log.txt');

// 自定义错误处理器
Errors::setErrorHandler(function($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile:$errline");
});
```

## 示例

### 示例 1：查找元素

```php
$doc = new Document('<div class="item">1</div><div class="item">2</div>');
$items = $doc->find('.item');
foreach ($items as $item) {
    echo $item->text() . "\n";
}
```

### 示例 2：修改元素

```php
$doc = new Document('<div class="container">Text</div>');
$doc->addClass('.container', 'active');
$doc->css('.container', 'color', 'red');
echo $doc->html();
```

### 示例 3：网页爬虫

```php
$html = file_get_contents('https://example.com');
$doc = new Document($html);

$links = $doc->find('a[href]');
foreach ($links as $link) {
    echo $link->text() . ': ' . $link->attr('href') . "\n";
}
```

### 示例 4：表格数据提取

```php
$html = '<table>
    <tr><td>ID</td><td>名称</td></tr>
    <tr><td>1</td><td>产品 A</td></tr>
    <tr><td>2</td><td>产品 B</td></tr>
</table>';

$doc = new Document($html);
$rows = $doc->find('tr:not(:first-child)');

foreach ($rows as $row) {
    $cells = $row->find('td');
    echo $cells[0]->text() . ': ' . $cells[1]->text() . "\n";
}
```

## 性能优化建议

1. **使用更具体的选择器** - 更具体的选择器速度更快
   ```php
   // ✅ 推荐
   $doc->find('div.container > p.highlight');
   // ❌ 避免
   $doc->find('div p');
   ```

2. **缓存查询结果** - 存储频繁使用的元素
   ```php
   // ✅ 推荐
   $container = $doc->first('.container');
   $item = $container->first('.item');
   // ❌ 避免
   $doc->first('.container .item');
   ```

3. **使用 ID 选择器** - ID 选择器是最快的
   ```php
   // ✅ 推荐
   $doc->find('#main-content');
   // ❌ 避免
   $doc->find('div[id="main-content"]');
   ```

## 测试

运行测试套件：

```bash
php tests.php
```

运行示例：

```bash
php examples.php
```

## 文档

- **[USER_GUIDE.md](USER_GUIDE.md)** - 完整的用户指南和示例
- **[RULE_GUIDE.md](RULE_GUIDE.md)** - 全面选择器参考手册
- **[REGEX_ENHANCED.md](docs/REGEX_ENHANCED.md)** - 正则表达式增强功能详解
- **[TABLE_EXTRACTION.md](docs/TABLE_EXTRACTION.md)** - 表格数据提取完整指南

## 贡献

欢迎贡献！请随时提交 Pull Request。

## 许可证

MIT License

## 支持

如有问题和疑问，请使用 GitHub 问题跟踪器。

---

*版本: 2.0.0*  
*最后更新: 2026-05-14*

---

## 附录：快速参数参考

### selectors 数组参数格式（findWithFallback）

```php
[
    'selector'       => 'string',     // CSS/XPath/正则 选择器表达式
    'type'           => 'string',     // css|xpath|regex|table|list|form|link|image|text|json
    'attribute'      => 'string|null', // 仅 type=regex，匹配的属性名
    'extractMode'    => 'string|null', // 仅 type=regex: elements|text|attr|match
    'group'          => 'int|null',    // 仅 extractMode=match，分组索引
    'location'       => 'array|null',  // 仅 type=regex，多分组提取配置
    'extractOptions' => 'array|null',  // 仅 type=table|list|text，提取选项
]
```

### extractTable 选项

```php
$options = [
    'selectorType'          => 'auto',       // auto|css|xpath|regex
    'headerRow'             => 0,            // 表头行索引
    'skipRows'              => 0,            // 跳过行数
    'includeHeader'         => true,         // 包含表头
    'includeHeaderAsFirstRow' => false,      // 表头作为首行
    'trimText'              => true,         // 修剪空白
    'removeEmpty'           => true,         // 移除空行
    'cellSelector'          => 'td, th',     // 单元格选择器
    'rowSelector'           => 'tr',         // 行选择器
    'returnFormat'          => 'structured', // structured|associative|indexed|both
    'preserveStructure'     => true,         // 保留 thead/tbody/tfoot
    'returnAllTables'       => true,         // 返回所有表格
    'tableIndex'            => null,         // 指定表格索引
];
```

### extractList 选项

```php
$options = [
    'recursive'    => false,  // 递归提取嵌套列表
    'trimText'     => true,   // 修剪空白
    'includeIndex' => false,  // 包含索引
];
```

### queryMatrix 选项

```php
$options = [
    'rowSelector'  => null,    // 行选择器，null=直接子元素
    'cellSelector' => null,    // 单元格选择器，null=直接子元素
    'trimText'     => true,    // 修剪空白
    'removeEmpty'  => true,    // 移除空行
    'selectorType' => 'auto',  // auto|css|xpath|regex
];
```

### Document 类常用方法速查

| 方法 | 功能 | 参数 |
|------|------|------|
| `find()` | 查找元素 | `(expression, type='css', contextNode=null)` |
| `first()` | 查找首个 | `(expression, type='css', contextNode=null)` |
| `xpath()` | XPath 查询 | `(xpathExpression)` |
| `regex()` | 正则查找 | `(pattern, contextNode=null, attribute=null)` |
| `findByText()` | 文本查找 | `(text, selector='*')` |
| `findByAttribute()` | 属性查找 | `(attribute, value=null, selector='*')` |
| `findByData()` | data属性查找 | `(dataName, value=null, selector='*')` |
| `findByPath()` | 路径查找 | `(path, relative=false)` |
| `extractTable()` | 提取表格 | `(table=null, options=[])` |
| `extractList()` | 提取列表 | `(list=null, options=[])` |
| `extractFormData()` | 提取表单 | `(form=null)` |
| `findWithFallback()` | 回退查找 | `(selectors, contextNode=null, getFirst=true)` |
| `queryMatrix()` | 矩阵数据 | `(container='div', options=[])` |

详细完整参数说明请参考 `src/docs/RULE_GUIDE.md`（选择器规则说明文档）和 `src/docs/USER_GUIDE.md`（完整使用指南）。
