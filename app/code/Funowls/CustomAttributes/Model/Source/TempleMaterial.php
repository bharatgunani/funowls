<?php
namespace Funowls\CustomAttributes\Model\Source;

class TempleMaterial extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
    * Get all options
    *
    * @return array
    */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __(''), 'value'=>''],
            ['label' => __('Long Tip Rubber'), 'value'=>'Long Tip Rubber'],
            ['label' => __('Metal'), 'value'=>'Metal'],
            ['label' => __('Acetate'), 'value'=>'Acetate'],
            ['label' => __('Cellulose P.Inj'), 'value'=>'Cellulose P.Inj'],
            ['label' => __('Grilamid'), 'value'=>'Grilamid'],
            ['label' => __('Rubber'), 'value'=>'Rubber'],
            ['label' => __('Carbon'), 'value'=>'Carbon'],
            ['label' => __('Polyamide Inj'), 'value'=>'Polyamide Inj'],
            ['label' => __('Steel'), 'value'=>'Steel'],
            ['label' => __('Plastic'), 'value'=>'Plastic'],
            ['label' => __('Policarbonate'), 'value'=>'Policarbonate'],
            ['label' => __('Acetate Metal'), 'value'=>'Acetate Metal'],
            ['label' => __('Optyl'), 'value'=>'Optyl'],
            ['label' => __('Long Tip Acetate'), 'value'=>'Long Tip Acetate'],
            ['label' => __('Copolyamide'), 'value'=>'Copolyamide'],
        ];
 
        return $this->_options;
    }
}
?>