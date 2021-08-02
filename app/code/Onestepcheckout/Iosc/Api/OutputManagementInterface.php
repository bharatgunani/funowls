<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */

namespace Onestepcheckout\Iosc\Api;

interface OutputManagementInterface
{

    /**
     * provide json input for processing
     *
     * @param $input
     * @return boolean
     */
    public function processPayload($input);

    /**
     * provide key name to output array
     *
     * @return string
     */
    public function getOutputKey();
}
