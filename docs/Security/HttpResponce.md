# HttpResponce class

## Contao 4

In Contao 4 managed edition the X-Frame-Options settings are populated by the [NelmioSecurityBundle](https://github.com/nelmio/NelmioSecurityBundle) and the Haste Plus settings will be overwritten. In the contao manager bundle all sites are set to `X-Frame-Options: SAMEORIGIN` by the [bundle settings](https://github.com/contao/manager-bundle/blob/master/src/Resources/contao-manager/nelmio_security.yml). Because of the config handling from Symfony and NelmioSecurityBundle, this can't be overwritten by the settings (it's always the first and will always match). 

More Informations about this topic is found in the correponding [issue](https://github.com/contao/manager-bundle/issues/48).


An other way we found is to implement a compiler pass and overwrite it there or move the corresponding value at the bottom of the array:

```
$paths = $container->getParameter('nelmio_security.clickjacking.paths');
$key = '^/.*';
$catchAll = $paths[$key];
unset($paths[$key]);
$paths[$key] = $catchAll;
$container->setParameter('nelmio_security.clickjacking.paths', $paths);
```
