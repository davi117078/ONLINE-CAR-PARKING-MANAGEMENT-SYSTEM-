<?php
function generateBookingQR($booking_code) {
  $outDir = __DIR__ . '/../public/assets/qrs';
  if (!is_dir($outDir)) mkdir($outDir, 0755, true);
  $file = $outDir . '/' . $booking_code . '.png';
  // Simple placeholder QR: image with text. Replace with proper library in production.
  $im = imagecreatetruecolor(300,300);
  $bg = imagecolorallocate($im, 255,255,255);
  $black = imagecolorallocate($im, 0,0,0);
  imagefilledrectangle($im,0,0,300,300,$bg);
  imagestring($im, 3, 10, 20, $booking_code, $black);
  imagepng($im, $file);
  imagedestroy($im);
  return '/ocpms/public/assets/qrs/' . basename($file);
}
