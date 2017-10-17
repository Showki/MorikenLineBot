<?php

namespace MorikenBot;

use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Event\MessageEvent\TextMessage as MessageEvent;
use LINE\LINEBot\Event\PostbackEvent;
use MorikenBot\EventHandler\PostbackEventHandler;
use MorikenBot\EventHandler\MessageEventHandler;

class Route
{
	public function register(\Slim\App $app)
	{
		$app->post('/', function (\Slim\Http\Request $req, \Slim\Http\Response $res) {
			$bot = $this->bot;
			$logger = $this->logger;

			$signature = $req->getHeader(HTTPHeader::LINE_SIGNATURE);
			if (empty($signature)) {
				$logger->info('Signature is missing');
				return $res->withStatus(400, 'Bad Request');
			}

			try {
				$events = $bot->parseEventRequest($req->getBody(), $signature[0]);
			} catch (InvalidSignatureException $e) {
				$logger->info('Invalid signature');
				return $res->withStatus(400, 'Invalid signature');
			} catch (InvalidEventRequestException $e) {
				return $res->withStatus(400, "Invalid event request");
			}

			foreach ($events as $event) {
				$handler = null;

				if ($event instanceof MessageEvent) {
					$handler = new MessageEventHandler($bot, $logger, $event);
				} elseif ($event instanceof PostbackEvent) {
					$handler = new PostbackEventHandler($bot, $logger, $event);
				} 
				$handler->handle();
			}
			$res->write('OK');
			return $res;
		});
	}
}