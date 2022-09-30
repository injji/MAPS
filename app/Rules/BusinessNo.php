<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use GuzzleHttp\Client;

class BusinessNo implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$value) {
            return true;
        }
        try {
            $value = preg_replace('/-/', '', $value);
            $response = (new Client)->request('post', 'https://teht.hometax.go.kr/wqAction.do?actionId=ATTABZAA001R08', [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=UTF8',
                ],
                'body' => '<map><inqrTrgtClCd>1</inqrTrgtClCd><txprDscmNo>'.$value.'</txprDscmNo><map id="userReqInfoVO"/></map><nts<nts>nts>'
            ]);

            $response = simplexml_load_string((string) $response->getBody());
            return ((string) $response->nrgtTxprYn) == 'N';
        } catch (\Exception $e) {
            return true;
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.business_no');
    }
}
