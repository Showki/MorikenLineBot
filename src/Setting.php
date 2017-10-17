<?php

namespace MorikenBot;

class Setting
{
	static function getSetting()
	{
		return [
			'settings' => [
				'displayErrorDetails' => true, // set to false in production
				'addContentLengthHeader' => false, // Allow the web server to send the content-length header
		
				// Monolog settings
				'logger' => [
					'name' => 'slim-app',
					'path' => __DIR__ . '/../logs/app.log',
					'level' => \Monolog\Logger::DEBUG,
				],

				// Line bot settings
				'bot' => [
					'channelToken' => getenv('CHANNEL_ACCESS_TOKEN'),
					'channelSecret' => getenv('CHANNEL_SECRET'),
				],

				// 'apiEndpointBase' => getenv('LINEBOT_API_ENDPOINT_BASE'),
			],
		];
	}
}