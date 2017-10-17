<?php
namespace MorikenBot\Builder\MessageBuilder;

use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;

const CORRECT_NUMBER = 0;

class ProblemMessageBuilder
{
	private $grade;
	private $times;

	public function __construct($grade,$times){
		$this->grade = $grade;
		$this->times = $times;
	}

	public function build()
	{
		$problem = $this->getProblem($this->grade);
		
		$title = "平成".$problem['employ']."年度 ".$problem['grade']."級 過去問題";
		$sentence = $problem['sentence'];
		$imageUrl = "https://moriken-line-bot.herokuapp.com/img/question_img_02.png";
		$templates = $this->createProblemTemplates($problem['choices']);

		$buttonTemplateBuilder = new ButtonTemplateBuilder(
			$title, $sentence, $imageUrl, $templates
		);

		return new TemplateMessageBuilder($title, $buttonTemplateBuilder);
	}

	public function createProblemTemplates($choices)
	{
		$problemTemplates = [];
		$correct = $choices[CORRECT_NUMBER];

		foreach ($choices as $optionNo => $option) {
			if ($optionNo === CORRECT_NUMBER) {
				continue;
			}
			$problemTemplates[] = $this->createPostbackTemplate($option, $correct);
		}
		$problemTemplates[] = $this->createPostbackTemplate($correct, $correct, 1);

		shuffle($problemTemplates);
		return $problemTemplates;
	}
	
	function createPostbackTemplate($option, $correct, $result=0)
	{
		$postback = 'option='.$option;
		$postback .= '&correct='.$correct;
		$postback .= '&grade='.(string)$this->grade;
		$postback .= '&times='.(string)$this->times;
		$postback .= '&result='.(string)$result;
	
		return new PostbackTemplateActionBuilder($option, $postback);
	}

	function getProblem($grade)
	{
		$gradeParam = "&grade=".(string)$grade;
		$typeParam = "&type=1";
		$itemParam = "&item=1";

		$url = "http://sakumon.jp/app/LK_API/problems/index.json?kentei_id=1&public_flag=1".$gradeParam.$typeParam.$itemParam;

		$json = file_get_contents($url);
		$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
		$arr = json_decode($json,true);

		$problem['employ'] = (string)($arr["response"]["Problems"][0]["Problem"]["employ"] - 1988);
		$problem['grade'] = (string)$arr["response"]["Problems"][0]["Problem"]["grade"];
		$problem['sentence'] = $arr["response"]["Problems"][0]["Problem"]["sentence"];
		$problem['type'] = (int)$arr["response"]["Problems"][0]["Problem"]["type"];

		$problem['choices'][0] 	= $arr["response"]["Problems"][0]["Problem"]["right_answer"];
		$problem['choices'][1] 	= $arr["response"]["Problems"][0]["Problem"]["wrong_answer1"];
		$problem['choices'][2] 	= $arr["response"]["Problems"][0]["Problem"]["wrong_answer2"];
		$problem['choices'][3] 	= $arr["response"]["Problems"][0]["Problem"]["wrong_answer3"];

		return $problem;
	}
}