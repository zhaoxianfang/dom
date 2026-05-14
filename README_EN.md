# zxf/dom - Powerful PHP DOM Operation Library

A powerful and easy-to-use PHP DOM manipulation library that provides a simple API for parsing, querying, and manipulating HTML/XML documents.


[🇨🇳 查看中文文档 (README.md)](README.md)

## Features

- ✅ **Complete CSS3 Selector Support** - Support for 150+ CSS selector types
- ✅ **Native XPath Support** - Direct XPath expression querying
- ✅ **Rich Pseudo-classes** - Support for 100+ pseudo-class selectors
- ✅ **Pseudo-element Support** - Support for `::text` and `::attr()` pseudo-elements
- ✅ **Extended Selector Features** - Text length matching, attribute length/count selectors, depth-based selectors
- ✅ **Regex Support** - Powerful regex matching and data extraction
- ✅ **Table Data Extraction** - Structured table data extraction (thead/tbody/tfoot separation)
- ✅ **List Data Extraction** - Nested list recursive extraction support
- ✅ **Matrix Data Extraction** - Non-table grid data extraction with custom row/cell selectors
- ✅ **Form Data Extraction** - Extract form field data as associative arrays
- ✅ **Link/Image Data Extraction** - Extract structured link and image data
- ✅ **Smart Selector Types** - findWithFallback supports css/xpath/regex/table/list/form/link/image/text/json (10 types)
- ✅ **JSON Data Handling** - Parse and extract JSON strings/arrays/objects
- ✅ **Chaining** - Fluent API design with chainable operations
- ✅ **PHP 8.2+ Type System** - Complete type annotations for better IDE support
- ✅ **HTML/XML Dual Mode** - Support for both HTML and XML document processing
- ✅ **High Performance** - Selector compilation cache for improved query speed
- ✅ **UTF-8 Encoding Support** - Full support for Chinese and multi-byte characters
- ✅ **Form Element Operations** - Specialized form selectors and manipulation methods
- ✅ **Error Handling** - Unified exception handling and error reporting
- ✅ **Complete Test Coverage** - 230+ test cases ensuring code quality

## Requirements

- PHP >= 8.2 (supports 8.2, 8.3, 8.4)
- libxml extension
- cURL extension (for loading documents from remote URLs)

## Installation

### Using Composer

```bash
composer require zxf/dom
```

### Manual Installation

```php
require_once 'path/to/Query.php';
require_once 'path/to/Document.php';
// ... other files

use zxf\Dom\Selectors\Query;
use zxf\Dom\Document;

Query::initialize();
```

## Quick Start

### Basic Usage

### Basic Usage

```php
use zxf\Dom\Document;

// Create document from HTML string
$doc = new Document('<div class="container"><p>Hello World</p></div>');

// Find elements
$elements = $doc->find('.container p');
echo $elements[0]->text(); // Output: Hello World

// Get first element
$element = $doc->first('.container');
echo $element->html(); // Output: <p>Hello World</p>

// Get text using pseudo-element
$text = $doc->text('.container p::text');
echo $text; // Output: Hello World

// Get attribute using pseudo-element
$html = '<a href="https://example.com">Link</a>';
$doc = new Document($html);
$url = $doc->text('a::attr(href)');
echo $url; // Output: https://example.com
```

### XML Document Processing

```php
$xml = '<root><item id="1">Item 1</item><item id="2">Item 2</item></root>';
$doc = new Document($xml, false, 'UTF-8', Document::TYPE_XML);

$items = $doc->find('item');
foreach ($items as $item) {
    echo $item->attr('id') . ': ' . $item->text() . "\n";
}
```

### Chaining

```php
$doc = new Document('<div class="container"><p>Text</p></div>');

// Document chaining
$doc->addClass('.container', 'active')
    ->addClass('.container', 'highlight')
    ->css('.container', 'color', 'red');

// Element chaining
$element = $doc->first('.container');
$element->addClass('class1')
        ->addClass('class2')
        ->css('background', 'blue')
        ->attr('data-id', '123');
```

## Supported Selectors

### CSS Selectors (70+ Types)

**Basic Selectors:**
- `*` - Wildcard selector
- `tag` - Tag selector
- `.class` - Class selector
- `#id` - ID selector
- `s1, s2` - Multiple selectors
- `s1 s2` - Descendant selector
- `s1 > s2` - Child selector
- `s1 + s2` - Adjacent sibling selector
- `s1 ~ s2` - General sibling selector

**Attribute Selectors:**
- `[attr]` - Has attribute
- `[attr=value]` - Attribute equals
- `[attr~=value]` - Attribute contains word
- `[attr|=value]` - Attribute equals or starts with
- `[attr^=value]` - Attribute starts with
- `[attr$=value]` - Attribute ends with
- `[attr*=value]` - Attribute contains

**Pseudo-classes (100+ types):**
- Structural: `:first-child`, `:last-child`, `:nth-child(n)`, etc.
- Content: `:contains(text)`, `:has(selector)`, `:empty`, etc.
- Form: `:enabled`, `:disabled`, `:checked`, `:required`, etc.
- Form elements: `:text`, `:password`, `:checkbox`, `:radio`, etc.
- HTML elements: `:header`, `:input`, `:button`, `:link`, etc.
- Position: `:first`, `:last`, `:even`, `:odd`, `:eq(n)`, etc.
- Visibility: `:visible`, `:hidden`

**Pseudo-elements:**
- `::text` - Get element text content
- `::attr(name)` - Get element attribute value

### XPath Selectors

- Complete XPath 1.0 support
- All XPath functions: `contains()`, `starts-with()`, `position()`, `last()`, etc.
- All XPath axes and operators

```php
// XPath examples
$elements = $doc->xpath('//div[@class="container"]');
$elements = $doc->xpath('//a[contains(@href, "example.com")]');
$elements = $doc->xpath('(//div[@class="item"])[1]');
```

## API Reference

### Document

Main document class representing HTML/XML documents.

```php
use zxf\Dom\Document;

// Create document
$doc = new Document($htmlString);
$doc = new Document($htmlString, false, 'UTF-8', Document::TYPE_XML);

// Load content
$doc->load($string);
$doc->load($file, true);

// Save document
$doc->save($filename);

// Find elements
$elements = $doc->find('div');
$element = $doc->first('div');

// Get content
$html = $doc->html();
$text = $doc->text();
$title = $doc->title();

// Element operations
$doc->addClass('.selector', 'class-name');
$doc->removeClass('.selector', 'class-name');
$doc->hasClass('.selector', 'class-name');
$doc->css('.selector', 'property', 'value');
$doc->attr('.selector', 'attribute', 'value');
$doc->removeAttr('.selector', 'attribute');

// XPath queries
$elements = $doc->xpath('//div[@class="item"]');
```

### Element

Represents an element in the document.

```php
$element = $doc->first('div');

// Content
$text = $element->text();
$html = $element->html();
$element->setValue('new text');
$element->setHtml('<p>new html</p>');

// Attributes
$value = $element->attr('name');
$element->attr('name', 'value');
$allAttrs = $element->attributes();
$element->removeAttr('name');

// Classes
$element->addClass('class1', 'class2');
$element->removeClass('class1');
$element->hasClass('class1');
$classes = $element->classes()->all();

// Styles
$element->css('color', 'red');
$color = $element->css('color');
$styles = $element->style()->all();

// Node operations
$parent = $element->parent();
$children = $element->children();
$firstChild = $element->firstChild();
$lastChild = $element->lastChild();
$siblings = $element->siblings();
$index = $element->index();

// Manipulation
$element->append($newElement);
$element->prepend($newElement);
$element->before($newElement);
$element->after($newElement);
$element->remove();
$element->empty();
$cloned = $element->clone();
```

### ClassAttribute

Manages element class attributes.

```php
$classes = $element->classes();

// Add classes
$classes->add('class1', 'class2');

// Remove classes
$classes->remove('class1');

// Check class
$has = $classes->has('class1');

// Get all classes
$all = $classes->all();

// Clear all classes
$classes->clear();

// Toggle class
$classes->toggle('active');
```

### StyleAttribute

Manages element style attributes.

```php
$style = $element->style();

// Set styles
$style->set('color', 'red');
$style->set(['color' => 'red', 'background' => 'blue']);

// Get styles
$color = $style->get('color');
$all = $style->all();

// Remove style
$style->remove('color');

// CamelCase support
$style->set('backgroundColor', 'red');
```

### Encoder

Utility class for encoding/decoding.

```php
use zxf\Dom\Utils\Encoder;

// HTML encoding
$html = Encoder::encodeHtml('<script>alert("XSS")</script>');

// HTML decoding
$html = Encoder::decodeHtml('&lt;script&gt;');

// URL encoding
$url = Encoder::encodeUrl('中文内容');

// URL decoding
$url = Encoder::decodeUrl('%E4%B8%AD%E6%96%87');
```

### Errors

Error handling utilities.

```php
use zxf\Dom\Utils\Errors;

// Silence errors
Errors::silence();

// Enable logging
Errors::setLoggingEnabled(true);
Errors::setLogFile('/path/to/log.txt');

// Custom error handler
Errors::setErrorHandler(function($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile:$errline");
});
```

## Examples

### Example 1: Find Elements

```php
$doc = new Document('<div class="item">1</div><div class="item">2</div>');
$items = $doc->find('.item');
foreach ($items as $item) {
    echo $item->text() . "\n";
}
```

### Example 2: Modify Elements

```php
$doc = new Document('<div class="container">Text</div>');
$doc->addClass('.container', 'active');
$doc->css('.container', 'color', 'red');
echo $doc->html();
```

### Example 3: Web Scraping

```php
$html = file_get_contents('https://example.com');
$doc = new Document($html);

$links = $doc->find('a[href]');
foreach ($links as $link) {
    echo $link->text() . ': ' . $link->attr('href') . "\n";
}
```

### Example 4: Table Data Extraction

```php
$html = '<table>
    <tr><td>ID</td><td>Name</td></tr>
    <tr><td>1</td><td>Product A</td></tr>
    <tr><td>2</td><td>Product B</td></tr>
</table>';

$doc = new Document($html);

// Method 1: Manual extraction
$rows = $doc->find('tr:not(:first-child)');
foreach ($rows as $row) {
    $cells = $row->find('td');
    echo $cells[0]->text() . ': ' . $cells[1]->text() . "\n";
}

// Method 2: extractTable() API
$tableData = $doc->extractTable('table');
// Returns structured data:
// ['thead' => ['ID', 'Name'], 'tbody' => [['1', 'Product A'], ['2', 'Product B']]]

// Method 3: Via findWithFallback
$tableData = $doc->findWithFallback([
    ['selector' => 'table', 'type' => 'table'],
]);
```

### Example 5: Advanced Data Extraction with findWithFallback

```php
$html = '<div>
    <table class="products">
        <thead><tr><th>Name</th><th>Price</th></tr></thead>
        <tbody>
            <tr><td>Product A</td><td>$10</td></tr>
            <tr><td>Product B</td><td>$20</td></tr>
        </tbody>
    </table>
    <ul class="categories">
        <li>Category 1</li>
        <li>Category 2</li>
        <li>Category 3</li>
    </ul>
    <form id="search-form">
        <input name="keyword" value="search">
        <input name="page" value="1">
    </form>
</div>';

$doc = new Document($html);

// Extract table data with fallback
$tableData = $doc->findWithFallback([
    ['selector' => 'table.products', 'type' => 'table'],
    ['selector' => 'table.data', 'type' => 'table'],
]);

// Extract list data
$listData = $doc->findWithFallback([
    ['selector' => 'ul.categories', 'type' => 'list'],
    ['selector' => 'ol.items', 'type' => 'list'],
]);

// Extract form data
$formData = $doc->findWithFallback([
    ['selector' => 'form#search-form', 'type' => 'form'],
]);

// Extract links
$links = $doc->findWithFallback([
    ['selector' => 'a.external', 'type' => 'link'],
    ['selector' => 'a[href^="https"]', 'type' => 'link'],
]);

// Extract images
$images = $doc->findWithFallback([
    ['selector' => 'img.thumbnail', 'type' => 'image'],
]);

// Extract text content
$texts = $doc->findWithFallback([
    ['selector' => 'p.description', 'type' => 'text'],
]);

// Mixed types
$results = $doc->findWithFallback([
    ['selector' => 'table.products', 'type' => 'table'],
    ['selector' => 'ul.categories', 'type' => 'list'],
]);

// With custom options
$tableData = $doc->findWithFallback([
    [
        'selector' => 'table.products',
        'type' => 'table',
        'extractOptions' => [
            'returnFormat' => 'associative',
            'includeHeader' => true,
        ],
    ],
]);
```

## Performance Tips

1. **Use specific selectors** - More specific selectors are faster
   ```php
   // ✅ Good
   $doc->find('div.container > p.highlight');
   // ❌ Avoid
   $doc->find('div p');
   ```

2. **Cache query results** - Store frequently used elements
   ```php
   // ✅ Good
   $container = $doc->first('.container');
   $item = $container->first('.item');
   // ❌ Avoid
   $doc->first('.container .item');
   ```

3. **Use ID selectors** - ID selectors are the fastest
   ```php
   // ✅ Good
   $doc->find('#main-content');
   // ❌ Avoid
   $doc->find('div[id="main-content"]');
   ```

## Testing

Run the test suite:

```bash
php tests.php
```

Run examples:

```bash
php examples.php
```

## Documentation

- **[README_CN.md](README_CN.md)** - 🇨🇳 中文文档
- **[USER_GUIDE.md](USER_GUIDE.md)** - Complete user guide with examples
- **[RULE_GUIDE.md](RULE_GUIDE.md)** - Comprehensive selector reference (100+ selectors)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

MIT License

## Support

For issues and questions, please use the GitHub issue tracker.

---

*Version: 2.0.0*  
*Last Updated: 2026-05-14*

---

## Appendix: Quick Parameter Reference

### selectors Array Format (for findWithFallback)

```php
[
    'selector'       => 'string',      // CSS/XPath/Regex selector expression
    'type'           => 'string',      // css|xpath|regex|table|list|form|link|image|text|json
    'attribute'      => 'string|null',  // only for type=regex, attribute name to match
    'extractMode'    => 'string|null',  // only for type=regex: elements|text|attr|match
    'group'          => 'int|null',     // only for extractMode=match, group index
    'location'       => 'array|null',   // only for type=regex, multi-group extraction config
    'extractOptions' => 'array|null',   // only for type=table|list|text, extraction options
]
```

### extractTable Options

```php
$options = [
    'selectorType'          => 'auto',       // auto|css|xpath|regex
    'headerRow'             => 0,            // header row index (0-based)
    'skipRows'              => 0,            // rows to skip from start
    'includeHeader'         => true,         // include header data
    'includeHeaderAsFirstRow' => false,      // include header as first row
    'trimText'              => true,         // trim cell whitespace
    'removeEmpty'           => true,         // remove empty rows
    'cellSelector'          => 'td, th',     // cell selector
    'rowSelector'           => 'tr',         // row selector
    'returnFormat'          => 'structured', // structured|associative|indexed|both
    'preserveStructure'     => true,         // preserve thead/tbody/tfoot
    'returnAllTables'       => true,         // return all matching tables
    'tableIndex'            => null,         // specify table index
];
```

### extractList Options

```php
$options = [
    'recursive'    => false,  // recursively extract nested lists
    'trimText'     => true,   // trim whitespace
    'includeIndex' => false,  // include index number
];
```

### queryMatrix Options

```php
$options = [
    'rowSelector'  => null,    // row selector, null=direct children
    'cellSelector' => null,    // cell selector, null=direct children
    'trimText'     => true,    // trim whitespace
    'removeEmpty'  => true,    // remove empty rows
    'selectorType' => 'auto',  // auto|css|xpath|regex
];
```

For complete API documentation, see `src/docs/RULE_GUIDE.md` and `src/docs/USER_GUIDE.md`.
