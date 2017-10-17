<?php

namespace MorikenBot\EventHandler;

use MorikenBot\EventHandler;
use MorikenBot\Builder\QuizBuilder;
use MorikenBot\Builder\MessageBuilder\AnswerMessageBuilder;
use MorikenBot\Builder\MessageBuilder\SelectTimesMessageBuilder;

class PostbackEventHandler implements EventHandler
{
	private $bot;
	private $logger;
	private $event;
	private $postback;

	public function __construct($bot, $logger, $event)
	{
		$this->bot = $bot;
		$this->logger = $logger;
		$this->event = $event;
		parse_str($this->event->getPostbackData(), $this->postback);
	}

	public function handle()
	{
		if(!empty($this->postback['action'])) {
			// 次へのボタンが押されてたら出題
			if ($this->postback["action"] == "next") {
				$this->bot->replyMessage(
					$this->event->getReplyToken(),
					(new QuizBuilder(
						$this->postback["selected_grade"],
						$this->postback["selected_times"]))->build());
			}
			// やめるボタンが押されてたら中断
			if ($this->postback["action"] == "stop") {
				$this->bot->replyText(
					$this->event->getReplyToken(), "中断しました！");
			}
		}

		if (!empty($this->postback["selected_times"])) {
			// 級と問題数が選択済みなので、問題テンプレートを返す
			$this->bot->replyMessage(
				$this->event->getReplyToken(),
				(new QuizBuilder(
					$this->postback["selected_grade"],
					$this->postback["selected_times"]))->build());
		} elseif (!empty($this->postback["selected_grade"])) {
			// 級が選択済みなので、回答数選択テンプレートを返す
			$this->bot->replyMessage(
				$this->event->getReplyToken(),
				(new SelectTimesMessageBuilder(
					$this->postback["selected_grade"]))->build());
		} else {
			// 回答結果のテンプレートを返す
			$this->bot->replyMessage(
				$this->event->getReplyToken(),
				(new AnswerMessageBuilder($this->postback))->build());
		}
	}
}
