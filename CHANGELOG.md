# Change Log
All notable changes to this project will be documented in this file.

## [1.6.2] - 2016-12-07

### Changed
- added skipFields to General::getFields()

## [1.6.1] - 2016-12-06

### Changed
- fixed phpfastcache 5 composer dependency

## [1.6.0] - 2016-12-05

### Changed
- phpfastcache upgrade from 4 to 5, please adjust your modules!

## [1.5.6] - 2016-12-05

### Added
- General::getModelInstances()
- General::getTableArchives()

## [1.5.5] - 2016-12-02

### Changed
- Files::sanitizeFileName() makes usage of contao standardize() function, and extension will now always be lower case

### Added
- Files::addUniqIdToFilename()
- Files::getUniqueFileNameWithinTarget()
