<?php
namespace Codeception\Module;

use Aws\S3\S3Client;

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
	 * @va
	 */
	protected static $clients;

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
	 * @param string $bucket
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 *
	 * @return bool
	 */
	public function doesBucketExist( $bucket ) {
		try {
			return $this->getClient()->doesBucketExist( $bucket );
		} catch ( \Exception $e ) {
			\PHPUnit_Framework_Assert::fail( $e->getMessage() );
		}
	}

	/**
	 * Asserts if a bucket exists
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 *
	 * @param string $bucket
	 */
	public function seeBucket( $bucket ) {
		$this->assertTrue( $this->doesBucketExist( $bucket ) );
	}

	/**
	 * Delete a bucket
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 *
	 * @param string $bucket
	 */
	public function deleteBucket( $bucket ) {
		try {
			$this->getClient()->deleteBucket( array( 'Bucket' => $bucket ) );
		} catch ( \Exception $e ) {
			\PHPUnit_Framework_Assert::fail( $e->getMessage() );
		}
	}

	/**
	 * Get the region of the bucket
	 *
	 * @param $bucket
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 *
	 * @return mixed
	 */
	public function getBucketLocation( $bucket ) {
		try {
			$location = $this->getClient()->getBucketLocation( array( 'Bucket' => $bucket ) );
		} catch ( \Exception $e ) {
			\PHPUnit_Framework_Assert::fail( $e->getMessage() );
		}

		return $location->get( 'LocationConstraint' );
	}

	/**
	 * Assert a bucket has the correct region
	 *
	 * @param string $location
	 * @param string $bucket
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 */
	public function seeBucketLocation( $location, $bucket ) {
		$this->assertEquals( $location, $this->getBucketLocation( $bucket ) );
	}

	/**
	 * Checks if a file exists
	 *
	 * @param string $bucket
	 * @param string $key
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 *
	 * @return bool
	 */
	public function doesFileExist( $bucket, $key ) {
		try {
			return $this->getClient()->doesObjectExist( $bucket, $key );
		} catch ( \Exception $e ) {
			\PHPUnit_Framework_Assert::fail( $e->getMessage() );
		}
	}

	/**
	 * Asserts if a file exists
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 *
	 * @param string $bucket
	 * @param string $key
	 */
	public function seeFile( $bucket, $key ) {
		$this->assertTrue( $this->doesFileExist( $bucket, $key ) );
	}
}