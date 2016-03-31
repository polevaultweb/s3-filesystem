s3-filesystem
==========

An Amazon S3 Filesystem module for Codeception.

## Installation
To install simply require the package in the `composer.json` file like

```json
  "require-dev":
    {
      "polevaultweb/s3-filesystem": "master@dev"
    }
```
    
and then use `composer update` to fetch the package.  
After that  follow the configuration instructions below.

### S3Filesystem configuration
S3Filesystem extends `Filesystem` module hence any parameter required and available to that module is required and available in `S3Filesystem` as well.  
In the suite `.yml` configuration file add the module among the loaded ones:

```yml
  modules:
      enabled:
          - S3Filesystem
      config:
          S3Filesystem:
              accessKey: xxxxxxxxxxxx
              accessSecret: xxxxxxxxxxxxxxxxxxxxxxxx
``` 

### Supports

* doesFileExist
* doesBucketExist
* deleteBucket
* getBucketLocation

And assertions

* seeFile
* seeBucket
* seeBucketLocation

### Usage

```php
$I = new AcceptanceTester( $scenario );

$I->setBucket( 'my-bucket' );
$I->seeFile( 'path/to/file.jpg' );
$I->seeBucketLocation( 'eu-west-1' );
```

Or for single uses you can chain like so:

```php
$I = new AcceptanceTester( $scenario );

$I->setBucket( 'my-bucket' )->seeFile( 'path/to/file.jpg' );
```


