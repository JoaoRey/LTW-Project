<?php
declare(strict_types=1);

class Review
{
    public int $reviewId;
    public int $userId;
    public int $itemId;
    public float $rating;
    public string $comment;
    public string $reviewDate;

    public function __construct(int $reviewId, int $userId, int $itemId, float $rating, string $comment, string $reviewDate)
    {
        $this->reviewId = $reviewId;
        $this->userId = $userId;
        $this->itemId = $itemId;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->reviewDate = $reviewDate;
    }
    
    // Insert a new review
    static function insertReview(PDO $db, int $userId, int $itemId, float $rating, string $comment)
    {
        try {
            $stmt = $db->prepare('
                INSERT INTO Review (UserId, ItemId, Rating, Comment, ReviewDate)
                VALUES (?, ?, ?, ?, datetime("now"))
            ');

            $stmt->execute(array($userId, $itemId, $rating, $comment));

            return $db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error inserting review: " . $e->getMessage());
        }
    }
    //Get all reviews
    static function getReviews(PDO $db): array
    {
        try {
            $stmt = $db->prepare('
                SELECT r.ReviewId, r.UserId, r.ItemId, r.Rating, r.Comment, r.ReviewDate, u.Username
                FROM Review r
                JOIN User u ON r.UserId = u.UserId
            ');

            $stmt->execute();
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $reviewList = array();
            foreach ($reviews as $review) {
                $reviewList[] = new Review(
                    $review['ReviewId'],
                    $review['UserId'],
                    $review['ItemId'],
                    $review['Rating'],
                    $review['Comment'],
                    $review['ReviewDate']
                );
            }

            return $reviewList;
        } catch (PDOException $e) {
            throw new Exception("Error getting reviews: " . $e->getMessage());
        }
    }



    // Get all reviews for a specific item
    static function getReviewsByItemId(PDO $db, int $itemId): array
    {
        try {
            $stmt = $db->prepare('
                SELECT r.ReviewId, r.UserId, r.ItemId, r.Rating, r.Comment, r.ReviewDate, u.Username
                FROM Review r
                JOIN User u ON r.UserId = u.UserId
                WHERE r.ItemId = ?
            ');

            $stmt->execute(array($itemId));
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $reviewList = array();
            foreach ($reviews as $review) {
                $reviewList[] = new Review(
                    $review['ReviewId'],
                    $review['UserId'],
                    $review['ItemId'],
                    $review['Rating'],
                    $review['Comment'],
                    $review['ReviewDate']
                );
            }

            return $reviewList;
        } catch (PDOException $e) {
            throw new Exception("Error getting reviews: " . $e->getMessage());
        }
    }

    //get all reviews received for a specific user
    static function getReviewsReceivedbyId(PDO $db, int $userId): array
    {
        try {
            $stmt = $db->prepare('
                SELECT r.ReviewId, r.UserId, r.ItemId, r.Rating, r.Comment, r.ReviewDate, u.Username
                FROM Review r
                JOIN User u ON r.UserId = u.UserId
                JOIN Item i ON r.ItemId = i.ItemId
                WHERE i.SellerId = ?
            ');

            $stmt->execute(array($userId));
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $reviewList = array();
            foreach ($reviews as $review) {
                $reviewList[] = new Review(
                    $review['ReviewId'],
                    $review['UserId'],
                    $review['ItemId'],
                    $review['Rating'],
                    $review['Comment'],
                    $review['ReviewDate']
                );
            }

            return $reviewList;
        } catch (PDOException $e) {
            throw new Exception("Error getting reviews: " . $e->getMessage());
        }
    }

    //get all reviews made by a specific user
    static function getReviewsMadebyId(PDO $db, int $userId): array
    {
        try {
            $stmt = $db->prepare('
                SELECT r.ReviewId, r.UserId, r.ItemId, r.Rating, r.Comment, r.ReviewDate, u.Username
                FROM Review r
                JOIN User u ON r.UserId = u.UserId
                WHERE r.UserId = ?
            ');

            $stmt->execute(array($userId));
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $reviewList = array();
            foreach ($reviews as $review) {
                $reviewList[] = new Review(
                    $review['ReviewId'],
                    $review['UserId'],
                    $review['ItemId'],
                    $review['Rating'],
                    $review['Comment'],
                    $review['ReviewDate']
                );
            }

            return $reviewList;
        } catch (PDOException $e) {
            throw new Exception("Error getting reviews: " . $e->getMessage());
        }
    }


    // Calculate the average rating received by the user
    static function calculateAverageRating(PDO $db, int $userId): float
    {
        try {
            $stmt = $db->prepare('SELECT AVG(Rating) as averageRating FROM Review r JOIN Item i ON r.ItemId = i.ItemId WHERE i.SellerId = ?');
            $stmt->execute(array($userId));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return floatval($result['averageRating']);
        } catch (PDOException $e) {
            throw new Exception("Error calculating average rating: " . $e->getMessage());
        }
    }
}
?>
