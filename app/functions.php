<?php

declare(strict_types=1);

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
