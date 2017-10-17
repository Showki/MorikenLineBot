<?php
namespace MorikenBot\Builder\MessageBuilder;

use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;

class SelectGradeMessageBuilder
{
	public function build()
	{
		$title = "解きたい問題の級を選択してください";
		$message = "以下の3級・2級・1級の中から選択してください";
		$imageUrl = "https://moriken-line-bot.herokuapp.com/img/question_img_02.png";
		$templates = $this->getGradePostbackTemplates();

		$buttonTemplateBuilder = new ButtonTemplateBuilder(
			$title, $message, $imageUrl, $templates
		);

		return new TemplateMessageBuilder($title, $buttonTemplateBuilder);
	}

	public function getGradePostbackTemplates()
	{
		$templates[] = $this->createGradePostbackTemplate("1");
		$templates[] = $this->createGradePostbackTemplate("2");
		$templates[] = $this->createGradePostbackTemplate("3");

		return $templates;
	}
	
	public function createGradePostbackTemplate($grade)
	{
		$postback .= '&selected_grade='.$grade;
		return new PostbackTemplateActionBuilder($grade."級", $postback);
	}
}