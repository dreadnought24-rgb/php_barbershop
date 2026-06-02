<?php

function sendJsonResponse(array $payload, int $statusCode = 200): void
{
	http_response_code($statusCode);
	header("Content-Type: application/json; charset=UTF-8");
	echo json_encode($payload);
}

function sendJsonSuccess(string $message, array $data = [], int $statusCode = 200): void
{
	$payload = [
		"success" => true,
		"message" => $message,
	];

	if (!empty($data)) {
		$payload["data"] = $data;
	}

	sendJsonResponse($payload, $statusCode);
}

function sendJsonError(string $message, int $statusCode = 400, array $extra = []): void
{
	sendJsonResponse(array_merge([
		"success" => false,
		"message" => $message,
	], $extra), $statusCode);
}

?>
