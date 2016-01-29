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

The module currently supports methods

* doesBucketExist
* deleteBucket

And assertions

* seeBucket