# alignet-payme-gateway
Alignet (PayMe) PHP classes

## PayMe class methods

* getWalletCode($id, $email, $firstNames, $lastNames)
* formatPrice($price)
* createPurchaseVerification($orderNumber, $price)
* verifyFromCallback($postData)

## Usage

### Installation

    composer require kaoz70/alignet-payme-gateway

### Init

    // Set the configuration class
    $payMeConfig = new PayMeConfig('dev'); // or 'prod'
    $payMeConfig->setWalletKey('key...');
    $payMeConfig->setVpos2Key('key...');
    $payMeConfig->setCommerceId('12345');
    $payMeConfig->setAcquirerId('123');
    $payMeConfig->setWalletId('123');

    // Init the PayMe class 
    $payme = new PayMe($payMeConfig);
    
### Get the users's wallet code

    try {
        $walletCode = $payme->getWalletCode(2458, 'example@email.com', 'Juan Jose', 'Perez Romero')
        // Store the wallet code in DB...
    } catch (\SoapFault $e) {
        // Handle SOAP error
    } catch () {
        // Handle generic error
    }
    
    
### Generate the purchase verification

    $price = $payme->formatPrice($total); // All prices sent to PayMe should be formatted with this method
    $orderNumber = 12364 // Generate a unique order number every time
    $purchaseVerification = $payme->createPurchaseVerification($orderNumber, $price)
    
    
### Alignet callback URL

    try {
        // Set the configuration with the same data as before
        $payMeConfig = new PayMeConfig('dev'); // or 'prod'
        $payMeConfig->setWalletKey('key...');
        $payMeConfig->setVpos2Key('key...');
        $payMeConfig->setCommerceId('12345');
        $payMeConfig->setAcquirerId('123');
        $payMeConfig->setWalletId('123');
    
        // Init the PayMe class 
        $payme = new PayMe($payMeConfig);
        
        $data = $_POST;
        $payme->verifyFromCallback($data);

        // Success
    } catch (PaymentStatusException $exception) {
        // Handle verification exception
    } catch (\Exception $exception) {
        // Handle generic error
    }