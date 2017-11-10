# StringUtil class

> This documentation is incomplete

Namespace: `HeimrichHannot\Haste\Util`

## Methods

### convertGermanSpecialLetters

Converts german umlauts an the ß character to webconform characters. The method can be used static.

```
StringUtil::convertGermanSpecialLetters(string $str): string
```

#### Return
The string, where "ä", "ö", "ü", "ß", "Ä", "Ö", "Ü" is replaced with "ae", "oe", "ue", "ss", "Ae", "Oe", "Ue"

### nl2p

Convert new line or br with <p> tags

```
StringUtil::nl2p(mixed $text): string
```


### replaceNonXmlEntities

Converts non XML Entities in XML Strings to compatible entities.

```
StringUtil::replaceNonXmlEntities(string $str): string
```

#### Return
The string where `&nbsp;` and `&mdash;` are replace with `&#xA0;` or `&#x2014;`.
