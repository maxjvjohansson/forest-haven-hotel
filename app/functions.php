<?php

declare(strict_types=1);

use GuzzleHttp\Client;

function validateFeatures(PDO $database, array $features): array
{
    // Convert all feature values to whole numbers and filter away invalid values
    $cleanFeatures = array_map('intval', $features);
    $cleanFeatures = array_filter($cleanFeatures, fn($value) => $value > 0);

    if (!empty($cleanFeatures)) {
        $placeholders = implode(',', array_fill(0, count($cleanFeatures), '?'));
        $statement = $database->prepare("SELECT id FROM features WHERE id IN ($placeholders)");

        $statement->execute($cleanFeatures);
        $validFeatures = $statement->fetchAll(PDO::FETCH_COLUMN);

        // Return only the features that exist in the database
        return array_intersect($cleanFeatures, $validFeatures);
    }

    return []; // Return empty array if features don't exist
}

// Function to check if UUID is valid
function isValidUuid(string $uuid): bool
{
    return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) === 1;
}
