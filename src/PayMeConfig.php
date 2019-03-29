<?php

namespace Kaoz70\PayMe;


class PayMeConfig
{
    private $env = 'dev';
    private $walletKey;
    private $vpos2Key;
    private $currencyCode = '840';
    private $commerceId;
    private $acquirerId;
    private $walletId;

    private $config = [
        'prod' => [
            'wallet_url' => 'https://www.pay-me.pe/WALLETWS/services/WalletCommerce?wsdl',
            'vpos2_js' => 'https://vpayment.verifika.com/VPOS2/js/modalcomercio.js',
        ],

        'dev' => [
            'wallet_url' => 'https://integracion.alignetsac.com/WALLETWS/services/WalletCommerce?wsdl',
            'vpos2_js' => 'https://integracion.alignetsac.com/VPOS2/js/modalcomercio.js',
        ],
    ];

    public function __construct($env)
    {
        $this->setEnv($env);
    }

    /**
     * @param mixed $acquirerId
     */
    public function setAcquirerId($acquirerId)
    {
        $this->acquirerId = $acquirerId;
    }

    /**
     * @param mixed $commerceId
     */
    public function setCommerceId($commerceId)
    {
        $this->commerceId = $commerceId;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @param string $env
     */
    public function setEnv($env)
    {
        $this->env = $env;
    }

    /**
     * @param mixed $vpos2Key
     */
    public function setVpos2Key($vpos2Key)
    {
        $this->vpos2Key = $vpos2Key;
    }

    /**
     * @param mixed $walletId
     */
    public function setWalletId($walletId)
    {
        $this->walletId = $walletId;
    }

    /**
     * @param mixed $walletKey
     */
    public function setWalletKey($walletKey)
    {
        $this->walletKey = $walletKey;
    }

    /**
     * @return mixed
     */
    public function getAcquirerId()
    {
        return $this->acquirerId;
    }

    /**
     * @return mixed
     */
    public function getCommerceId()
    {
        return $this->commerceId;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @return mixed
     */
    public function getVpos2Js()
    {
        return $this->config[$this->env]['vpos2_js'];
    }

    /**
     * @return mixed
     */
    public function getVpos2Key()
    {
        return $this->vpos2Key;
    }

    /**
     * @return mixed
     */
    public function getWalletId()
    {
        return $this->walletId;
    }

    /**
     * @return mixed
     */
    public function getWalletKey()
    {
        return $this->walletKey;
    }

    /**
     * @return mixed
     */
    public function getWalletUrl()
    {
        return $this->config[$this->env]['wallet_url'];
    }
}
