# StringUtil class

> This documentation is incomplete

Namespace: `HeimrichHannot\Haste\Util`

## Methods

### convertGermanSpecialLetters

Converts german umlauts an the ß character to webconform characters. The method can be used static.

```
StringUtil::convertGermanSpecialLetters(string $str)
```

#### Return
The string, where "ä", "ö", "ü", "ß", "Ä", "Ö", "Ü" is replaced with "ae", "oe", "ue", "ss", "Ae", "Oe", "Ue"


### replaceNonXmlEntities

Converts non XML Entities in XML Strings to compatible entities.

```
StringUtil::replaceNonXmlEntities(string $str)
```

#### Return
The string where `&nbsp;` and `&mdash;` are replace with `&#xA0;` or `&#x2014;`.
