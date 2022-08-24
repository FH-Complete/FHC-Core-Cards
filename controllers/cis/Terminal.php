<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


class Terminal extends Auth_Controller
{

	private $_ci; // Code igniter instance

	private $_uid;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(array(
				'index' => 'admin:rw',
				'addTerminal' => 'admin:rw',
				'deleteTerminal' => 'admin:rw',
				'updateTerminal' => 'admin:rw'
			)
		);

		$this->_ci =& get_instance();

		$this->_ci->load->model('extensions/FHC-Core-Cards/Terminal_model', 'TerminalModel');
		$this->_ci->load->library('WidgetLib');
		$this->_ci->loadPhrases(
			array(
				'projektarbeitsbeurteilung',
				'person',
				'global',
				'lehre',
				'filter',
				'ui'
			)
		);

		$this->setControllerId(); // sets the controller id
		$this->_setAuthUID();
	}

	public function index()
	{
		$data[self::FHC_CONTROLLER_ID] = $this->getControllerId();

		$this->_ci->load->view('extensions/FHC-Core-Cards/cis/terminalOverview.php', $data);
	}

	public function addTerminal()
	{
		$name = $this->_ci->input->post('name');
		$beschreibung = $this->_ci->input->post('beschreibung');
		$ort = $this->_ci->input->post('ort');
		$type = $this->_ci->input->post('type');
		$aktiv = $this->_ci->input->post('aktiv');

		if (is_null($name) || is_null($beschreibung) || is_null($ort))
			$this->terminateWithJsonError('Bitte alle Felder ausfüllen!');

		if ($type !== 'student')
			$this->terminateWithJsonError('Kein gültiger Terminaltyp');

		$result = $this->_ci->TerminalModel->insert(
			array(
				'name' => $name,
				'beschreibung' => $beschreibung,
				'aktiv' => $aktiv,
				'ort' => $ort,
				'type' => $type,
				'insertamum' => date('Y-m-d H:i:s'),
				'insertvon' => $this->_uid
			)
		);

		if (isError($result))
			$this->terminateWithJsonError(getError($result));

		if (success($result))
		{
			$this->outputJsonSuccess(array
				(
					'cardsterminal_id' => $result->retval,
					'name' => $name,
					'beschreibung' => $beschreibung,
					'aktiv' => $aktiv,
					'ort' => $ort,
					'type' => $type
				)
			);
		}
	}

	public function deleteTerminal()
	{
		$terminalID = $this->_ci->input->post('id');
		$result = $this->_ci->TerminalModel->delete(array('cardsterminal_id' => $terminalID));

		if (isError($result))
			$this->terminateWithJsonError(getError($result));
		$this->outputJsonSuccess(getData($result));
	}

	public function updateTerminal()
	{
		$terminalID = $this->_ci->input->post('id');
		$name = $this->_ci->input->post('name');
		$beschreibung = $this->_ci->input->post('beschreibung');
		$ort = $this->_ci->input->post('ort');
		$type = $this->_ci->input->post('type');
		$aktiv = $this->_ci->input->post('aktiv');

		if (is_null($name) || is_null($beschreibung) || is_null($ort))
			$this->terminateWithJsonError('Bitte alle Felder ausfüllen!');

		if ($type !== 'student')
			$this->terminateWithJsonError('Kein gültiger Terminaltyp');

		$result = $this->_ci->TerminalModel->update(
			array
			(
				'cardsterminal_id' => $terminalID)
			,
			array
			(
				'name' => $name,
				'beschreibung' => $beschreibung,
				'aktiv' => $aktiv,
				'ort' => $ort,
				'type' => $type,
				'updateamum' => date('Y-m-d H:i:s'),
				'updatevon' => $this->_uid
			)
		);

		$this->outputJson($result);
	}

	/**
	 * Retrieve the UID of the logged user and checks if it is valid
	 */
	private function _setAuthUID()
	{
		$this->_uid = getAuthUID();

		if (!$this->_uid) show_error('User authentification failed');
	}
}