# Changelog
All notable changes to this project will be documented in this file.

## [1.6.85] - 2017-08-28

### Added
- `phpFastCache` symfony support for within contao 4

## [1.6.84] - 2017-08-25

### Added
- Container util class containing shortcuts: isFrontend, isBackend and getRequest

## [1.6.83] - 2017-08-16

### Fixed
- General::checkUrl

### Added
- Member::getMembersAsOptionsIncludingEmailAndIdByGroups

## [1.6.82] - 2017-08-07

### Added
- General::checkUrl

## [1.6.81] - 2017-08-03

### Added
- Number functions

## [1.6.80] - 2017-08-01

### Added
- Member::getMembersAsOptionsIncludingEmailAndId

## [1.6.79] - 2017-07-27

### Added
- `InsertTags` class and two new inserttags `{{trimsplit::,::2,1}} & {{encrypt::SECRET_VALUE}}`

## [1.6.78] - 2017-07-25

### Fixed
- empty line in config.php

## [1.6.77] - 2017-07-24

### Added
- Url::redirect (contao 3 redirect behavior -> no exception thrown as in contao 4)

## [1.6.76] - 2017-07-24

### Fixed
- jquery loading order

## [1.6.75] - 2017-07-21

### Fixed
- Curl

## [1.6.74] - 2017-07-21

### Fixed
- fixed `jquery` js path for contao 4

## [1.6.73] - 2017-07-19

### Fixed
- dlh_marker template

## [1.6.72] - 2017-07-18

### Added
- `width` % and pcnt dimension support for `dlh_googlemaps_haste.html`

## [1.6.71] - 2017-07-18

### Fixed
- `tl_settings` palette invokation

## [1.6.70] - 2017-07-18

### Fixed
- `dlh_googlemaps_haste` maps `_marker` element was changed to `_overlay`, changed back to `_marker`

## [1.6.69] - 2017-07-12

### Added
- `HeimrichHannot\Haste\Util\DOMUtil::createAttributes` to create string tag attributes from array 

## [1.6.68] - 2017-07-11

### Fixed
- `dlh_googlemaps_haste` maps `_marker` element was changed to `_overlay` 

## [1.6.67] - 2017-07-10

### Fixed
- maps marker undefined check

## [1.6.66] - 2017-06-27

### Fixed
- QueryHelper::computeCondition() now works correctly with blobs

## [1.6.65] - 2017-06-27

### Added
- added an action to the custom control

## [1.6.64] - 2017-06-27

### Added
- you can now create your own custom control button for the google map
### Fixed
- undefinded check

## [1.6.63] - 2017-06-23

### Added
- HeimrichHannot\Haste\Util\GeoLocation

## [1.6.62] - 2017-06-12

### Fixed
- General::getModalEditLink(), General::getArchiveModalEditLink()
- contao 4 support for paths

## [1.6.61] - 2017-05-15

### Fixed
- bounds check in GoogleMapsOverlay

## [1.6.60] - 2017-05-09

### Added
- php 7 support

## [1.6.59] - 2017-05-05

### Added
- Curl::prepareHeaderArrayForPrint
- DateUtil::checkFormat()

## [1.6.58] - 2017-04-28

### Fixed
- QueryHelper computeCondition() (broken operators & check for string in case of "like")

## [1.6.57] - 2017-04-26

### Added
- Support for Exif/Iptc data handling

## [1.6.56] - 2017-04-26

### Fixed
- getCurrentLocation bug

## [1.6.55] - 2017-04-25

### Added
- General::getOptionsFromDca

## [1.6.54] - 2017-04-21

### Added
- English locales

## [1.6.53] - 2017-04-19

### Fixed
- update google visualization api loader code and remove jsapi as it generates `Parser-blocking` message within https-secure websites

## [1.6.52] - 2017-04-13

### Added
- added rgxp `maxlength::[length]` to validate html input values

## [1.6.51] - 2017-04-10

### Changed

- tl_settings.headerXFrameSkipPages can now hold multiple pages

## [1.6.50] - 2017-04-05

### Fixed
- return value fixed for `\HeimrichHannot\Haste\Cache\Cache::getInstance()`

## [1.6.49] - 2017-04-05

### Added
- geo.js
- util.js -> call()
- General::findFuzzyAddressOnGoogleMaps()

## [1.6.48] - 2017-03-29

### Fixed
- Check within `HeimrichHannot\Haste\Dca\General` methods that model class exists


## [1.6.47] - 2017-03-24

### Fixed
- Url::generateAbsoluteUrl() -> now correct handling of collections

## [1.6.46] - 2017-03-22

### Fixed
- `price` rgxp and `HeimrichHannot\Haste\Util\Validator::isPrice` validation fixed

## [1.6.45] - 2017-03-22

### Added

-  Added `HeimrichHannot\Haste\Util\Validator` class with `IBAN` and `BIC` validation
- `globalcitizen/php-iban` dependency added 
- added rgxp `iban` to validate input values
- added rgxp `bic` to validate input values

## [1.6.44] - 2017-03-22

### Added

- RemoteImageCache

## [1.6.43] - 2017-03-22

### Fixed

- curl header issue

## [1.6.42] - 2017-03-21

### Fixed

- QueryHelper::createWhereForSerializedBlob()

## [1.6.41] - 2017-03-21

### Fixed

- enhanced Curl

## [1.6.40] - 2017-03-21

### Added

- Added price, customDate widget rgxp within `HeimrichHannot\Haste\Util\Widget::addCustomRegexp()`

### Changed

- Moved posfloat rgxp from `HeimrichHannot\Haste\Util\Numbers::positiveFloatRegExpHook()` to `HeimrichHannot\Haste\Util\Widget::addCustomRegexp()`

## [1.6.39] - 2017-03-20

### Fixed

- enhanced Curl::request()

## [1.6.38] - 2017-03-17

### Fixed

- FormSubmission::prepareSpecialValueForPrint() for non-associative option arrays

## [1.6.37] - 2017-03-15

### Added

- FormSubmission::prepareSpecialValueForSave() checks for eval option `skipPrepareForSave` to skip preparation

## [1.6.36] - 2017-03-13

### Added

- QueryHelper::doBulkInsert(), add missing white space for `UPDATE ON DUPLICATE KEY` part

## [1.6.35] - 2017-03-13

### Added

- QueryHelper::processInPieces(), process big result data from queries in pieces (working with bulksize to free memory again)
- QueryHelper::doBulkInsert(), insert multiple entities wisely (working with bulksize to free memory again)

## [1.6.34] - 2017-03-09

### Added

- General::setDefaultsFromDca() added

## [1.6.33] - 2017-03-07

### Added

- General::getConfigByArrayOrCallbackOrFunction()

## [1.6.32] - 2017-03-03

### Fixed

- HeimrichHannot\Haste\Util\DOMUtil::convertToInlineCss, prevent inlining inside conditional comments

## [1.6.31] - 2017-02-28

### Fixed
- FormSubmission::tokenizeData(), set ##form_value_[fieldname]## to ##form_plain_[fieldname]## for binary values or values that call json_encode to fail

## [1.6.30] - 2017-02-22

### Added
- check within Files::getFileFromUuid against dir

## [1.6.29] - 2017-02-22

### Added
- JS-Helper: HASTE_PLUS.isTruthy()

## [1.6.28] - 2017-02-22

### Added
- JS-Helper: HASTE_PLUS.parseQueryString()

## [1.6.27] - 2017-02-21

### Added
- JS-Helper: HASTE_PLUS.getParameterByName()

## [1.6.26] - 2017-02-20

### Fixed
- HeimrichHannot\Haste\Dca\General::setAuthorIDOnCreate does no check if PROPERTY_AUTHOR and PROPERTY_AUTHOR_TYPE instead of PROPERTY_SESSION_ID (typo)

## [1.6.25] - 2017-02-15

### Added
- added Curl class

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
