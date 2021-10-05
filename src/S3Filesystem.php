<?php
namespace Codeception\Module;

use Aws\S3\S3Client;
use Aws\S3\BatchDelete;

/**
 * Class S3Filesystem
 *
 * @package Codeception\Module
 */
class S3Filesystem extends Filesystem {

	/**
	 * @var array
	 */
	protected $requiredFields = array( 'accessKey', 'accessSecret' );

	/**
	 * @var array
	 */
	protected static $clients;

	/**
	 * @var string
	 */
	protected $bucket;

	/**
	 * @var string
	 */
	protected $region = 'us-east-1';

	/**
	 * Initialize the S3 client
	 */
	public function _initialize() {
		parent::_initialize();
	}

	/**
	 * Set the bucket used in the methods
	 *
	 * @param string $bucket
	 *
	 * @return S3Filesystem $this
	 */
	public function setBucket( $bucket ) {
		$this->bucket = $bucket;

		if ( $this->doesBucketExist() ) {
			$region = $this->getBucketLocation();
			$this->setRegion( $region );
		} else {
			$this->setRegion();
		}

		return $this;
	}

	/**
	 * Set the region used of the bucket
	 *
	 * @param string $region
	 */
	public function setRegion( $region = 'us-east-1' ) {
		$this->region = $region;
	}

	/**
	 * @return mixed
	 */
	protected function getClient() {
		if ( ! isset( self::$clients[ $this->region ] ) ) {
			$args = array(
				'version'     => isset( $this->config['version'] ) ? $this->config['version'] : 'latest',
				'region'      => $this->region,
				'signature'   => isset( $this->config['signature'] ) ? $this->config['signature'] : 'v4',
				'credentials' => array(
					'key'    => $this->config['accessKey'],
					'secret' => $this->config['accessSecret'],
				),
			);

			self::$clients[ $this->region ] = new S3Client( $args );
		}

		return self::$clients[ $this->region ];
	}

	/**
	 * Checks if a bucket exists
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 *
	 * @return bool
	 */
	public function doesBucketExist() {
		try {
			return $this->getClient()->doesBucketExist( $this->bucket );
		} catch ( \Exception $e ) {
			\PHPUnit_Framework_Assert::fail( $e->getMessage() );
		}
	}

	/**
	 * Asserts if a bucket exists
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 */
	public function seeBucket() {
		$this->assertTrue( $this->doesBucketExist() );
	}

	/**
	 * Create a bucket
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 */
	public function createBucket() {
		$args = array( 'Bucket' => $this->bucket );

		if ( ! empty( $this->region ) ) {
			$args['LocationConstraint'] = $this->region;
		}

		try {
			$this->getClient()->createBucket( $args );
		} catch ( \Exception $e ) {
			\PHPUnit_Framework_Assert::fail( $e->getMessage() );
		}
	}

	/**
	 * Delete a bucket
	 *
	 * @param bool $force Optionally force bucket delete even if it is not empty.
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 */
	public function deleteBucket( $force = false ) {
		try {
			$client = $this->getClient();

			// Delete the objects in the bucket before attempting to delete the bucket.
			if ( $force ) {
				$delete = BatchDelete::fromListObjects( $client, array( 'Bucket' => $this->bucket ) );
				$delete->delete();
			}

			$client->deleteBucket( array( 'Bucket' => $this->bucket ) );

			// Wait until the bucket is not accessible.
			$client->waitUntil( 'BucketNotExists', array( 'Bucket' => $this->bucket ) );
		} catch ( \Exception $e ) {
			\PHPUnit_Framework_Assert::fail( $e->getMessage() );
		}
	}

	/**
	 * Get the region of the bucket
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 *
	 * @return mixed
	 */
	public function getBucketLocation() {
		try {
			$location = $this->getClient()->getBucketLocation( array( 'Bucket' => $this->bucket ) );

			return $location->get( 'LocationConstraint' );
		} catch ( \Exception $e ) {
			\PHPUnit_Framework_Assert::fail( $e->getMessage() );
		}
	}

	/**
	 * Assert a bucket has the correct region
	 *
	 * @param string $location
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 */
	public function seeBucketLocation( $location ) {
		$this->assertEquals( $location, $this->getBucketLocation() );
	}

	/**
	 * Checks if a file exists
	 *
	 * @param string $key
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 *
	 * @return bool
	 */
	public function doesFileExist( $key ) {
		try {
			return $this->getClient()->doesObjectExist( $this->bucket, $key );
		} catch ( \Exception $e ) {
			\PHPUnit_Framework_Assert::fail( $e->getMessage() );
		}
	}

	/**
	 * Asserts if a file exists
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 *
	 * @param string $key
	 */
	public function seeFile( $key ) {
		$this->assertTrue( $this->doesFileExist( $key ) );
	}

	/**
	 * Delete a single file from the current bucket.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function deleteBucketFile( $key ) {
		try {
			return $this->getClient()->deleteObject( array( 'Bucket' => $this->bucket, 'Key' => $key ) );
		} catch ( \Exception $e ) {
			\PHPUnit_Framework_Assert::fail( $e->getMessage() );
		}
	}
}
