<?php
namespace Funowls\CustomAttributes\Model\Source;

class EyeShape extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
            ['label' => __('Navigator'), 'value'=>'Navigator'],
            ['label' => __('Cat Eye'), 'value'=>'Cat Eye'],
            ['label' => __('Butterfly'), 'value'=>'Butterfly'],
            ['label' => __('Multishape'), 'value'=>'Multishape'],
            ['label' => __('Oval Modified'), 'value'=>'Oval Modified'],
            ['label' => __('Special Shape'), 'value'=>'Special Shape'],
            ['label' => __('Square'), 'value'=>'Square'],
            ['label' => __('Round'), 'value'=>'Round'],
            ['label' => __('Rectangular'), 'value'=>'Rectangular'],
            ['label' => __('Pillow'), 'value'=>'Pillow'],
            ['label' => __('Pilot'), 'value'=>'Pilot'],
            ['label' => __('Aviator'), 'value'=>'Aviator'],
            ['label' => __('Browline'), 'value'=>'Browline'],
            ['label' => __('Tea Cup'), 'value'=>'Tea Cup'],
            ['label' => __('Rectangle'), 'value'=>'Rectangle'],
            ['label' => __('Rectangular / Square'), 'value'=>'Rectangular / Square']
        ];
 
        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
?>