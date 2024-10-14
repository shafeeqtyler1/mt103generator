# MT103 Generator Package

This package provides an easy way to generate MT103 files (SWIFT payment messages) in PHP. It supports generating single and multiple transactions in the MT103 format.

## Installation

You can install the package via Composer:

```bash
composer require shafeeqkt/mt103generator

Usage
1. Generating a Single MT103 File
To generate an MT103 file for a single transaction, follow these steps:

```php


use Shafeeqkt\Mt103generator\Mt103;

$generator = new Mt103();

$generator->setSenderInfo('Your Company Inc.', '1002')
    ->setSenderAddress('1234 Elm St', 'Springfield', 'IL', '62704', 'US')
    ->setBeneficiaryDetails('John Doe', '4321 Oak St', '', 'Hometown', 'CA', '90210')
    ->setBeneficiaryBankDetails('123456789', 'Bank of America', '5678 Maple St', '')
    ->setDescription('Payment for services')
    ->setProcessingDate(date('Y-m-d'))
    ->setSettlementDate(date('Y-m-d'));

// Generate the MT103 string
$fileContent = $generator->generateString();

// Output or save the MT103 message
echo $fileContent;


```


2. Generating Multiple MT103 Files
To handle multiple transactions and generate MT103 messages for each, you can add multiple transactions using the addTransaction method.
```php
use Shafeeqkt\Mt103generator\Mt103;

$generator = new Mt103();

// Prepare an array of transactions
$transactions = [
    [
        'sender_name' => 'Your Company Inc.',
        'sender_code' => '1002',
        'sender_address' => '1234 Elm St',
        'sender_city' => 'Springfield',
        'sender_state' => 'IL',
        'sender_zip' => '62704',
        'sender_country' => 'US',
        'beneficiary_name' => 'John Doe',
        'beneficiary_address' => '4321 Oak St',
        'beneficiary_city' => 'Hometown',
        'beneficiary_state' => 'CA',
        'beneficiary_zip' => '90210',
        'beneficiary_account_number' => '123456789',
        'beneficiary_bank_name' => 'Bank of America',
        'beneficiary_bank_address' => '5678 Maple St',
        'description' => 'Payment for services',
        'processing_date' => date('Y-m-d'),
        'settlement_date' => date('Y-m-d')
    ],
    [
        'sender_name' => 'Your Second Company Inc.',
        'sender_code' => '2003',
        'sender_address' => '5678 Pine St',
        'sender_city' => 'Metropolis',
        'sender_state' => 'NY',
        'sender_zip' => '10001',
        'sender_country' => 'US',
        'beneficiary_name' => 'Jane Smith',
        'beneficiary_address' => '8765 Birch St',
        'beneficiary_city' => 'Smallville',
        'beneficiary_state' => 'TX',
        'beneficiary_zip' => '75001',
        'beneficiary_account_number' => '987654321',
        'beneficiary_bank_name' => 'Wells Fargo',
        'beneficiary_bank_address' => '123 Cedar St',
        'description' => 'Consulting services',
        'processing_date' => date('Y-m-d'),
        'settlement_date' => date('Y-m-d')
    ]
];

// Add each transaction to the generator
foreach ($transactions as $transaction) {
    $generator->addTransaction($transaction);
}

// Generate MT103 messages for all transactions
$mt103Strings = $generator->generateMultipleStrings();

// Output or save each MT103 message
foreach ($mt103Strings as $mt103String) {
    echo $mt103String . "\n\n";
}
```



Available Methods
Single Transaction Methods
setSenderInfo(string $name, string $code)
Sets the sender's name and code.

setSenderAddress(string $address, string $city, string $state, string $zip, string $country)
Sets the sender's address details.

setBeneficiaryDetails(string $name, string $address, string $address2, string $city, string $state, string $zip)
Sets the beneficiary's details.

setBeneficiaryBankDetails(string $accountNumber, string $bankName, string $bankAddress)
Sets the beneficiary's bank details.

setDescription(string $description)
Sets the description for the payment.

setProcessingDate(string $processingDate)
Sets the processing date for the payment.

setSettlementDate(string $settlementDate)
Sets the settlement date for the payment.

generateString()
Generates the MT103 message string for a single transaction.

Multiple Transaction Methods
addTransaction(array $transaction)
Adds a transaction to the generator. The transaction array should contain the same keys used in the single transaction methods.

generateMultipleStrings()
Generates MT103 message strings for all added transactions.



Running Tests
This package comes with unit tests using PHPUnit. You can run the tests with the following command:

```cmd
./vendor/bin/phpunit


```cmd



### Explanation:

1. **Installation Section**: Describes how to install the package using Composer.
2. **Usage Section**: Shows examples for generating single and multiple MT103 messages.
3. **Available Methods Section**: Lists the available methods for single and batch transaction processing.
4. **Testing Section**: Provides instructions for running unit tests using PHPUnit.
5. **License Section**: Specifies that the package is under the MIT License.

This `README.md` should give users a clear idea of how to use the package. You can add more details based on additional features or changes in your package.

