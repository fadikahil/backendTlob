<?php

namespace App\Services;

class ReceiptService
{
    /**
     * Generate receipt image
     *
     * @param string $userName
     * @param string $itemTitle
     * @param string $organizationName
     * @param string $itemCategory
     * @param string $itemDate
     * @param string $itemLocation
     * @param int $growthScore
     * @return string Path to generated image
     */
    public static function generateReceiptImage(
        string $userName,
        string $itemTitle,
        string $organizationName,
        string $itemCategory,
        string $itemDate,
        string $itemLocation,
        int $growthScore
    ): string {
        // Load the background image template
        $backgroundPath = resource_path('tlobni_certificate/receipt_fixed.png');

        if (!file_exists($backgroundPath)) {
            throw new \Exception("Background image not found at: {$backgroundPath}");
        }

        // Create image from background
        $image = imagecreatefrompng($backgroundPath);

        if (!$image) {
            throw new \Exception("Failed to load background image");
        }

        // Enable anti-aliasing
        imageantialias($image, true);

        // Allocate colors
        $navyBlueColor = imagecolorallocate($image, 15, 35, 66);
        $goldColor = imagecolorallocate($image, 218, 165, 98);
        $whiteColor = imagecolorallocate($image, 255, 255, 255);
        $darkTextColor = imagecolorallocate($image, 40, 40, 40);
        $mediumTextColor = imagecolorallocate($image, 80, 80, 80);

        // Get font paths
        $fontRegular = self::findFont('regular');
        $fontBold = self::findFont('bold');

        if ($fontRegular && $fontBold) {
            self::renderTextOnBackground($image, $userName, $itemTitle, $organizationName,
                $itemCategory, $itemDate, $itemLocation, $growthScore,
                $fontRegular, $fontBold, $navyBlueColor, $darkTextColor,
                $mediumTextColor, $goldColor, $whiteColor);
        } else {
            throw new \Exception("Required fonts not found. Please install TrueType fonts.");
        }

        // Generate unique filename
        $filename = 'receipt_' . time() . '_' . uniqid() . '.png';
        $path = storage_path('app/public/receipts/' . $filename);

        // Create directory if it doesn't exist
        if (!file_exists(storage_path('app/public/receipts'))) {
            mkdir(storage_path('app/public/receipts'), 0755, true);
        }

        // Save image with high quality
        imagepng($image, $path, 9);

        // Free memory
        imagedestroy($image);

        return $path;
    }

    /**
     * Render text overlay on the background image
     */
    private static function renderTextOnBackground(
        $image,
        $userName,
        $itemTitle,
        $organizationName,
        $itemCategory,
        $itemDate,
        $itemLocation,
        $growthScore,
        $fontRegular,
        $fontBold,
        $navyBlueColor,
        $darkTextColor,
        $mediumTextColor,
        $goldColor,
        $whiteColor
    ) {
        // Image is 2340x1655 pixels

        // User name - positioned directly under "THIS OPPORTUNITY IS UNLOCKED ON TLOBNI BY:"
        imagettftext($image, 45, 0, 180, 690, $navyBlueColor, $fontBold, $userName);

        // Item title - positioned below user name (may wrap to multiple lines)
        $wrappedTitle = self::wrapTextForTTF($itemTitle, 22, $fontBold, 1200);
        $titleY = 870;
        $wrappedTitle = [$wrappedTitle[0]];
        foreach ($wrappedTitle as $line) {
            imagettftext($image, 50, 0, 180, $titleY, $navyBlueColor, $fontBold, $line);
            $titleY += 70;
        }

        // Opportunity Maker - aligned after the colon with proper spacing
        imagettftext($image, 32, 0, 600, 1048, $navyBlueColor, $fontBold, $organizationName);

        // Category - aligned after the colon with proper spacing
        imagettftext($image, 32, 0, 400, 1130, $navyBlueColor, $fontBold, $itemCategory);

        // Date - aligned after the colon with proper spacing
        imagettftext($image, 32, 0, 300, 1210, $navyBlueColor, $fontBold, $itemDate);

        // Location - aligned after the colon with proper spacing
        imagettftext($image, 32, 0, 385, 1290, $navyBlueColor, $fontBold, $itemLocation);

        // Growth Score in the badge (right side)
        $scoreText = $growthScore >= 0 ? "+{$growthScore}" : "{$growthScore}";

        // Center position for badge - measured from the badge circle in template
        $badgeCenterX = 2010;
        $badgeCenterY = 675;

        // Get custom font for growth score (Rational TW Display Semi Bold)
        $scoreFont = self::findFont('score');
        if (!$scoreFont) {
            // Fallback to bold font if custom score font not found
            $scoreFont = $fontBold;
        }

        // Score value (large, centered in badge circle)
        $scoreBbox = imagettfbbox(95, 0, $scoreFont, $scoreText);
        $scoreWidth = $scoreBbox[2] - $scoreBbox[0];
        $scoreHeight = $scoreBbox[1] - $scoreBbox[7];

        // Custom gold color for growth score: 0xffe7cca8 (ARGB format)
        $customGoldColor = imagecolorallocate($image, 0xe7, 0xcc, 0xa8);
        imagettftext($image, 83, 0, $badgeCenterX - ($scoreWidth / 2), $badgeCenterY + ($scoreHeight / 3), $customGoldColor, $scoreFont, $scoreText);
    }

    /**
     * Wrap text to fit within a certain width for TTF fonts
     */
    private static function wrapTextForTTF($text, $fontSize, $font, $maxWidth)
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine . ($currentLine ? ' ' : '') . $word;
            $bbox = imagettfbbox($fontSize, 0, $font, $testLine);
            $width = $bbox[2] - $bbox[0];

            if ($width > $maxWidth && $currentLine !== '') {
                $lines[] = $currentLine;
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }

        if ($currentLine) {
            $lines[] = $currentLine;
        }

        return $lines;
    }

    /**
     * Find available font on system
     */
    private static function findFont($type = 'regular')
    {
        $possiblePaths = [];

        if ($type === 'score') {
            // Custom font for growth score (Rational TW Display Semi Bold)
            $possiblePaths = [
                public_path('fonts/RationalTWDisplay-SemiBold.ttf'),
                public_path('fonts/RationalDisplay-SemiBold.ttf'),
                public_path('fonts/rational-tw-display-semibold.ttf'),
                storage_path('fonts/RationalTWDisplay-SemiBold.ttf'),
                resource_path('fonts/RationalTWDisplay-SemiBold.ttf'),
            ];
        } elseif ($type === 'bold') {
            $possiblePaths = [
                public_path('fonts/arialbd.ttf'),
                public_path('fonts/Arial-Bold.ttf'),
                public_path('fonts/Roboto-Bold.ttf'),
                storage_path('fonts/arialbd.ttf'),
                'C:/Windows/Fonts/arialbd.ttf',
                'C:/Windows/Fonts/Arial-Bold.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
                '/System/Library/Fonts/Helvetica.ttc',
            ];
        } else {
            $possiblePaths = [
                public_path('fonts/arial.ttf'),
                public_path('fonts/Arial.ttf'),
                public_path('fonts/Roboto-Regular.ttf'),
                storage_path('fonts/arial.ttf'),
                'C:/Windows/Fonts/arial.ttf',
                'C:/Windows/Fonts/Arial.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
                '/System/Library/Fonts/Helvetica.ttc',
            ];
        }

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }


    /**
     * Get public URL for the receipt
     */
    public static function getReceiptUrl(string $path): string
    {
        $filename = basename($path);
        return asset('storage/receipts/' . $filename);
    }
}
