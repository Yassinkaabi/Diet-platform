<?php

namespace App\Service;

use App\Entity\User;

class DietRecommendation
{
    public const ACTIVITY_LEVELS = ['Sedentary', 'Lightly active', 'Moderately active', 'Extremely active'];

    public function calculateCaloriesNeeded(User $user): int
    {
        $weight = $user->getWeight();
        $height = $user->getHeight();
        $age = $user->getAge();

        // Harris-Benedict equation
        $caloriesNeeded = 88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age);
        // Adjust based on activity level
        $activityLevel = $user->getActivityLevel();
        $maxActivityLevel = $activityLevel ? max($activityLevel) : null;

        if (!empty($activityLevelArray)) {
            $maxActivityLevel = max($activityLevelArray);
            switch ($maxActivityLevel) {
                case 'Sedentary':
                    $caloriesNeeded *= 1.2;
                    break;
                case 'Lightly active':
                    $caloriesNeeded *= 1.375;
                    break;
                case 'Moderately active':
                    $caloriesNeeded *= 1.55;
                    break;
                case 'Extremely active':
                    $caloriesNeeded *= 1.9;
                    break;
                // Add more cases as needed
                default:
                    // Use a default multiplier if the activity level is unknown
                    $caloriesNeeded *= 1.5;
            }
        }

        // Set the activity level with the calculated value (if needed)
        // $user->setActivityLevel([$caloriesNeeded]);

        return (int) $caloriesNeeded - 141.925;
    }
}
