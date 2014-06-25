<?php
    class PDOError {
        var $message;

        function PDOError($message = "An Error Has Occurred.") {
            $this->message = $message;
        }
    }
?>