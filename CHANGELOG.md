# Change Log
All notable changes to this project will be documented in this file.

## [1.6.24] - 2017-02-10

### Changed
- removed preset skipFields in getFields()

## [1.6.23] - 2017-02-02

### Added
- added General::getModelInstanceIfId()

## [1.6.22] - 2017-01-23

### Fixed
- made all overridden fields mandatory -> else user won't check "overrideFieldName"

## [1.6.21] - 2017-01-18

### Fixed
- performance tweaks
- added DC_HastePlus

## [1.6.21] - 2017-01-18

### Added
- multiColumnEditor support

## [1.6.20] - 2017-01-17

### Fixed
- Replaced array() by []

## [1.6.19] - 2017-01-17

### Added
- General::addOverridableFields() and General::getOverridableProperty()

## [1.6.18] - 2017-01-17

### Changed
- add headerXFrameSkipPages to tl_settings configuration and do not set "X-Frame-Options: SAMEORIGIN" for this pages

## [1.6.17] - 2017-01-12

### Added
- Salutations

## [1.6.16] - 2017-01-10

### Added
- StringUtil::html2Text()

### Added
- Stringutil::generateEmailWithName()

## [1.6.15] - 2016-12-16

### Added
- Stringutil::generateEmailWithName()

## [1.6.14] - 2016-12-16

### Added
- Added new eval rgxp `posfloat`

## [1.6.13] - 2016-12-15

### Changed
- FormSubmission::prepareSpecialValueForPrint, check that varValue is no array

## [1.6.12] - 2016-12-15

### Changed
- FormSubmission::prepareData will now add all fields (also empty) to $arrSubmissionData. Otherwise `##form_submission_*##` tokens that are not present within token array, will stay in notification center e-mails. If the field is empty, `##form_submission_*##` will be replaced with an empty string, field label wont be added.

## [1.6.11] - 2016-12-14

### Added
- Model::setDefaultsFromDca()

## [1.6.10] - 2016-12-14

### Added
- Files::getFileLineCount()

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
