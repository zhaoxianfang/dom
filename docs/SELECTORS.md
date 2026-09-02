# 选择器参考 (Selectors Reference)

`zxf/dom` 提供三种互补的查找方式，覆盖从简单到复杂的现代网页解析需求：

| 类型 | 常量 | 说明 |
| --- | --- | --- |
| CSS 选择器 | `Query::TYPE_CSS` | 现代浏览器级 CSS 选择器（含 CSS3/CSS4 特性） |
| XPath | `Query::TYPE_XPATH` | 原生 XPath 1.0 路径，最灵活 |
| 正则表达式 | `Query::TYPE_REGEX` | 在元素文本/属性/HTML 上做正则匹配与捕获组提取 |

```php
use zxf\Dom\Document;
use zxf\Dom\Selectors\Query;

$doc = Document::create($html);

// CSS（默认）
$doc->find('.item');
$doc->find('div:has(> img)');

// XPath
$doc->find('//div[@class="item"]', Query::TYPE_XPATH);

// 正则
$doc->findByRegex('/(\d+)/', Query::REGEX_TEXT);
```

---

## 一、CSS 选择器

> 引擎把 CSS 选择器编译为 XPath 1.0 执行（属性大小写不敏感通过 `translate()` 实现）。
> 所有选择器均支持组合嵌套与上下文节点（`find($sel, $type, $contextNode)`）。

### 1. 基础选择器

| 选择器 | 示例 | 说明 |
| --- | --- | --- |
| 类型选择器 | `div` `p` `a` | 按标签名匹配 |
| 通用选择器 | `*` | 匹配任意元素 |
| 类选择器 | `.item` | 匹配含 `item` 类的元素 |
| ID 选择器 | `#main` | 匹配 `id="main"` 的元素 |
| 多类 | `.a.b` | 同时含 `a` 和 `b` 类 |
| 属性存在 | `[data-id]` | 存在该属性 |
| 属性精确 | `[data-id="2"]` | 值精确相等 |
| 属性不等 | `[data-id!="2"]` | 值不等于 |
| 属性包含词 | `[class~="item"]` | 属性值为空格分隔的词列表且含 `item` |
| 属性前缀 | `[class^="it"]` | 值以 `it` 开头 |
| 属性后缀 | `[class$="em"]` | 值以 `em` 结尾 |
| 属性包含 | `[class*="te"]` | 值包含 `te` |
| 属性语言/前缀 | `[lang|="en"]` | 值等于 `en` 或以 `en-` 开头 |

**命名空间选择器**（CSS / XML）

| 选择器 | 示例 | 说明 |
| --- | --- | --- |
| 前缀限定 | `svg\|circle` | 匹配 `svg` 命名空间（按 local-name）下的 `circle` |
| 任意命名空间 | `*\|circle` | 任意命名空间下 local-name 为 `circle` 的元素 |
| 命名空间通配 | `svg\|*` | 命名空间通配（降级为匹配所有元素） |

```php
$doc->find('svg|circle');   // SVG 圆形
$doc->find('*|circle');      // 任意命名空间的 circle
```

> XPath 1.0 要求注册命名空间前缀才能用 `prefix:tag` 语法，而 libxml 默认未注册 `svg`/`math` 等前缀。
> 本库将 `ns|tag` 统一编译为 `*[local-name()='tag']`，不依赖前缀注册即可在 HTML5 解析的 SVG/MathML 上正确匹配。

**大小写不敏感**（CSS4）：在属性选择器末尾加 `i` 修饰符。

```php
$doc->find('a[href="HOME" i]');        // 匹配 href="Home" / "HOME" / "home"
$doc->find('[class="ITEM" i]');        // 匹配 class="item" / "ITEM"
$doc->find('input[name="EMAIL" I]');   // I / i 皆可
```

> 仅对属性值生效；`s` 修饰符显式声明区分大小写（默认行为），亦被接受。

### 2. 组合器 (Combinators)

| 组合器 | 示例 | 说明 |
| --- | --- | --- |
| 后代 | `div p` | `div` 内任意层级的 `p` |
| 直接子代 | `ul > li` | `ul` 的直接子 `li` |
| 相邻兄弟 | `h1 + p` | 紧跟 `h1` 之后的 `p` |
| 后续兄弟 | `h1 ~ p` | `h1` 之后的所有同级 `p` |

```php
$doc->find('table > tbody > tr');   // 严格层级
$doc->find('.card + .card');        // 相邻
$doc->find('dt ~ dd');              // 通用兄弟
```

### 3. 伪类 (Pseudo-classes)

#### 结构伪类

| 选择器 | 说明 |
| --- | --- |
| `:first-child` | 父元素的第一个子元素 |
| `:last-child` | 父元素的最后一个子元素 |
| `:only-child` | 父元素唯一子元素 |
| `:nth-child(An+B)` | 第 An+B 个子元素（`2n` 偶数、`2n+1`/`odd` 奇数、`3n` 每三、具体数字 `1`） |
| `:nth-last-child(An+B)` | 从末尾计数的 `:nth-child` |
| `:first-of-type` | 同类型中第一个 |
| `:last-of-type` | 同类型中最后一个 |
| `:only-of-type` | 同类型中唯一 |
| `:nth-of-type(An+B)` | 同类型中第 An+B 个 |
| `:nth-last-of-type(An+B)` | 从末尾计数的 `:nth-of-type` |
| `:empty` | 无子节点（含文本）的元素 |
| `:root` | 文档根元素 |
| `:not(:empty)` | 可组合否定 |

#### `:nth-child` 的 `of S` 语法（CSS4）

现代浏览器支持在 `nth-child` 中限定候选集合。本库完整支持：

```php
$doc->find('li:nth-child(2 of .active)');     // 在 .active 的 li 中排第 2
$doc->find('li:nth-child(odd of .item)');     // 在 .item 中排奇数位
$doc->find('tr:nth-of-type(2 of .row)');      // 在 .row 行中排第 2
```

> 元素自身也必须匹配 `of` 过滤条件。`of S` 可与 `An+B`、`even`/`odd`、反向 `nth-last-*` 任意组合。

#### 逻辑组合伪类（CSS4）

| 选择器 | 示例 | 说明 |
| --- | --- | --- |
| `:is()` | `:is(h1, h2, h3)` | 匹配括号内任一选择器（等价于 `h1, h2, h3` 但可作为复合一部分） |
| `:where()` | `:where(.a, .b)` | 同 `:is()`，但特异性恒为 0 |
| `:not()` | `:not(.hidden)` | 否定单个复合选择器 |
| `:not()` 复杂 | `:not(.a .b)` | 否定含组合器的选择器（无后代 `.a .b` 的元素） |
| `:not()` 列表 | `:not(div, span)` | 否定列表中任一 |

```php
$doc->find('section :is(h1, h2, h3)');      // section 内的 h1/h2/h3
$doc->find('a:where(.ext, .link)');         // 链接且含 ext/link 类
$doc->find('div:not(.box .y)');             // 不含后代 .box .y 的 div
$doc->find('li:not(.a, .b)');               // 既非 .a 也非 .b 的 li
```

> `:is()`/`:where()` 内部支持逗号分隔的复合选择器列表，可嵌套其它伪类。

```php
$doc->find('li:is(:not(.a), .b)');   // 排除 .a 类、或匹配 .b 类的 li
$doc->find(':is(li, svg|circle)');   // li 或 SVG circle（命名空间可嵌套）
$doc->find('a:where(:not(.ext), .link)');
```

#### 关系伪类（CSS4）

| 选择器 | 示例 | 说明 |
| --- | --- | --- |
| `:has()` | `div:has(img)` | 含后代 `img` 的 `div` |
| `:has()` 子代 | `div:has(> span)` | 直接子代含 `span` 的 `div` |
| `:has()` 兄弟 | `h1:has(+ p)` | 后有相邻 `p` 的 `h1` |
| `:has()` 复杂 | `article:has(> .meta, footer)` | 组合匹配 |

```php
$doc->find('li:has(> a)');            // 直接子代含链接的 li
$doc->find('div:has(img, video)');    // 含图片或视频的 div
$doc->find('section:has(h2 + p)');    // 含「h2 后紧跟 p」结构的 section
```

> `:has()` 内部选择器支持 `>`、`+`、`~` 组合器与任意复合选择器；`> X` 仅匹配直接子代，其余为后代。

#### 表单伪类

| 选择器 | 说明 |
| --- | --- |
| `:checked` | 选中的 checkbox/radio |
| `:disabled` | 禁用元素 |
| `:enabled` | 启用元素 |
| `:required` | 必填 |
| `:optional` | 非必填 |
| `:read-only` / `:read-write` | 只读 / 可写 |
| `:selected` | 选中的 option |

```php
$doc->find('input:checked');
$doc->find('select:has(option:selected)');
```

#### 文本/链接伪类

| 选择器 | 说明 |
| --- | --- |
| `:contains(text)` | 文本内容包含 `text` 的元素 |
| `:link` / `:visited` | 链接（按 `href` 存在判定） |
| `:parent` / `:empty` | 有子节点 / 无子节点 |

#### 状态伪类

| 选择器 | 说明 |
| --- | --- |
| `:first` / `:last` | 首个 / 末个元素（文档顺序） |
| `:even` / `:odd` | 偶数 / 奇数位置 |
| `:gt(n)` / `:lt(n)` / `:eq(n)` | 位置大于 / 小于 / 等于 n |
| `:header` | `h1`~`h6` 标题 |
| `:animated` / `:focus` | 动态/聚焦（DOM 静态解析下按结构近似） |

### 4. 伪元素 (Pseudo-elements)

伪元素用于从匹配元素**提取特定内容**，返回的是字符串/值而非元素对象：

| 伪元素 | 示例 | 返回 |
| --- | --- | --- |
| `::text` | `.title::text` | 元素文本内容 |
| `::attr(name)` | `a::attr(href)` | 指定属性值 |
| `::html` | `.box::html` | 内部 HTML |

```php
$links = $doc->find('a::attr(href)');   // ['/a', '/b', ...]
$titles = $doc->find('h1::text');       // ['标题1', '标题2']
```

> 伪元素与 `findFirst()` 配合可提取单个值；未匹配时返回 `null`。
> 伪元素提取支持上下文节点（在 `find($sel, $type, $context)` 的上下文子树内定位）。

### 5. 选择器列表

逗号分隔的多个选择器返回所有匹配的并集（按文档顺序去重）：

```php
$doc->find('h1, h2, .lead');
$doc->find('.card, .panel, table tr');
```

---

## 二、XPath 选择器

直接使用 XPath 1.0 表达式，适合复杂结构查询：

```php
// 绝对路径
$doc->find('/html/body/div', Query::TYPE_XPATH);

// 相对路径（从文档根）
$doc->find('//div[@class="item"]', Query::TYPE_XPATH);

// 带命名空间（XML）
$doc->find('//svg:circle', Query::TYPE_XPATH);

// 轴与函数
$doc->find('//ul/li[position() mod 2 = 1]', Query::TYPE_XPATH);     // 奇数 li
$doc->find('//a[contains(@href, "example")]', Query::TYPE_XPATH);

// 文本节点 / 注释 / CDATA
$doc->find('//text()', Query::TYPE_XPATH);          // 文本节点（返回 Text 对象）
$doc->find('//comment()', Query::TYPE_XPATH);       // 注释（返回 Comment 对象）
```

> `wrapNode` 会按节点类型自动包装为 `Element` / `Text` / `Comment` / `Cdata`，
> 因此 XPath 查询文本节点时返回的是类型正确的对象而非裸字符串。

---

## 三、正则表达式选择器

在元素文本、属性或 HTML 上执行正则匹配，支持捕获组提取。

```php
use zxf\Dom\Document;

$doc = Document::create($html);

// 文本匹配
$results = $doc->findByRegex('/(\d{4})-(\d{2})-(\d{2})/', Document::REGEX_TEXT);

// 属性匹配
$results = $doc->findByRegex('/id=(\d+)/', Document::REGEX_ATTR, 'href');

// HTML 匹配
$results = $doc->findByRegex('/<span[^>]*>(.*?)<\/span>/', Document::REGEX_HTML);

// 带标志
$results = $doc->findByRegex('/hello/i', Document::REGEX_TEXT, null, 'i');
```

`findByRegex` 返回数组，每项含：

```php
[
    'element' => Element,        // 匹配到的元素
    'tag'     => 'div',          // 标签名
    'value'   => '完整匹配文本',   // 匹配到的原文
    'groups'  => ['2026', '09', '01'],  // 捕获组（含命名组）
    'offset'  => 12,             // 在来源中的偏移
]
```

> 正则语法为 PCRE（PHP），支持命名组 `(?<year>\d{4})`、非捕获组 `(?:...)`、
> 惰性量词 `*?`、前瞻断言等。捕获组结果同时以数字索引与命名键返回。

---

## 四、上下文节点与链式查找

所有 `find` 系列方法均接受上下文节点参数，实现作用域查找：

```php
$container = $doc->first('.container');
$container->find('.item');                 // Element 上的链式查找
$doc->find('.item', Query::TYPE_CSS, $container->getNode());  // 等价

// 在遍历中维护上下文
foreach ($doc->find('ul') as $ul) {
    $items = $ul->find('li');              // 仅当前 ul 内的 li
}
```

> 上下文节点场景下，CSS 选择器自动编译为相对 XPath（`.` 轴），
> 伪元素（`::text` / `::attr`）也会在上下文子树内定位。

---

## 五、性能与最佳实践

1. **优先 CSS 选择器**：编译为 XPath 后执行，覆盖绝大多数场景。
2. **精确属性优于通配**：`[data-id="2"]` 比 `*[contains(@class,"x")]` 更快。
3. **`:has()` 成本高**：`:has()` 需要检查每个候选的后代，复杂文档中慎用深层 `:has()`。
4. **伪元素一次一值**：`::text` / `::attr` 用于提取而非过滤，提取场景优先于遍历后 `text()`。
5. **正则作用域**：`findByRegex` 默认在文本上匹配，给定属性名可缩小范围。
6. **缓存编译结果**：`Query::compile()` 内部对相同选择器有静态缓存。

---

## 六、与浏览器实现的差异说明

本引擎以 XPath 1.0 为执行后端，下列情况与浏览器存在语义差异：

- `:nth-child(An+B of S)`：候选集合的「位置」基于文档中**匹配 S 的同父兄弟**的实际顺序。
- 大小写不敏感 `i`：通过 XPath `translate()` 转小写实现，仅对属性值生效。
- `:has()`：等价于「存在后代/子代匹配」，不支持浏览器中基于布局的 `:has()` 边界情况。
- 伪元素提取：文本节点的空白处理遵循 DOM `textContent` 语义，与浏览器 `innerText` 的换行归一化不同。
- 命名空间（SVG/MathML）：需显式在 XPath 中声明前缀，CSS 选择器对命名空间元素的支持有限。
