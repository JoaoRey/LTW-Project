<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');

class Communication
{
    public int $communicationId;
    public int $senderId;
    public int $receiverId;
    public int $itemId; // Novo campo adicionado
    public string $messageText;
    public string $sendDate;

    public function __construct(int $communicationId, int $senderId, int $receiverId, int $itemId, string $messageText, string $sendDate)
    {
        $this->communicationId = $communicationId;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->itemId = $itemId;
        $this->messageText = $messageText;
        $this->sendDate = $sendDate;
    }

    // Insert a new communication
    static function insertCommunication(PDO $db, int $senderId, int $receiverId, int $itemId, string $messageText): int
    {
        try {
            $stmt = $db->prepare('
                INSERT INTO Communication (SenderId, ReceiverId, ItemId, CommunicationText, SendDate)
                VALUES (?, ?, ?, ?, datetime("now"))
            ');

            $stmt->execute([$senderId, $receiverId, $itemId, $messageText]);

            return (int)$db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error inserting communication: " . $e->getMessage());
        }
    }



    // Delete a communication
    static function deleteCommunication(PDO $db, int $communicationId): void
    {
        try {
            $stmt = $db->prepare('
                DELETE FROM Communication
                WHERE CommunicationId = ?
            ');

            $stmt->execute(array($communicationId));
        } catch (PDOException $e) {
            throw new Exception("Error deleting communication: " . $e->getMessage());
        }
    }

    // Get all communications sent by a user
    static function getSentCommunications(PDO $db, int $senderId): array
    {
        try {
            $stmt = $db->prepare('
                SELECT *
                FROM Communication
                WHERE SenderId = ?
            ');

            $stmt->execute(array($senderId));

            $communications = array();

            while ($communication = $stmt->fetch()) {
                $communications[] = new Communication(
                    $communication['CommunicationId'],
                    $communication['SenderId'],
                    $communication['ReceiverId'],
                    $communication['ItemId'],
                    $communication['CommunicationText'],
                    $communication['SendDate']
                );
            }

            return $communications;
        } catch (PDOException $e) {
            throw new Exception("Error fetching sent communications: " . $e->getMessage());
        }
    }

    // Get all communications received by a user
    static function getReceivedCommunications(PDO $db, int $receiverId): array
    {
        try {
            $stmt = $db->prepare('
                SELECT *
                FROM Communication
                WHERE ReceiverId = ?
            ');

            $stmt->execute(array($receiverId));

            $communications = array();

            while ($communication = $stmt->fetch()) {
                $communications[] = new Communication(
                    $communication['CommunicationId'],
                    $communication['SenderId'],
                    $communication['ReceiverId'],
                    $communication['ItemId'],
                    $communication['CommunicationText'],
                    $communication['SendDate']
                );
            }

            return $communications;
        } catch (PDOException $e) {
            throw new Exception("Error fetching received communications: " . $e->getMessage());
        }
    }
    // Get

    // Get all communications between two users
    static function getCommunicationsForItem(PDO $db, int $senderId, int $receiverId, int $itemId): array
    {
        try {
            $stmt = $db->prepare('
                SELECT c.*, u.Username AS SenderName
                FROM Communication c
                INNER JOIN User u ON c.SenderId = u.UserId
                WHERE ((c.SenderId = ? AND c.ReceiverId = ?) OR (c.SenderId = ? AND c.ReceiverId = ?))
                AND c.ItemId = ?
            ');
    
            $stmt->execute([$senderId, $receiverId, $receiverId, $senderId, $itemId,]);
    
            $communications = [];
            
            while ($communication = $stmt->fetch()) {
                $communications[] = new Communication(
                    $communication['CommunicationId'],
                    $communication['SenderId'],
                    $communication['ReceiverId'],
                    $communication['ItemId'],
                    $communication['CommunicationText'],
                    $communication['SendDate'],
                    $communication['SenderName']
                    
                );
            }
    
            return $communications;
        } catch (PDOException $e) {
            throw new Exception("Error fetching communications for item: " . $e->getMessage());
        }
    }
    // Adicione este método à classe Communication para obter as mensagens com o nome do remetente
static function getCommunicationsForItemWithSenderName(PDO $db, int $senderId, int $receiverId, int $itemId): array
{
    try {
        $stmt = $db->prepare('
            SELECT c.*, u.Username AS SenderName
            FROM Communication c
            INNER JOIN User u ON c.SenderId = u.UserId
            WHERE ((c.SenderId = ? AND c.ReceiverId = ?) OR (c.SenderId = ? AND c.ReceiverId = ?))
            AND c.ItemId = ?
        ');

        $stmt->execute([$senderId, $receiverId, $receiverId, $senderId, $itemId]);

        $communications = [];

        while ($communication = $stmt->fetch()) {
            $communications[] = [
                'CommunicationId' => $communication['CommunicationId'],
                'SenderId' => $communication['SenderId'],
                'ReceiverId' => $communication['ReceiverId'],
                'ItemId' => $communication['ItemId'],
                'CommunicationText' => $communication['CommunicationText'],
                'SendDate' => $communication['SendDate'],
                'SenderName' => $communication['SenderName'], // Incluindo o nome do remetente
            ];
        }

        return $communications;
    } catch (PDOException $e) {
        throw new Exception("Error fetching communications for item: " . $e->getMessage());
    }
}


    // Save a communication
    static function saveCommunication(PDO $db, int $communicationId, int $senderId, int $receiverId, string $messageText, string $sendDate): void
    {
        try {
            $stmt = $db->prepare('
                UPDATE Communication
                SET SenderId = ?, ReceiverId = ?, CommunicationText = ?, SendDate = ?
                WHERE CommunicationId = ?
            ');

            $stmt->execute(array($senderId, $receiverId, $messageText, $sendDate, $communicationId));
        } catch (PDOException $e) {
            throw new Exception("Error saving communication: " . $e->getMessage());
        }
    }
    // Get all existing chats
// Get all existing chats
static function getAllChats(PDO $db): array
{
    $session = new Session();
    $userId = $session->getId();
    
    try {
        $stmt = $db->prepare('
            SELECT DISTINCT SenderId, ReceiverId, ItemId
            FROM Communication
            WHERE SenderId = :userId OR ReceiverId = :userId
        ');

        $stmt->execute(['userId' => $userId]);

        $chats = [];

        while ($row = $stmt->fetch()) {
            $senderId = $row['SenderId'];
            $receiverId = $row['ReceiverId'];
            $itemId = $row['ItemId'];
            
            // Verifique se o chat já está na lista de chats
            $chatExists = false;
            foreach ($chats as $chat) {
                if (($chat['senderId'] == $senderId && $chat['receiverId'] == $receiverId && $chat['itemId'] == $itemId) ||
                    ($chat['senderId'] == $receiverId && $chat['receiverId'] == $senderId && $chat['itemId'] == $itemId)) {
                    $chatExists = true;
                    break;
                }
            }
            
            // Se o chat não existir na lista, adicione-o
            if (!$chatExists) {
                $chats[] = ['senderId' => $senderId, 'receiverId' => $receiverId, 'itemId' => $itemId];
            }
        }

        return $chats;
    } catch (PDOException $e) {
        throw new Exception("Error fetching all chats: " . $e->getMessage());
    }
}

    
}
?>