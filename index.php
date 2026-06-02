<?php

header("Content-Type: application/json");

echo json_encode([
	"success" => true,
	"message" => "Barbershop API is running"
]);

?>
