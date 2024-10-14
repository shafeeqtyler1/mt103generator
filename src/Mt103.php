<?php 

namespace Shafeeqkt\Mt103generator;

class Mt103 {
    private string $outputText = "";
    private string $referenceNumber = "";
    private string $settlementDate = "";
    private string $currencyCode = "USD";
    private string $debitAccountNumber = "";
    private string $senderName = "";
    private string $senderAddress1 = "";
    private string $senderCity = "";
    private string $senderState = "";
    private string $senderZip = "";
    private string $senderCountry = "US";

    private string $swiftBic = "";
    private string $amount = "";
    private string $beneficiaryBankRoutingNumber = "";
    private string $beneficiaryBankName = "";
    private string $beneficiaryBankAddress1 = "";
    private string $beneficiaryBankAddress2 = "";
    private string $beneficiaryAccountNumber = "";
    private string $beneficiaryName = "";
    private string $beneficiaryState = "";
    private string $beneficiaryCity = "";
    private string $beneficiaryZip = "";
    private string $beneficiaryCountry = "";
    private string $beneficiaryAddress = "";


    private string $paymentDescription = "";
    private string $feeType = "OUR";
    private string $bankInstruction = "";
    private string $processingDate = "";
    private string $clearingCode = "FW";
    private string $exchangeRate = "1.000000A"; //Default exchange rate
    private string $senderId = "";
    
    private string $field52d = "";
    private string $field59 = "";

    public function __construct()
    {
        
    }

    public function generateString(): string
    {
        $this->outputText = ":20:{$this->referenceNumber}\n"; // Reference no
        $this->outputText .= ":32A:{$this->processingDate}{$this->settlementDate}{$this->currencyCode}{$this->amount}\n";
        $this->outputText .= ":36:{$this->exchangeRate}\n";
        $this->outputText .= ":50:/{$this->debitAccountNumber}\n";
        $this->outputText .= ":52D:/{$this->senderId}\n{$this->field52d}\n"; // Show sender name in bank statement

        if (!empty($this->swiftBic)) {
            $this->outputText .= ":57A:/{$this->swiftBic}\n";
        } else {
            $this->outputText .= ":57D://{$this->clearingCode}{$this->beneficiaryBankRoutingNumber}\n{$this->beneficiaryBankName}\n{$this->beneficiaryBankAddress1}\n{$this->beneficiaryBankAddress2}\n";
        }

        $this->outputText .= ":59F:/{$this->beneficiaryAccountNumber}\n{$this->field59}\n";
        $this->outputText .= ":70:{$this->paymentDescription}";
        $this->outputText .= ":71A:{$this->feeType}\n";

        if (!empty($this->bankInstruction)) {
            $this->outputText .= ":72:{$this->bankInstruction}\n";
        }

        $this->outputText .= ":END:\n";

        return preg_replace('/^[ \t]*[\r\n]+/m', '', str_replace("\n", "\r\n", $this->outputText)); // Converting LF to CRLF
    }

    public function setReferenceNumber(string $number): self
    {
        $this->referenceNumber = $this->formatText($number, 16);
        return $this;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = number_format($amount, 2, ',', '');
        return $this;
    }

    public function setProcessingDate(string $date = ""): self
    {
        $this->processingDate = date('Ymd', strtotime($date ?: 'now'));
        return $this;
    }

    public function setSettlementDate(string $date = ""): self
    {
        $this->settlementDate = date('Ymd', strtotime($date ?: 'now'));
        return $this;
    }

    public function setCurrencyCode(string $currencyCode = "USD"): self
    {
        $this->currencyCode = strtoupper($currencyCode);
        return $this;
    }

    public function setDebitAccount(string $accNumber): self
    {
        $this->debitAccountNumber = $accNumber;
        return $this;
    }

    public function setBeneficiaryBankDetails(string $accNumber,string $routingNumber, string $bankName, string $address1,string $city = "", string $state = "", string $zip = "", string $country = "US"): self
    {
        $this->beneficiaryAccountNumber = $accNumber;
        $this->beneficiaryBankRoutingNumber = $routingNumber;
        $this->beneficiaryBankName = $this->formatText($bankName, 35);
        $this->beneficiaryBankAddress1 = $this->formatText($address1, 35);
        return $this;
    } 

    public function setBeneficiaryDetails(string $name, string $address1, string $city = "", string $state = "", string $zip = "", string $country = "US"): self
    {
        $this->beneficiaryName = $this->formatText($name, 66);
        $this->beneficiaryAddress = $this->formatText($address1, 33);
        $this->beneficiaryCity = $city;
        $this->beneficiaryState = $state;
        $this->beneficiaryZip = $zip;
        $this->beneficiaryCountry = $country;

        $this->set59Field(); // Prepare field 59
        return $this;
    }

    public function setSwiftCode(string $swift): self
    {
        $this->swiftBic = $swift;
        return $this;
    }

    protected $senderIsIndividual=false;
    public function setSenderInfo(string $sender_name, string $sender_reference = "",bool $is_individual=false ,string $addressLine1, string $city, string $state, string $zip, string $country = "US"): self
    {
        $this->senderIsIndividual   =$is_individual;
        $this->senderId = $this->formatText($sender_reference, 33);
        $this->senderName = $sender_name;
        $this->senderAddress1 = $this->formatText($addressLine1, 35);
        $this->senderCity = $this->formatText($city, 35);
        $this->senderState = $this->formatText($state, 35);
        $this->senderZip = $this->formatText($zip, 10);
        $this->senderCountry = $this->formatText($country, 2);
        $this->set52DField(); // Prepare field 52D

        return $this;
    }   

    public function setDescription(string $description): self
    {   

        $description = $this->formatText($description, 105);
        $description_line = str_split($description,35);
        $this->paymentDescription="";  
        
            foreach($description_line as $i=>$line){
                $this->paymentDescription .=$line."\n";  
            }
        return $this;
    }

    public function setFee(string $feeType = "OUR"): self
    {
        $this->feeType = $feeType;
        return $this;
    }

    public function setBankInstruction(string $instruction): self
    {
        $this->bankInstruction = $instruction;
        return $this;
    }

    private function formatText(string $text, int $maxLength): string
    {
        $text = preg_replace('/[^A-Za-z0-9 -]/', '', $text); // Remove unwanted characters
        return substr($text, 0, $maxLength);
    }

    private function set52DField(): void
    {
       
        $field_52d = '';
        $sender_name = $this->formatText($this->senderName,70);
        $address1 = $this->senderAddress1;
        $city = $this->senderCity;
        $state = $this->senderState;
        $zip_code = $this->senderZip;
        $country = $this->senderCountry;
        $address2 = "$country/$city,$state,$zip_code" ;

        $name_lines = str_split($sender_name, 35);
        $address_lines = str_split($address1, 35);

        $type =$this->senderIsIndividual?'I':"C";
        foreach ($name_lines as $index => $line) {
            $field_52d .= "1/" . ($index == 0 ? "$type/" : "") . $line . "\n";
        }

        foreach ($address_lines as $line) {
            $field_52d .="2/".$line . "\n";
        }
        $field_52d .="3/"."$address2\n";
        $this->field52d=$field_52d;        
    }

    private function set59Field(){


        $field_59 = '';
        $beneficiary_name = $this->beneficiaryName;
        $address_1 = $this->beneficiaryAddress;
        $country = $this->beneficiaryCountry;
        $city = $this->beneficiaryCity;
        $zip = $this->beneficiaryZip;
        $state = $this->beneficiaryState;

        // Split the name and address into lines of maximum 33 characters each
        $name_lines = str_split($beneficiary_name, 33);
        $address_lines = str_split($address_1, 33);

        // if (!empty($address_2)) {
        //     $address_lines = array_merge($address_lines, str_split($address_2, 33));
        // }

        //   if (!empty($address_3)) {
        //     $address_lines = array_merge($address_lines, str_split($address_3, 33));
        //   }

        // Generate the :52D field
        // $field_59 .=$name_lines[0]."\n";

        for ($i = 0; $i < count($name_lines); $i++) {
            $field_59 .="1/". $name_lines[$i] . "\n";
        }

        foreach ($address_lines as $line) {
            $field_59 .="2/".$line . "\n";
        }

        $field_59 .="3/{$country}/$city,$state,$zip\n";
        $this->field59=$field_59;
        return $this;
    }


      // Store transaction data (support multiple transactions)
      private $transactions = [];
       // Add method to add multiple transactions
    public function addTransaction($transaction) {
        $this->transactions[] = $transaction;
        return $this;  // Allow chaining
    }

    // Modify generateString to handle multiple transactions
    public function generateMultipleStrings() {
        $mt103Strings = [];
        
        foreach ($this->transactions as $transaction) {
            // You may use a helper method to set the transaction data
            $this->setTransactionData($transaction);

            // Generate the MT103 message for each transaction
            $mt103Strings[] = $this->generateString();
        }

        return $mt103Strings;
    }


     // Helper method to set the data for each transaction
     private function setTransactionData($transaction) {
        // Here, you map the transaction details to the existing set methods
        $this->setSenderInfo(
                $transaction['sender_name'], 
                $transaction['sender_code'],
                $transaction['sender_code'],
                $transaction['sender_address'], 
                $transaction['sender_city'], 
                $transaction['sender_state'], 
                $transaction['sender_zip'], 
                $transaction['sender_country']

            )           
            ->setBeneficiaryDetails(
                $transaction['beneficiary_name'], 
                $transaction['beneficiary_address'], 
                $transaction['beneficiary_city'], 
                $transaction['beneficiary_state'], 
                $transaction['beneficiary_zip'],
                $transaction['beneficiary_country']

            )
            ->setBeneficiaryBankDetails(
                $transaction['beneficiary_account_number'], 
                $transaction['beneficiary_routing_number'],
                $transaction['beneficiary_bank_name'], 
                $transaction['beneficiary_bank_address']
            )
            ->setDescription($transaction['description'])
            ->setProcessingDate($transaction['processing_date'])
            ->setSettlementDate($transaction['settlement_date']);
    }
}

