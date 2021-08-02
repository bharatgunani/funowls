<?php
namespace Funowls\CustomAttributes\Model\Source;

class LensMaterial extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
            ['label' => __('Policarbonate'), 'value'=>'Policarbonate'],
            ['label' => __('Crystal'), 'value'=>'Crystal'],
            ['label' => __('Policarbonate Polarized'), 'value'=>'Policarbonate Polarized'],
            ['label' => __('Nylon Lens'), 'value'=>'Nylon Lens'],
            ['label' => __('CR39 Organic'), 'value'=>'CR39 Organic'],
            ['label' => __('Nylon'), 'value'=>'Nylon'],
            ['label' => __('NXT Lens'), 'value'=>'NXT Lens'],
            ['label' => __('Polycarbon Polarized'), 'value'=>'Polycarbon Polarized'],
            ['label' => __('Plastic'), 'value'=>'Plastic'],
            ['label' => __('Triacetate Polarized Lens'), 'value'=>'Triacetate Polarized Lens'],
            ['label' => __('Policarbonate Lens'), 'value'=>'Policarbonate Lens'],
            ['label' => __('Polycarbonate'), 'value'=>'Polycarbonate'],
            ['label' => __('Triacetate Polarized'), 'value'=>'Triacetate Polarized'],
        ];
 
        return $this->_options;
    }
}
?>