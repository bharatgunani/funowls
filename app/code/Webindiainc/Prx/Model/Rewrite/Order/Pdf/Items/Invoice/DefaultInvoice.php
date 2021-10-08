<?php
namespace Webindiainc\Prx\Model\Rewrite\Order\Pdf\Items\Invoice;
class DefaultInvoice extends \Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice
{
	public function draw()
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = [];

        // draw Product name
        $lines[0] = [
            [
                'text' => $this->string->split(html_entity_decode($item->getName()), 35, true, true),
                'feed' => 35,
				'font' => 'bold'
            ]
        ];

        // draw SKU
        $lines[0][] = [
            'text' => $this->string->split(html_entity_decode($this->getSku($item)), 17),
            'feed' => 270,
            'align' => 'left',
        ];

        // draw QTY
        $lines[0][] = ['text' => $item->getQty() * 1, 'feed' => 430, 'align' => 'right'];

        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 373;
        $feedPriceNew = 380;
        $feedSubtotal = $feedPrice + 190;
        foreach ($prices as $priceData) {
            if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedPrice, 'align' => 'left'];
                // draw Subtotal label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedSubtotal, 'align' => 'right'];
                $i++;
            }
            // draw Price
            $lines[$i][] = [
                'text' => $priceData['price'],
                'feed' => $feedPriceNew,
                'font' => 'bold',
                'align' => 'right',
            ];
            // draw Subtotal
            $lines[$i][] = [
                'text' => $priceData['subtotal'],
                'feed' => $feedSubtotal,
                'font' => 'bold',
                'align' => 'right',
            ];
            $i++;
        }

        // draw Tax
        $lines[0][] = [
            'text' => $order->formatPriceTxt($item->getTaxAmount()),
            'feed' => 500,
            'font' => 'bold',
            'align' => 'right',
        ];

        // custom options
        $options = $this->getItemOptions();
        /* echo '<pre>'; print_r($options); die; */
        if ($options) {
			$k = 1;
            foreach ($options as $key => $option) {
                // draw options label
                
				$notDisplayOption = array('frameprice', 'lensprice', 'lenstotalprice', 'lensusage', 'lenstype', 'lensthickness');
				if(in_array($key, $notDisplayOption)) {
					continue;
				}

                // Checking whether option value is not null
                if ($option['value'] !== null) {
                    if (isset($option['print_value'])) {
                        $printValue = $option['print_value'];
                    } else {
                        $printValue = $this->filterManager->stripTags($option['value']);
                    }
                    $values = explode(', ', $printValue);
					
                    foreach ($values as $value) {
                        $lines[$k][0] = ['text' => $this->string->split($option['label']. ": ", 50, true, true), 'feed' => 35];
                        $lines[$k][1] = ['text' => $this->string->split($value, 50, true, true), 'font' => 'bold', 'feed' => 100];
						$k++;
                    }
                }
            }
        }

        $lineBlock = ['lines' => $lines, 'height' => 20];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }        		
}