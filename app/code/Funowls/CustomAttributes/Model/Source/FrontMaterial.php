<?php
namespace Funowls\CustomAttributes\Model\Source;

class FrontMaterial extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
            ['label' => __('Carbon Fibre'), 'value'=>'Carbon Fibre'],
            ['label' => __('Acetate'), 'value'=>'Acetate'],
            ['label' => __('Cellulose P.Inj'), 'value'=>'Cellulose P.Inj'],
            ['label' => __('Grilamid'), 'value'=>'Grilamid'],
            ['label' => __('Poly Inj Metal'), 'value'=>'Poly Inj Metal'],
            ['label' => __('Nylon'), 'value'=>'Nylon'],
            ['label' => __('Grilamid Metal'), 'value'=>'Grilamid Metal'],
            ['label' => __('Metal'), 'value'=>'Metal'],
            ['label' => __('Polyamide Inj'), 'value'=>'Polyamide Inj'],
            ['label' => __('Steel'), 'value'=>'Steel'],
            ['label' => __('Policarbonate'), 'value'=>'Policarbonate'],
            ['label' => __('Plastic'), 'value'=>'Plastic'],
            ['label' => __('Titanium'), 'value'=>'Titanium'],
            ['label' => __('Acetate Metal'), 'value'=>'Acetate Metal'],
            ['label' => __('Optyl'), 'value'=>'Optyl'],
        ];
 
        return $this->_options;
    }
}
?>