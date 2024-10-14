<?php 
use Shafeeqkt\Mt103generator\Mt103;
use PHPUnit\Framework\TestCase;
class Mt103Test extends TestCase{
    public function testGenerateFile()
    {
        // Create an instance of the generator
        $generator = new Mt103();
        $generator->setReferenceNumber(time()) //set unique company reference
            ->setSenderInfo('Your Company Inc','1002',false,'1234 Elm St', 'Springfield', 'IL', '62704',"US")       
            ->setDebitAccount('1456566546556') //set your debit account eg: FBO Debit
            ->setBeneficiaryDetails('John Doe', '4321 Oak St', 'Hometown', 'CA', '90210','US')
            ->setBeneficiaryBankDetails('6545321322','123456789', 'Bank of America', '5678 Maple St', '')            
            ->setDescription('Payment for services')
            ->setProcessingDate("2024-10-15")
            ->setSettlementDate("2024-10-15");
            


        // Generate the file content
        $fileContent = $generator->generateString();
   
        // Assert that the file content is not empty
        $this->assertNotEmpty($fileContent);

        // Add more assertions to validate the content format
        $this->assertStringContainsString('Your Company Inc', $fileContent);
        $this->assertStringContainsString('John Doe', $fileContent);
        $this->assertStringContainsString('Payment for services' , $fileContent);

        echo "\n\n\n\n";
        echo($fileContent);
    }
}

