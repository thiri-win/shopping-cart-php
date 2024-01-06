<?php

if (isset($success_msg)) {
    foreach ($success_msg as $success_msg) {
        // echo '<script>swal("' . $success_msg . '", "", "success")</script>';
        echo '<script>Swal.fire({title: "'.$success_msg.'",text: "'.$success_msg.'",icon: "success"});</script>';
    }
}

if (isset($warning_msg)) {
    foreach ($warning_msg as $warning_msg) {
        echo '<script>Swal.fire({title: "'.$warning_msg.'",text: "'.$warning_msg.'",icon: "warning"});</script>';
    }
}

if (isset($error_msg)) {
    foreach ($error_msg as $error_msg) {
        echo '<script>Swal.fire({title: "'.$error_msg.'",text: "'.$error_msg.'",icon: "error"});</script>';
    }
}

if (isset($info_msg)) {
    foreach ($info_msg as $info_msg) {
        echo '<script>Swal.fire({title: "'.$info_msg.'",text: "'.$info_msg.'",icon: "info"});</script>';
    }
}
