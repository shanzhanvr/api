<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/7/1
 * Time: 14:39
 */
namespace library\Service\Speech\lib;
class AipSignOption {
    const EXPIRATION_IN_SECONDS = 'expirationInSeconds';

    const HEADERS_TO_SIGN = 'headersToSign';

    const TIMESTAMP = 'timestamp';

    const DEFAULT_EXPIRATION_IN_SECONDS = 1800;

    const MIN_EXPIRATION_IN_SECONDS = 300;

    const MAX_EXPIRATION_IN_SECONDS = 129600;
}
