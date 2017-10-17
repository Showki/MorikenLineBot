<?php
namespace MorikenBot\Builder\MessageBuilder;

use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;

class SelectTimesMessageBuilder
{
	public function __construct($grade){
		$this->grade = $grade;
	}

	public function build()
	{
		$title = "何問解きたいかを選択してください";
		$message = "以下の1問・10問・20問・30級の中から選択してください";
		$imageUrl = "https://moriken-line-bot.herokuapp.com/img/question_img_01.png";
		$templates = $this->getTimesPostbackTemplate();

		$buttonTemplateBuilder = new ButtonTemplateBuilder(
			$title, $message, $imageUrl, $templates
		);

		return new TemplateMessageBuilder($title, $buttonTemplateBuilder);
	}

	public function getTimesPostbackTemplate()
	{
		$templates[] = $this->createTimesPostbackTemplate("1");
		$templates[] = $this->createTimesPostbackTemplate("10");
		$templates[] = $this->createTimesPostbackTemplate("20");
		$templates[] = $this->createTimesPostbackTemplate("30");

		return $templates;
	}

	public function createTimesPostbackTemplate($times)
	{
		$postback = '&selected_times='.$times;
		$postback .= '&selected_grade='.$this->grade;
	
		return new PostbackTemplateActionBuilder($times."問", $postback);
	}
}