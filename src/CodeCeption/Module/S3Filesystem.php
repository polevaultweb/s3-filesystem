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
	protected $client;

	/**
	 * Initialize the S3 client
	 */
	public function _initialize() {
		parent::_initialize();

		$args = array(
			'version'     => isset( $this->config['version'] ) ? $this->config['version'] : 'latest',
			'region'      => isset( $this->config['region'] ) ? $this->config['region'] : 'us-east-1',
			'signature'   => isset( $this->config['signature'] ) ? $this->config['signature'] : 'v4',
			'credentials' => array(
				'key'    => $this->config['accessKey'],
				'secret' => $this->config['accessSecret'],
			),
		);

		$this->client = new S3Client( $args );
	}

	/**
	 * Delete a Bucket
	 *
	 * @param string $bucket
	 */
	public function deleteBucket( $bucket ) {
		$this->client->deleteBucket( array( 'Bucket' => $bucket ) );
	}
}