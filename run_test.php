<?php
$output = shell_exec('vendor\\bin\\phpunit.bat tests\\Feature\\RadiologyWorkflowAuthorizationTest.php 2>&1');
file_put_contents('test_result.log', $output);
$json = json_decode($output, true);
if (isset($json['tests'])) {
    foreach ($json['tests'] as $t) {
        echo $t['name'] . ": " . $t['status'] . "\n";
        if (!empty($t['message'])) {
            echo "  Message: " . $t['message'] . "\n";
        }
    }
} else {
    echo $output;
}
