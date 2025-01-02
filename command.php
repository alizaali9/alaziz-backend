<?php

// Define the path to your Laravel project

$projectPath = '/home4/alazizin/app.alazizinstitute.com/';



// Change the working directory to the Laravel project path

chdir($projectPath);



// Run the Artisan command

$output = [];

$returnVar = 0;

// exec('php artisan db:seed --class=SuperAdminSeeder', $output, $returnVar);
// exec('php artisan make:migration add_order_to_course_parts_table --table=course_parts', $output, $returnVar);

exec('php artisan migrate', $output, $returnVar);
// exec('php artisan migrate:fresh', $output, $returnVar);



// Output the results

echo "Output:\n";

echo implode("\n", $output);

echo "\nReturn Code: $returnVar\n";

?>

