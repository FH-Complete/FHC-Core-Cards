<?php

class Terminal_model extends DB_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->dbTable = 'extension.tbl_cards_terminal';
		$this->pk = array('cardsterminal_id');
		$this->hasSequence = true;
	}

}