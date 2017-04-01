MATA Media
==========================================

Media module interfaces with media storages such as AWS S3 to host content like images, videos and files. It is used extensively by many MATA and MATA CMS modules.


Installation
------------

- Add the module using composer:

```json
"mata/mata-media": "~1.0.0"
```

-  Run migrations
```
php yii migrate/up --migrationPath=@vendor/mata/mata-media/migrations
```


Changelog
---------

## 1.0.4.3-alpha, April 1, 2017
- Added the ability to overwrite the module

## 1.0.4.2-alpha, October 28, 2016

- Added client validation for MandatoryMediaValidator

## 1.0.4.1-alpha, October 4, 2016

- Updates for S3Controller

## 1.0.4-alpha, June 9, 2015

- Added dependency on mata/mata-framework : ~1.1.0-alpha

## 1.0.3-alpha, June 3, 2015
- Updated config for CodeCeption
- Addressed issue with multiple registration of handlers for events in BootstrapInterface
- Added MediaTest which currently encapsulates Media, Environment and AR History test cases
- Removed AspectMock

## 1.0.2-alpha, May 28, 2015
- Changed the logic for Media to allow for versioning, environments and media deletion
- Added migration which renames [[DocumentId]] column to [[For]]

## 1.0.1-alpha, May 25, 2015
- [[Media]] model uses [[mata\db\ActiveQuery]]
- Added caching for getting single individual records with [[cachedOne()]]
- Added dependency on mata-framework ~1.0.3-alpha

## 1.0.0-alpha, May 18, 2015

- Initial release.
