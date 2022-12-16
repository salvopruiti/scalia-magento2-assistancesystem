<?php

namespace ScaliaGroup\AssistanceSystem\Block;

use Magento\Framework\View\Element\Template;

class Form extends Template
{
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
        return [
            'Piquadro',
            'Pollini',
            'The Bridge',
            'Coccinelle',
            'Travelite',
            'Titan'
        ];
    }

    public function getTypes()
    {
        return [
            1 => 'Bagaglio DAT danneggiato',
            2 => 'Prodotto difettoso in garanzia',
            3 => 'Riparazione a pagamento'
        ];
    }
}