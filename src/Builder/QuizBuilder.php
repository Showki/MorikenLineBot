<?php
namespace MorikenBot\Builder;

use MorikenBot\Builder\MessageBuilder\ProblemMessageBuilder;

class QuizBuilder
{
	private $grade;
	private $times;

	public function __construct($grade, $times=5)
	{
		$this->grade = $grade;
		$this->times = $times;
	}

	public function build()
	{
		return (new ProblemMessageBuilder(
			$this->grade, $this->times))->build();
	}
}