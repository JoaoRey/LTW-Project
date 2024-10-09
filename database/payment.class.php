<?php
declare(strict_types=1);

class Payment
{
    public int $paymentId;
    public int $buyerId;
    public int $sellerId;
    public int $itemId;
    public string $address;
    public string $city;
    public string $district;
    public string $country;
    public string $postalCode;
    public string $paymentDate;

    public function __construct(int $paymentId, int $buyerId, int $sellerId, int $itemId, string $address, string $city, string $district, string $country, string $postalCode, string $paymentDate)
    {
        $this->paymentId = $paymentId;
        $this->buyerId = $buyerId;
        $this->sellerId = $sellerId;
        $this->itemId = $itemId;
        $this->address = $address;
        $this->city = $city;
        $this->district = $district;
        $this->country = $country;
        $this->postalCode = $postalCode;
        $this->paymentDate = $paymentDate;
    }
    

    // Insert a new payment
    static function insertPayment(PDO $db, int $buyerId, int $sellerId, int $itemId, string $address, string $city, string $district, string $country, string $postalCode, string $paymentDate): String
    {
    try {
        $stmt = $db->prepare('
            INSERT INTO Payment (BuyerId, SellerId, ItemId, Address, City, District, Country, PostalCode, PaymentDate)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute(array($buyerId, $sellerId, $itemId, $address, $city, $district, $country, $postalCode, $paymentDate));

        return $db->lastInsertId();
    } catch (PDOException $e) {
        throw new Exception("Error inserting payment: " . $e->getMessage());
    }
}   

    //Get all payments by buyer ID
    static function getPaymentsByBuyerId(PDO $db, int $buyerId): array
    {
        try {
            $stmt = $db->prepare('
                SELECT *
                FROM Payment
                WHERE BuyerId = ?
            ');

            $stmt->execute(array($buyerId));

            $payments = array();

            while ($payment = $stmt->fetch()) {
                $payments[] = new Payment(
                    $payment['PaymentId'],
                    $payment['BuyerId'],
                    $payment['SellerId'],
                    $payment['ItemId'],
                    $payment['Address'],
                    $payment['City'],
                    $payment['District'],
                    $payment['Country'],
                    $payment['PostalCode'],
                    $payment['PaymentDate']
                );
            }

            return $payments;
        } catch (PDOException $e) {
            throw new Exception("Error fetching payments: " . $e->getMessage());
        }
    }
    

    // Get payment by item ID
    static function getPaymentByItemId(PDO $db, int $itemId): ?Payment
    {
        try {
            $stmt = $db->prepare('
                SELECT *
                FROM Payment
                WHERE ItemId = ?
            ');

            $stmt->execute(array($itemId));

            $payment = $stmt->fetch();

            if ($payment) {
                return new Payment(
                    $payment['PaymentId'],
                    $payment['BuyerId'],
                    $payment['SellerId'],
                    $payment['ItemId'],
                    $payment['Address'],
                    $payment['City'],
                    $payment['District'],
                    $payment['Country'],
                    $payment['PostalCode'],
                    $payment['PaymentDate']
                );
            } else {
                return null;
            }
        } catch (PDOException $e) {
            throw new Exception("Error fetching payment: " . $e->getMessage());
        }
    }

    // Get all payments
    static function getAllPayments(PDO $db): array
    {
        try {
            $stmt = $db->query('
                SELECT *
                FROM Payment
            ');

            $payments = array();

            while ($payment = $stmt->fetch()) {
                $payments[] = new Payment(
                    $payment['PaymentId'],
                    $payment['BuyerId'],
                    $payment['SellerId'],
                    $payment['ItemId'],
                    $payment['Address'],
                    $payment['City'],
                    $payment['District'],
                    $payment['Country'],
                    $payment['PostalCode'],
                    $payment['PaymentDate']
                );
            }

            return $payments;
        } catch (PDOException $e) {
            throw new Exception("Error fetching payments: " . $e->getMessage());
        }
    }

    
}
?>
