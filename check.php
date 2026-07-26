<?php
$data = json_decode(file_get_contents('output.json'), true);
echo json_encode($data['components']['securitySchemes'] ?? 'NOT FOUND');
echo "\n";
echo json_encode($data['security'] ?? 'NO GLOBAL SECURITY');
