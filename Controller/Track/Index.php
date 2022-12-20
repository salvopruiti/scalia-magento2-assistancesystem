<?php

namespace ScaliaGroup\AssistanceSystem\Controller\Track;

use Laminas\Http\Client;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Math\Random;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Result\PageFactory;
use ScaliaGroup\AssistanceSystem\Block\Track;
use ScaliaGroup\Integration\Logger\Logger;

class Index implements HttpGetActionInterface
{

    private PageFactory $pageFactory;
    private \Magento\Framework\App\Request\Http $_request;
    private Context $context;
    private UploaderFactory $uploaderFactory;
    private ScopeConfigInterface $scopeConfig;
    private ForwardFactory $forwardFactory;
    private Filesystem $filesystem;
    private Logger $logger;
    private \ScaliaGroup\Integration\Helper\Config $config;
    private Random $rand;
    private \Magento\Framework\Message\ManagerInterface $messageManager;
    private DataPersistorInterface $dataPersistor;


    public function __construct(
        Context $context, PageFactory $pageFactory,
        UploaderFactory $uploaderFactory,
        ScopeConfigInterface $scopeConfig,
        ForwardFactory $forwardFactory,
        Filesystem $filesystem,
        Logger $logger,
        \ScaliaGroup\Integration\Helper\Config $config,
        Random $rand,
        DataPersistorInterface $dataPersistor
    )
    {
        $this->pageFactory = $pageFactory;
        $this->_request = $context->getRequest();
        $this->context = $context;
        $this->uploaderFactory = $uploaderFactory;
        $this->scopeConfig = $scopeConfig;
        $this->forwardFactory = $forwardFactory;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->config = $config;
        $this->rand = $rand;
        $this->messageManager = $this->context->getMessageManager();
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        if(!$this->scopeConfig->isSetFlag('sg_assistance_system/general/enabled')){
            $forward = $this->forwardFactory->create();
            return $forward->forward('defaultNoRoute');
        }

        $returnId = $this->_request->getParam('return_id');
        $resultPage = $this->pageFactory->create();
        $block = $resultPage->getLayout()->addBlock(\Magento\Framework\View\Element\Template::class, 'trackForm', 'content');

        if(!$returnId) {

            $block->setTemplate('ScaliaGroup_AssistanceSystem::track/form.phtml');
            return $resultPage;

        } else {

            try {
                $host = $this->config->getMiddlewareHost();
                $access_token = $this->config->getMiddlewareAccessToken();

                $client = new Client($host . "/api/v1/returns/" . $returnId);

                $response = $client->setHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $access_token
                ])
                    ->setMethod('GET')
                    ->send();

                if($response->getStatusCode() != 200 && $this->config->getIsDebugMode()) {

                    $this->logger->debug("AssistanceSystemForm", [
                        'url' => $client->getUri()->toString(),
                        'status' => $response->getStatusCode(),
                        'status_txt' => $response->getReasonPhrase(),
                        'body' => $body = json_decode($response->getBody(), true),
                    ]);

                    if(!isset($body['message'])) $body['message'] = __('Server Error');

                    throw new LocalizedException(new Phrase($body['message'] ?: __('Server Error')));

                }

                $block->setTemplate('ScaliaGroup_AssistanceSystem::track/track.phtml');
                $block->setData('returnHTML', $response->getBody());

                return $resultPage;

            } catch (LocalizedException $e) {

                $this->messageManager->addErrorMessage($e->getMessage());

            } catch (\Exception $e) {

                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(
                    __('An error occurred while processing your form. Please try again later.') .' ('. $e->getMessage() . ')'
                );
            }


        }

        return $this->context->getResultRedirectFactory()->create()->setPath('assistenza/track');


    }

    public function getRequest()
    {
        return $this->_request;
    }

    public function getResponse()
    {
        return $this->context->getResponse();
    }
}