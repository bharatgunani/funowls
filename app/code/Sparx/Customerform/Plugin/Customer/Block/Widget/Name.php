<?php
namespace Sparx\Customerform\Plugin\Customer\Block\Widget;

class Name
{
    public function after_construct(\Magento\Customer\Block\Widget\Name $result)
    {

        $result->setTemplate('Sparx_Customerform::widget/name.phtml');
        return $result;
    }
}
?>