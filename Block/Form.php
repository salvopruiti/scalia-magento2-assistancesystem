<?php

namespace ScaliaGroup\AssistanceSystem\Block;

use Magento\Framework\View\Element\Template;
use ScaliaGroup\Integration\Helper\Config;
use Laminas\Http\Client;

class Form extends Template
{
    protected Config $config;

    public function __construct(
        Template\Context $context,
        Config $config,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->config = $config;
    }


    public function getFormAction()
    {
        return $this->getUrl('assistenza', ['_secure' => true]);
    }

    public function getMessage()
    {
        return $this->_scopeConfig->getValue("sg_assistance_system/general/message");
    }

    public function getTitle()
    {
        return $this->_scopeConfig->getValue("sg_assistance_system/general/title");
    }

    public function getPrivacyCheckBoxMessage()
    {
        return $this->_scopeConfig->getValue("sg_assistance_system/general/privacy");
    }

    public function getBrands()
    {
        try {
            $host = $this->config->getMiddlewareHost();
            $access_token = $this->config->getMiddlewareAccessToken();

            $client = new Client($host . "/api/v1/returns/brands");

            $client->setHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token
            ]);

            $client->setMethod("get");
            $response = $client->send();

            if($response->getStatusCode() != 200)
                throw new \Exception($response->getStatusCode() . ' ' . $response->getReasonPhrase());

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {

            return [

            ];

        }


    }

    public function getTypes()
    {
        try {
            $host = $this->config->getMiddlewareHost();
            $access_token = $this->config->getMiddlewareAccessToken();

            $client = new Client($host . "/api/v1/returns/types");

            $client->setHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token
            ]);

            $client->setMethod("get");
            $response = $client->send();

            if($response->getStatusCode() != 200)
                throw new \Exception($response->getStatusCode() . ' ' . $response->getReasonPhrase());

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {

            return [
                1 => 'Bagaglio DAT danneggiato',
                2 => 'Prodotto difettoso in garanzia',
                3 => 'Riparazione a pagamento'
            ];

        }
    }
}