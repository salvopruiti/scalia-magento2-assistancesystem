<?php

namespace ScaliaGroup\AssistanceSystem\ViewModel;

use Magento\Contact\Helper\Data;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class UserDataProvider implements ArgumentInterface
{

    private DataPersistorInterface $dataPersistor;
    private ?array $postData = null;
    /**
     * UserDataProvider constructor.
     * @param Data $helper
     */
    public function __construct(
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
    }

    public function getValue($key = null)
    {
        if (null === $this->postData) {
            $this->postData = (array) $this->dataPersistor->get('sg_assistance_form');
            $this->dataPersistor->clear('sg_assistance_form');
        }

        if(!$key)
            return $this->postData;

        if (isset($this->postData[$key])) {
            return $this->postData[$key];
        }
    }
}
