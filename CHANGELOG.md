# Change Log
All notable changes to this project will be documented in this file.

## [1.6.9] - 2016-12-12

### Added
- General::getLocalizedFieldname()

## [1.6.8] - 2016-12-12

### Added
- Widget class

### Fixed
- QueryHelper

## [1.6.7] - 2016-12-09

### Added
- StringUtil::camelCaseToDashed()

## [1.6.6] - 2016-12-08

### Added
- General::getArchiveModalEditLink()

## [1.6.5] - 2016-12-08

### Fixed
- fixed static call error in StringUtil::str_replace_once()

## [1.6.4] - 2016-12-08

### Added
- added foreignKey-support for FormSubmission::prepareSpecialValueForPrint()

## [1.6.3] - 2016-12-08

### Changed
- Files::getFolderFromDca uuid fix, now return correct folder path if varValue is uuid

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