<?php
namespace MorikenBot\Builder\MessageBuilder;

use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;

class AnswerMessageBuilder
{
	public function __construct($postback){
		$this->selectedOption = $postback["option"];
		$this->correctOption = $postback["correct"];
		$this->problemGrade = (int)$postback["grade"];
		$this->problemTimes = (int)$postback["times"] - 1;
		$this->isCorrect = (bool) $postback["result"];
	}

	public function build()
	{	
		$msg1 = "『". $this->selectedOption . "』" . " ですね…";
		$msg2 .= $this->isCorrect
			? "正解！"
			: "不正解！\n正解は『".$this->correctOption."』";

		$builder = (new MultiMessageBuilder())->add(
			new TextMessageBuilder($msg1))->add(
				new TextMessageBuilder($msg2));
		
		if($this->problemTimes > 0){
			$title = '次の問題へ進みますか？';
			$templates = $this->getConfirmTemplates();
			$confirmTemplateBuilder = new ConfirmTemplateBuilder($title, $templates);

			$builder->add(
				new TemplateMessageBuilder($title,$confirmTemplateBuilder));
		}else{
			$builder->add(
				new TextMessageBuilder("以上でクイズは終了です。\nお疲れ様でした！"));
		}
		return $builder;
	}

	public function getConfirmTemplates()
	{
		$templates[] = new PostbackTemplateActionBuilder(
			'進む',
			'action=next&selected_grade='.(string)$this->problemGrade.'&selected_times='.(string)$this->problemTimes);
		$templates[] = new PostbackTemplateActionBuilder(
			'やめる', 'action=stop');

		return $templates;
	}
}