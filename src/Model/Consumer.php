<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Model;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\MessageQueue\ConnectionLostException;
use Mediact\Webhook\Model\CustomFilters;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Tracedock\TransactionTracking\Api\ConfigInterface;

class Consumer
{
    private ?ClientInterface $client = null;

    private ConfigInterface $config;

    private LoggerInterface $logger;

    public function __construct(
        ConfigInterface $config,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Be wary of what exceptions can and should be fired based on
     * magento/framework-message-queue/Consumer.php:getTransactionCallback
     *
     * The following exceptions are available:
     *
     * MessageLockException:
     * Will acknowledge and ignore the message.
     *
     * ConnectionLostException:
     * Will remove the lock of the message.
     *
     * NotFoundException:
     * Will acknowledge and ignore the message and log the warning.
     *
     * Exception:
     * Will reject and remove the lock of the message and will retry later
     * again. The max number of retries is by default 3.
     *
     * Also the following statuses might be set:
     * MESSAGE_STATUS_NEW = 2;
     * MESSAGE_STATUS_IN_PROGRESS = 3;
     * MESSAGE_STATUS_COMPLETE= 4;
     * MESSAGE_STATUS_RETRY_REQUIRED = 5;
     * MESSAGE_STATUS_ERROR = 6;
     * MESSAGE_STATUS_TO_BE_DELETED = 7;
     *
     * @return void
     *
     * @throws Exception|ConnectionLostException
     */
    public function process(string $json): void
    {
        $url = parse_url($this->config->getApiUrl());
        if (empty($url)) {
            throw new ConnectionLostException(
                'Invalid payload url for TraceDock consumer: ' . $url
            );
        }

        $path = $url['path'];
        if (array_key_exists('fragment', $url)) {
            $path .= '#' . $url['fragment'];
        }

        $this->doRequest(
            $url['scheme'] . '://' . $url['host'],
            $path,
            $json
        );
    }

    private function doRequest(
        string $baseUrl,
        string $path,
        string $json
    ): ResponseInterface {
        try {
            $response = $this->getClient($baseUrl)->request(
                'POST',
                $path,
                ['body' => $json]
            );

            if ($response->getStatusCode() >= 300) {
                $this->logger->critical(
                    'Invalid Status code received from TraceDock: ' . (string)$response->getStatusCode()
                );
                throw new ConnectionLostException($response->getBody());
            }

            return $response;
        } catch (GuzzleException $e) {
            $this->logger->critical($e->getMessage());
            throw new ConnectionLostException($e->getMessage());
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
            throw $e;
        }
    }

    private function getClient(string $baseUri): ClientInterface
    {
        if ($this->client === null) {
            $this->client = new Client(
                [
                    'base_uri' => $baseUri,
                    'connect_timeout' => 30,
                    'timeout' => 5
                ]
            );
        }

        return $this->client;
    }
}
