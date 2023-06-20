<?php
// Register an action that fires after a file is uploaded
add_action('wp_handle_upload', 'handle_upload');

function handle_upload($file){
    // Check if file is jpeg, jpg, png or already a webp
    $file_extension = strtolower(pathinfo($file['file'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, array('jpeg', 'jpg', 'png', 'webp'))) {
        return $file;
    }
    // If the file is already a WebP image, return early
    if ($file_extension == 'webp') {
        return $file;
    }

    $info = getimagesize($file['file']);
    if ($info) {
        $type = $info['mime'];
        if ($type === 'image/jpeg' || $type === 'image/jpg' || $type === 'image/png') {
            $max_dim = 1200;
            $width = $info[0];
            $height = $info[1];

            // Resize if needed
            if ($width > $max_dim || $height > $max_dim) {
                $ratio = $width / $height;
                if ($width > $height) {
                    $new_width = $max_dim;
                    $new_height = $max_dim / $ratio;
                } else {
                    $new_height = $max_dim;
                    $new_width = $max_dim * $ratio;
                }
                
                if ($type === 'image/jpeg' || $type === 'image/jpg') {
                    $src = imagecreatefromjpeg($file['file']);
                } else {
                    $src = imagecreatefrompng($file['file']);
                }
                $dst = imagescale($src, $new_width, $new_height);
                imagedestroy($src);
                imagejpeg($dst, $file['file']);
                imagedestroy($dst);
            }

            // Convert to WebP
            $filename_without_ext = pathinfo($file['file'], PATHINFO_FILENAME);
            $dir = pathinfo($file['file'], PATHINFO_DIRNAME);
            $webP = $dir . '/' . $filename_without_ext . '.webp';
                 
            if ($type === 'image/jpeg' || $type === 'image/jpg') {
                $image = imagecreatefromjpeg($file['file']);
            } else {
                $image = imagecreatefrompng($file['file']);
            }
            imagewebp($image, $webP);
            imagedestroy($image);

            // Delete the original file
            unlink($file['file']);

            // Update file info
            $file['file'] = $webP;
            $file['url'] = str_replace(wp_basename($file['url']), wp_basename($webP), $file['url']);
            $file['type'] = 'image/webp';
        }
    }
    return $file;
}
