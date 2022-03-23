<?php

class Card_model extends DB_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->dbTable = 'extension.tbl_cards_print';
		$this->pk = array();
		$this->hasSequence = false;
	}

}