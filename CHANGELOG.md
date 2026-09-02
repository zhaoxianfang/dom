# 更新日志 (Changelog)

所有重要变更均记录于此文件。

## [2.1.0] - 2026-09-01

### 增强 (Enhancements) - 选择器引擎

- **CSS4 逻辑组合伪类**：新增 `:is()`/`:where()` 支持（编译为 XPath `or` 组合，作用于当前元素；
  `:where()` 与 `:is()` 行为一致）；`:not()` 从「仅单选择器」升级为支持**复杂选择器**与**逗号列表**
  （`:not(.a .b)`、`:not(div, span)` 等），含组合器时自动转为后代否定。
- **CSS4 关系伪类 `:has()`**：保留后代匹配语义，并新增 `:has(> X)` 直接子代、`+:has(+ X)`/`:has(~ X)` 后续兄弟组合器支持。
- **`nth-child(An+B of S)` / `nth-of-type(... of S)`**：支持 CSS4 的 `of S` 过滤语法，在匹配 `S` 的候选兄弟集合中定位，
  元素自身也需满足 `of` 条件；可与 `even`/`odd`/反向 `nth-last-*` 任意组合。
- **属性大小写不敏感修饰符 `[attr="v" i]`**：新增 CSS4 `i`/`s` 修饰符支持，通过 XPath `translate()` 实现不区分大小写匹配
  （原仅区分大小写）。
- **后代/组合器解析括号感知**：修复解析器在 `:has(> span)`、`:not(.a .b)`、`[title="hello world"]`、`[class="A" i]` 等
  含**合法内部空格**的选择器上，错误按顶层空格/组合器分割的缺陷（新增 `splitTopLevelWhitespace`/`splitCombinators`，
  仅在不处于 `()`/`[]` 内时按空白与 `> + ~` 分割）。
- **伪元素 `::html`**：在 `::text`/`::attr(name)` 之外新增 `::html` 伪元素，从匹配元素提取内部 HTML。
- **命名空间选择器 `ns|tag` / `*|tag`**：支持 CSS 命名空间语法匹配 SVG/MathML 等元素，统一编译为 `*[local-name()='tag']`
  （规避 libxml 默认未注册命名空间前缀导致的 `Undefined namespace prefix` 错误）；`ns|*` 降级为匹配所有元素。
- **嵌套逻辑伪类**：`PATTERN_PSEUDO_CLASS` 改用平衡括号正则 `(?:[^()]+|(?R))*`，支持 `:is(:not(.a), .b)` 等嵌套括号伪类；
  `compileSegmentConditions` 对 `:not` 伪类分支改用 `trim(..., '[]')`（双侧去方括号），修复 `not(...])` 多余括号导致的无效 XPath。

### 修复 (Bug Fixes)

- **CSS 引擎 `matches()` 误判自身匹配**：原实现在「非严格」分支把当前元素的**后代**包进 `<root>` 再判断，
  导致 `.s` 类选择器错误匹配**无 class 属性的** `body`/`html`（判断的是后代是否匹配而非自身）。
  现已重写为：简单选择器走快速严格的 tag/id/class/属性比较，含伪类时回退到「父上下文 XPath 查询 + 比较自身节点」
  的严格匹配。修复后 `closest()`/`parents()`/`findByText()` 的过滤结果精确无误。
- **`DocumentFragment::append` 遍历 live NodeList 丢节点**：字符串路径直接遍历 `$temp->childNodes`（live 集合），
  在 `importNode` 后迭代器失效，多顶层元素（如 `<li>B</li><li>C</li>`）只插入第一个。现已先快照为数组再遍历。
- **`Node::before` 签名缺 `string`**：`before()` 声明拒绝 HTML 字符串参数（虽内部已支持 createFragment），
  导致 `$el->before('<i>...</i>')` 抛 `TypeError`。签名已补 `string`（与 `after`/`append`/`prepend`/`replaceWith` 一致）。
- **`wrapNode` 类型丢失**：文本/注释/CDATA 节点被错误包装为 `Element`（对其调用 `getAttribute` 等方法会异常）。
  现按节点类型返回 `Text`/`Comment`/`Cdata`，保证链式调用类型正确、`html()` 正确还原（如 `<!--c-->`、`<![CDATA[...]]>`）。
- **`loadHtml` 输出泄漏 XML 声明前缀**：加载时为保证 UTF-8 编码注入的 `<?xml encoding="UTF-8" ?>` 声明节点，
  会被 `saveHTML` 泄漏到所有 `html()`/`outerHtml()` 输出中。加载完成后已自动移除该临时声明节点，中文等仍正确编码。
- **`findWithFallback` 的 `json` 类型调用未定义函数**：`handleJsonData()` 误用不存在的 `parse_json()`，
  导致 `type => 'json'` 选择器触发致命错误。已改为 `json_decode(..., true)` 并兼容数组/字符串输入。
- **`findWithFallback` 的 `json` 类型忽略选择器范围**：原实现只解析整个文档原始内容，忽略 `selector`。
  现若指定 `selector`，优先解析匹配元素内的 JSON 文本，更符合 fallback 语义。
- **`findFirstWithFallback` 不支持字符串简写**：签名原仅接受数组。现已支持字符串简写（如 `findFirstWithFallback('table.grid')`），
  与 `findWithFallback` 行为一致。
- **`Document::getByText` 不存在**：`getByText()` 曾通过魔术方法抛 `BadMethodCallException`。已显式实现并委托给 `findByText()`。
- **注释中 `<?xml` 触发 PHP 开标签陷阱**：源码注释里书写 `<?xml ?>` 会被 PHP 词法分析器误判为未闭合的开标签，
  导致整个文件解析失败（`load()` 的清理逻辑注释已规避此写法）。
- **HTML5 / void 元素解析**：`setInnerHtml()`、`append()`/`prepend()`/`before()`/`after()`/`replaceWith()`
  传入的 HTML 字符串片段，原先误用 `appendXML()` 导致 `<br>` 等 void 元素、HTML 实体（如 `&amp;`）
  被错误丢弃或转义。现已统一通过临时 `DOMDocument` 以 HTML 语义解析后再 `importNode` 并入，
  行为与浏览器一致。
- **loadHtml 安全性与编码**：HTML 加载新增 `LIBXML_NONET`（禁止解析外部实体/网络资源，避免 SSRF 与外链阻塞），
  同时保留 UTF-8 编码声明以确保中文等多字节字符不再乱码；移除 `LIBXML_HTML_NOIMPLIED`
  （该标志在多根文档/含 `lang` 等属性时会静默丢失节点或属性）。
- **节点类型丢失**：`wrapImportedNode()` 原先对导入/克隆的元素返回匿名类，导致 `children()`、`closest()`、
  `matches()` 等类型方法失效。现在统一包装为 `Element`；文本/注释/CDATA 节点分别包装为 `Text`/`Comment`/`Cdata`，
  保证链式调用不丢方法。
- **findByText / findFirstByText 范围错误**：原先会返回根 `<html>`/`<body>`（因其包含目标文本）。
  现仅返回「最内层」匹配元素（自动剔除是其它匹配元素祖先的节点）。
- **getByText 信号值误用**：`getByText()` 返回空数组时原先用空数组作「未找到」信号，与查找结果为空时混淆。
  已改为 `null`/`[]` 语义清晰（无匹配返回空数组，异常返回 null）。

### 新增 (Added)

- **节点关系遍历**：`Element::closest()`、`ancestors()`、`parents()`、`siblings()`、`next()`、`previous()`，
  以及 `querySelector()` / `querySelectorAll()`（相对当前元素上下文的 CSS 查询）。
- **节点包装操作**：`wrap()`、`unwrap()`；`replaceWith()` 支持 HTML 字符串与数组。
- **原始 HTML 插入**：`append()`/`prepend()`/`before()`/`after()` 现在均接受 HTML 字符串片段。
- **findWithFallback / findFirstWithFallback 全面支持所有 DOM 操作**：
  - 兼容「字符串简写」数组：`['#a', '.b', 'div']`；
  - 支持 `::text` 与 `::attr(name)` 伪元素；
  - 新增 `value`（表单值）、`html`（内部 HTML）、`textby`（按文本）类型，配合原有 css/xpath/regex/table/list/form/link/image/text/json，共 12 种类型；
  - `findFirstWithFallback` 同样支持以上全部类型，命中时可返回节点、属性标量或文本。

### 优化 (Optimized)

- **查询性能**：`Document` 复用单一 `DOMXPath` 对象（每个文档实例仅构建一次），循环内重复查询显著加速；
  选择器编译结果仍由 `Query::$compiled` 静态缓存复用。
- **节点导入**：`append`/`prepend`/`before`/`after` 等批量插入时按原顺序正确排布，并对 HTML 字符串走片段解析。

### 文档 (Docs)

- README.md 全面更新：特性列表、findWithFallback 字符串简写与全部 12 种类型示例、Element 关系遍历与 HTML 字符串操作、
  selectors 数组格式附录、Element 方法速查表、XPath 缓存性能建议。

---

## [2.0.0] - 2026-05-14

初始公开发布。详见 README.md。
