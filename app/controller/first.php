<?php
class Second extends Controller
{
	function GET($a, $b)
	{
		//var_dump($this->params());
		//var_dump($this->DB());
		echo $a;
		echo $b;
	}
	
	function PUT()
	{
		var_dump($this->params());
	}
	
	function POST()
	{
		var_dump($this->params());
	}
}

class Index extends Controller
{
	function GET()
	{
		echo 123;
	}
}
