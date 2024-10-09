<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/item.class.php');
require_once(__DIR__ . '/../database/payment.class.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = new Session();
$db = getDatabaseConnection();

function generateShippingFormContent(int $itemId, $db): array
{
    $item = Item::getItem($db, $itemId);
    $payment = Payment::getPaymentByItemId($db, $itemId);
    if ($item && $payment) {
        $buyer = User::getUser($db, $payment->buyerId);
        $seller = User::getUser($db, $payment->sellerId);

        return [
            'title' => $item->title,
            'price' => $item->price,
            'description' => $item->description,
            'paymentDate' => $payment->paymentDate,
            'paymentAddress' => $payment->address,
            'paymentPostalCode' => $payment->postalCode,
            'paymentCity' => $payment->city,
            'paymentDistrict' => $payment->district,
            'paymentCountry' => $payment->country,
            'buyerFirstName' => $buyer->firstName,
            'buyerLastName' => $buyer->lastName,
            'buyerUsername' => $buyer->username,
            'buyerEmail' => $buyer->email,
            'sellerFirstName' => $seller->firstName,
            'sellerLastName' => $seller->lastName,
            'sellerUsername' => $seller->username,
            'sellerEmail' => $seller->email,
        ];
    } else {
        return [];
    }
}

if (isset($_POST['item_id'])) {
    $itemId = intval($_POST['item_id']);
    $shippingFormContent = generateShippingFormContent($itemId, $db);
    if (empty($shippingFormContent)) {
        $session->addMessage('error', 'Item or payment not found.');
        header('Location: /pages');
        exit();
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../html/style.css">
        <script src="../javascript/shipping_form.js" defer></script>

        <title>Shipping Form</title>
        
    </head>
    <body>
        <h1>Shipping Form for Item: <?= htmlspecialchars($shippingFormContent['title']); ?></h1>
        <p><strong>Description:</strong> <?= htmlspecialchars($shippingFormContent['description']); ?></p>
        <p><strong>Price:</strong> <?= number_format($shippingFormContent['price'],2); ?></p>
        <p><strong>Payment Date:</strong> <?= htmlspecialchars($shippingFormContent['paymentDate']); ?></p>
        <h2>Shipping Information</h2>
        <p><strong>Address:</strong> <?= htmlspecialchars($shippingFormContent['paymentAddress']); ?></p>
        <p><strong>Postal Code:</strong> <?= htmlspecialchars($shippingFormContent['paymentPostalCode']); ?></p>
        <p><strong>City:</strong> <?= htmlspecialchars($shippingFormContent['paymentCity']); ?></p>
        <p><strong>District:</strong> <?= htmlspecialchars($shippingFormContent['paymentDistrict']); ?></p>
        <p><strong>Country:</strong> <?= htmlspecialchars($shippingFormContent['paymentCountry']); ?></p>
        <h2>Buyer Information</h2>
        <p><strong>Buyer Name:</strong> <?= htmlspecialchars($shippingFormContent['buyerFirstName'] . ' ' . $shippingFormContent['buyerLastName']); ?></p>
        <p><strong>Username:</strong> <?= htmlspecialchars($shippingFormContent['buyerUsername']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($shippingFormContent['buyerEmail']); ?></p>
        <h2>Seller Information</h2>
        <p><strong>Seller Name:</strong> <?= htmlspecialchars($shippingFormContent['sellerFirstName'] . ' ' . $shippingFormContent['sellerLastName']); ?></p>
        <p><strong>Username:</strong> <?= htmlspecialchars($shippingFormContent['sellerUsername']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($shippingFormContent['sellerEmail']); ?></p>
    </body>
    </html>
    <?php
    
} else {
    $session->addMessage('error', 'Invalid Item!');
    header('Location: /pages');
    exit();
}
?>
