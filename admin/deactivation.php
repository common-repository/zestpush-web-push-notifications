<?php

function zestpush_deactivation() {
    // Perform deactivation work
    delete_option('zest_status');
    
    // Path to WordPress root directory
    $root_directory = ABSPATH;
    $file_path = $root_directory . 'zestpush-worker.js';
    
    // Delete the file if it exists
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            echo 'File deleted successfully';
        } else {
            echo 'There was an error deleting the file';
        }
    } else {
        echo 'File does not exist';
    }
}

?>