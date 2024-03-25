<?php

declare(strict_types=1);

namespace App\Application\HttpClient\Guzzle;

use App\Application\HttpClient\Exception\HttpClientException;
use App\Application\HttpClient\RequestOption\AbstractRequestOption;
use App\Application\HttpClient\RequestOption\RequestOptionBaseAuthorization;
use App\Application\HttpClient\RequestOption\RequestOptionBearerTokenHeader;
use App\Application\HttpClient\RequestOption\RequestOptionBody;
use App\Application\HttpClient\RequestOption\RequestOptionCertificate;
use App\Application\HttpClient\RequestOption\RequestOptionFormParams;
use App\Application\HttpClient\RequestOption\RequestOptionHeaders;
use App\Application\HttpClient\RequestOption\RequestOptionJSON;
use App\Application\HttpClient\RequestOption\RequestOptionMultipartFormParams;
use App\Application\HttpClient\RequestOption\RequestOptionQuery;
use App\Application\HttpClient\RequestOption\RequestOptionSSLKey;
use App\Application\HttpClient\RequestOption\RequestOptionSSLVerification;
use App\Application\HttpClient\RequestOption\RequestOptionTimeout;
use GuzzleHttp\RequestOptions;
/**
 * @internal
 */
class RequestOptionsTranslator
{
    private const OPTIONS_MAPPING = [
        RequestOptionBody::class => RequestOptions::BODY,
        RequestOptionFormParams::class => RequestOptions::FORM_PARAMS,
        RequestOptionHeaders::class => RequestOptions::HEADERS,
        RequestOptionBaseAuthorization::class => RequestOptions::AUTH,
        RequestOptionJSON::class => RequestOptions::JSON,
        RequestOptionQuery::class => RequestOptions::QUERY,
        RequestOptionTimeout::class => RequestOptions::TIMEOUT,
        RequestOptionCertificate::class => RequestOptions::CERT,
        RequestOptionSSLKey::class => RequestOptions::SSL_KEY,
        RequestOptionSSLVerification::class => RequestOptions::VERIFY,
        RequestOptionMultipartFormParams::class => RequestOptions::MULTIPART,
        RequestOptionBearerTokenHeader::class => RequestOptions::HEADERS,
    ];

    /**
     * @param AbstractRequestOption ...$options
     *
     * @return array<string, mixed>
     */
    public function translate(AbstractRequestOption ...$options): array
    {
        $requestOptions = [];

        foreach ($options as $option) {
            $optionClass = get_class($option);

            if (!isset(self::OPTIONS_MAPPING[$optionClass])) {
                throw new HttpClientException("Invalid request option: $optionClass");
            }
            $requestOptions[self::OPTIONS_MAPPING[$optionClass]] = $option->getValue();
        }

        return $requestOptions;
    }
}
