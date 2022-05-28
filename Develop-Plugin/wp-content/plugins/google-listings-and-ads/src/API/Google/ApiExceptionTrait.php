<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\GoogleListingsAndAds\API\Google;

use Automattic\WooCommerce\GoogleListingsAndAds\Vendor\GuzzleHttp\Exception\BadResponseException;
use Google\ApiCore\ApiException;
use Google\Rpc\Code;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Trait ApiExceptionTrait
 *
 * @package Automattic\WooCommerce\GoogleListingsAndAds\API\Google
 */
trait ApiExceptionTrait {

	/**
	 * Check if the ApiException contains a specific error.
	 *
	 * @param ApiException $exception  Exception to check.
	 * @param string       $error_code Error code we are checking.
	 *
	 * @return bool
	 */
	protected function has_api_exception_error( ApiException $exception, string $error_code ): bool {
		$meta = $exception->getMetadata();
		if ( empty( $meta ) || ! is_array( $meta ) ) {
			return false;
		}

		foreach ( $meta as $data ) {
			if ( empty( $data['errors'] ) || ! is_array( $data['errors'] ) ) {
				continue;
			}

			foreach ( $data['errors'] as $error ) {
				if ( in_array( $error_code, $error['errorCode'], true ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns a list of detailed errors from an ApiException.
	 * If no errors are found the default Exception message is returned.
	 *
	 * @param ApiException $exception Exception to check.
	 *
	 * @return array
	 */
	protected function get_api_exception_errors( ApiException $exception ): array {
		$errors = [];
		$meta   = $exception->getMetadata();

		if ( is_array( $meta ) ) {
			foreach ( $meta as $data ) {
				if ( empty( $data['errors'] ) || ! is_array( $data['errors'] ) ) {
					continue;
				}

				foreach ( $data['errors'] as $error ) {
					if ( empty( $error['message'] ) ) {
						continue;
					}

					if ( ! empty( $error['errorCode'] ) && is_array( $error['errorCode'] ) ) {
						$error_code = reset( $error['errorCode'] );
					} else {
						$error_code = 'ERROR';
					}

					$errors[ $error_code ] = $error['message'];
				}
			}
		}

		$errors[ $exception->getStatus() ] = $exception->getBasicMessage();
		return $errors;
	}

	/**
	 * Get an error message from a ClientException.
	 *
	 * @param ClientExceptionInterface $exception Exception to check.
	 * @param string                   $default   Default error message.
	 *
	 * @return string
	 */
	protected function client_exception_message( ClientExceptionInterface $exception, string $default ): string {
		if ( $exception instanceof BadResponseException ) {
			$response = json_decode( $exception->getResponse()->getBody()->getContents(), true );
			$message  = $response['message'] ?? false;
			return $message ? $default . ': ' . $message : $default;
		}
		return $default;
	}

	/**
	 * Map a gRPC code to HTTP status code.
	 *
	 * @param ApiException $exception Exception to check.
	 *
	 * @return int The HTTP status code.
	 *
	 * @see Google\Rpc\Code for the list of gRPC codes.
	 */
	protected function map_grpc_code_to_http_status_code( ApiException $exception ) {
		switch ( $exception->getCode() ) {
			case Code::OK:
				return 200;

			case Code::CANCELLED:
				return 499;

			case Code::UNKNOWN:
				return 500;

			case Code::INVALID_ARGUMENT:
				return 400;

			case Code::DEADLINE_EXCEEDED:
				return 504;

			case Code::NOT_FOUND:
				return 404;

			case Code::ALREADY_EXISTS:
				return 409;

			case Code::PERMISSION_DENIED:
				return 403;

			case Code::UNAUTHENTICATED:
				return 401;

			case Code::RESOURCE_EXHAUSTED:
				return 429;

			case Code::FAILED_PRECONDITION:
				return 400;

			case Code::ABORTED:
				return 409;

			case Code::OUT_OF_RANGE:
				return 400;

			case Code::UNIMPLEMENTED:
				return 501;

			case Code::INTERNAL:
				return 500;

			case Code::UNAVAILABLE:
				return 503;

			case Code::DATA_LOSS:
				return 500;

			default:
				return 500;
		}
	}
}
