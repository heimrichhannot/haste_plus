# Files class

> This documentation is incomplete

Namespace: `HeimrichHannot\Haste\Util`

## Methods

### sanitizeFileName

Sanitize a file or folder name. Also removes the "id-" prefix generated in the contao `standardize` method.
A max filename lenght can be set via the `maxCount` parameter.
By default names are converted to lowercase. This can be bypassed by set `preserveUppercase` to true.

```
Files::sanitizeFileName(string $fileName, int $maxCount = 0, boolean $preserveUppercase = false): string
```