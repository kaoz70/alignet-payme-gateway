<?php

namespace Kaoz70\PayMe;



use Kaoz70\PayMe\Exceptions\PaymentStatusException;
use Kaoz70\PayMe\Types\PaymentStatusTypes;

class PayMe
{

    /** @var PayMeConfig */
    private $config;

    public function __construct(PayMeConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param $id - Commerce user's unique identifier
     * @param $email - User's email
     * @param $firstNames - User's first names
     * @param $lastNames - User's last name
     * @return mixed
     * @throws \SoapFault | \Exception
     */
    public function getWalletCode($id, $email, $firstNames, $lastNames)
    {
        $mail = $this->config->getEnv() === 'prod' ? $email : 'test-' . $email;
        $registerVerification = openssl_digest(
            $this->config->getWalletId().$id.$mail.$this->config->getWalletKey(),
            'sha512'
        );

        $client = new \SoapClient($this->config->getWalletUrl());

        $data = [
            'idEntCommerce' => $this->config->getWalletId(),
            'codCardHolderCommerce' => $id,
            'names' => $firstNames,
            'lastNames' => $lastNames,
            'mail' => $mail,
            'registerVerification' => $registerVerification
        ];

        $result = $client->RegisterCardHolder($data);

        if ($result->ansCode == '000') {
            return $result->codAsoCardHolderWallet;
        } else {
            throw new \Exception($result->ansDescription, $result->ansCode);
        }
    }

    /**
     * Valor total de la compra, dado por el Comercio. el monto debe ir sin separador decimal
     * (Si el monto es 100.30 dólares entonces la cantidad a enviar es 10030)
     *
     * @param $price
     * @return int
     */
    public function formatPrice($price)
    {
        //Only 2 decimal places
        $format = number_format($price, 2, '.', '');
        //Remove the dot
        $noDot = str_replace('.', '', $format);
        //Remove any leading zeroes
        return (int) ltrim($noDot, '0');
    }

    /**
     * Identificador único por cada transacción, generado por el comercio.
     * Considerar que deberán ser enviados los 9 caracteres obligatoriamente.
     *
     * @param $number
     * @return string
     */
    public function formatOrderNumber($number)
    {
        return str_pad($number, 9, '0', STR_PAD_LEFT);
    }

    public function createPurchaseVerification($order, $price)
    {
        return openssl_digest(
            $this->config->getAcquirerId().$this->config->getCommerceId().$order.$price.$this->config->getCurrencyCode().$this->config->getVpos2Key(),
            'sha512'
        );
    }

    /**
     * Verify that the data sent has not ben tampered with
     *
     * @param array $post
     * @return bool
     */
    private function verifyTransaction(array $post)
    {
        //purchaseVerication que devuelve la Pasarela de Pagos
        $purchaseVericationVPOS2 = $post['purchaseVerification'];

        //purchaseVerication que genera el comercio
        $purchaseVericationComercio = openssl_digest(
            $post['acquirerId'] . $post['idCommerce'] . $post['purchaseOperationNumber'] . $post['purchaseAmount'] . $post['purchaseCurrencyCode'] . $post['authorizationResult'] . $this->config->getVpos2Key(),
            'sha512'
        );

        if ($purchaseVericationVPOS2 !== $purchaseVericationComercio || $purchaseVericationVPOS2 == '') {
            return false;
        }

        return true;
    }

    /**
     * @param array $request - Alignet's POST data
     * @return bool
     * @throws PaymentStatusException
     */
    public function verifyFromCallback(array $request)
    {
        /**
         * 00 - Operación Autorizada
         * 01 - Operación Denegada
         * 05 - Operación Rechazada
         */
        switch ($request['authorizationResult']) {
            case '00':
                if (!$this->verifyTransaction($request)) {
                    throw new PaymentStatusException('Invalid transaction', PaymentStatusTypes::$INVALID_VERIFICATION);
                }
                break;
            case '01':
                throw new PaymentStatusException('Payment was denied by the bank', PaymentStatusTypes::$DENIED);
                break;
            case '05':
                // User cancelled
                if($request['errorCode'] === '2300') {
                    throw new PaymentStatusException('User cancelled the payment', PaymentStatusTypes::$CANCELED);
                }
                // Bank rejected
                else {
                    throw new PaymentStatusException('Payment was rejected by the bank', PaymentStatusTypes::$REJECTED);
                }
                break;
        }

        return true;
    }

}
