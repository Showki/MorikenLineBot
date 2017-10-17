<?php

namespace MorikenBot\EventHandler;

use MorikenBot\EventHandler;
use MorikenBot\Builder\QuizBuilder;
use MorikenBot\Builder\MessageBuilder\SelectGradeMessageBuilder;

class MessageEventHandler implements EventHandler
{
    private $bot;
    private $logger;
    private $event;

    public function __construct($bot, $logger, $event)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->event = $event;
    }

	public function handle()
	{
		$message = $this->event->getText();
		// リッチメニューの押下判定
		if (preg_match("/^>/", $message)) {
			$grade = $this->getProblemGrade($message);
			// 級ボタンを押下
			if (($grade === 1) or ($grade === 2) or ($grade === 3)) {
				$this->bot->replyMessage(
					$this->event->getReplyToken(),
					(new QuizBuilder($grade))->build());
			}
			// 級・問題数の選択ボタンを押下
			if ($grade === 0) {
				$this->bot->replyMessage(
					$this->event->getReplyToken(),
					(new SelectGradeMessageBuilder())->build());
			}
			// リッチメニューを押下せずに不正な級数を入力した場合
			if ($grade === null) {
				$this->bot->replyText(
					$this->event->getReplyToken(),
					"不正な入力です！");
			}
		}
	}

	public function getProblemGrade($message)
	{
		$grade = "";
		if (preg_match("/1級/",$message)){
			$grade = 1;
		} elseif (preg_match("/2級/",$message)){
			$grade = 2;
		} elseif (preg_match("/3級/",$message)){
			$grade = 3;
		} elseif (preg_match("/指定/",$message)){
			$grade = 0;
		} else {
			$grade = null;
		}
		return $grade;
	}
}