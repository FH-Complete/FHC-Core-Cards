<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

use \chillerlan\QRCode\QROptions;
use \chillerlan\QRCode\QRCode;

class Cards extends Auth_Controller
{

	private $_ci; // Code igniter instance

	private $_uid;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(array(
			'cardValidation' => 'admin:rw',
			'cardLocking' => 'admin:rw',
			'getValidationData' => 'admin:rw',
			'searchPerson' => 'admin:rw',
			'getCards' => 'admin:rw',
			'cardCreation' => 'extension/student_cards:rw',
			'getQRCode' => 'extension/student_cards:rw',
			'downloadQRCode' => 'extension/student_cards:rw'
			)
		);

		$this->_ci =& get_instance();
		$this->_ci->load->model('ressource/Betriebsmittelperson_model', 'BetriebsmittelpersonModel');
		$this->_ci->load->model('person/Benutzer_model', 'BenutzerModel');
		$this->_ci->load->model('crm/Konto_model', 'KontoModel');
		$this->_ci->load->model('crm/Student_model', 'StudentModel');
		$this->_ci->load->model('organisation/Studiengang_model', 'StudiengangModel');
		$this->_ci->load->model('extensions/FHC-Core-Cards/Card_model', 'CardModel');
		$this->_ci->load->model('organisation/Studiensemester_model', 'StudiensemesterModel');

		$this->_ci->load->library('DocumentLib');

		$this->_ci->load->config('extensions/FHC-Core-Cards/cards');


		$this->_setAuthUID();

	}

	public function cardCreation()
	{
		$this->_ci->load->view('extensions/FHC-Core-Cards/cis/cardCreation');
	}

	public function cardValidation()
	{
		$this->_ci->load->view('extensions/FHC-Core-Cards/cis/cardValidation');
	}

	public function getValidationData()
	{
		$cardIdentifier = $this->_ci->input->post('cardIdentifier');

		if (empty($cardIdentifier))
			$this->terminateWithJsonError('Bitte eine Kartennummer angeben');

		$card = $this->_ci->BetriebsmittelpersonModel->getBetriebsmittelZuordnung($cardIdentifier);

		if (!hasData($card))
			$this->terminateWithJsonError('Konnte Karte keiner Person zuweisen.');

		$cardUser = getData($card)[0]->uid;

		$user = $this->_ci->BenutzerModel->load(array('uid' => $cardUser));

		if (!hasData($user))
			$this->terminateWithJsonError('Die Person kann nicht geladen werden.');

		$uid = getData($user)[0]->uid;

		$bezaehlteStudiengaenge = $this->_ci->KontoModel->getStudienbeitraege($uid, implode("','" , $this->_ci->config->item('BUCHUNGSTYPEN')));

		if (isError($bezaehlteStudiengaenge))
			$this->terminateWithJsonError('Fehler beim Auslesen des Studienganges.');

		if (!hasData($bezaehlteStudiengaenge))
			$this->terminateWithJsonError('Verlängerung der Karte ist derzeit nicht möglich da der Studienbeitrag noch nicht bezahlt wurde.');

		$bezaehlteStudiengaenge = getData($bezaehlteStudiengaenge);
		$lastStudienbeitrag = $bezaehlteStudiengaenge[0];

		$aktSemester = $this->_ci->StudiensemesterModel->getAktOrNextSemester();

		if (!hasData($aktSemester))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Fehler beim Auslesen des Studienganges. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$aktSemester = getData($aktSemester)[0];

		if (in_array($aktSemester->studiensemester_kurzbz, array_column($bezaehlteStudiengaenge, 'studiensemester_kurzbz')))
			$semester = $aktSemester->studiensemester_kurzbz;
		else
			$semester = $lastStudienbeitrag->studiensemester_kurzbz;

		$this->outputJsonSuccess($semester);
	}

	public function cardLocking()
	{
		$betriebsmittel_id = $this->_ci->input->post('betriebsmittelid');
		$anmerkung = $this->_ci->input->post('anmerkung');
		$uid = $this->_ci->input->post('uid');

		if (empty($betriebsmittel_id))
			$this->terminateWithJsonError('Bitte eine Kartennummer angeben');

		$betriebsmittel = $this->_ci->BetriebsmittelpersonModel->loadWhere(array('betriebsmittel_id' => $betriebsmittel_id));

		if (isError($betriebsmittel))
			$this->terminateWithJsonError(getError($betriebsmittel));

		if (!hasData($betriebsmittel))
			$this->terminateWithJsonError('Zutrittskarte nicht gefunden');

		$betriebsmittel = getData($betriebsmittel)[0];

		if ($betriebsmittel->uid === $uid)
		{
			$update = $this->_ci->BetriebsmittelpersonModel->update(
				array
				(
					'betriebsmittel_id' => $betriebsmittel->betriebsmittel_id
				),
				array
				(
					'anmerkung' => $anmerkung,
					'retouram' => date('Y-m-d'),
					'updateamum' => date('Y-m-d H:i:s'),
					'updatevon' => $this->_uid
				)
			);

			if (isError($update))
				$this->terminateWithJsonError(getError($update));

			$this->outputJsonSuccess('Success');
		}
		else
			$this->terminateWithJsonError('Fehler beim Sperren der Karte');
	}

	public function getCards()
	{
		$uid = $this->_ci->input->post('uid');

		if (empty($uid))
			$this->terminateWithJsonError('Bitte eine Person auswählen');

		$this->_ci->BetriebsmittelpersonModel->addSelect('wawi.tbl_betriebsmittelperson.anmerkung, ausgegebenam, betriebsmittel_id, retouram');
		$card = $this->_ci->BetriebsmittelpersonModel->getBetriebsmittelByUid($uid, 'Zutrittskarte');

		if (!hasData($card))
			$this->terminateWithJsonError('Die Person hat keine Zutrittskarte.');

		$this->outputJsonSuccess(getData($card));
	}

	public function getQRCode()
	{
		$student = $this->_ci->StudentModel->loadWhere(array('student_uid' => $this->_uid));

		if (!hasData($student))
			$this->terminateWithJsonError('Der Student kann nicht geladen werden.');

		$studiengang = $this->_ci->StudiengangModel->loadWhere(array('studiengang_kz' => getData($student)[0]->studiengang_kz));

		if (!hasData($studiengang))
			$this->terminateWithJsonError('Der Studiengang kann nicht geladen werden.');

		$studiengangTyp = getData($studiengang)[0]->typ;

		if ($studiengangTyp !== 'm' && $studiengangTyp !== 'b')
			$this->terminateWithJsonError('Sie sind nicht berechtigt.');

		$cards = $this->_ci->BetriebsmittelpersonModel->getBetriebsmittelByUid($this->_uid, 'Zutrittskarte');

		if (hasData($cards))
			$this->terminateWithJsonError('Sie haben bereits eine Zutrittskarte');

		$result = $this->_ci->CardModel->loadWhere(array('uid' => $this->_uid));

		$options = new QROptions([
			'outputType' => QRCode::OUTPUT_MARKUP_SVG,
			'addQuietzone' => true,
			'quietzoneSize' => 1,
			'scale' => 8,
			'eccLevel' => 0b10
		]);

		$qrcode = new QRCode($options);

		if (hasData($result))
		{
			$result = getData($result)[0];

			$hash = $result->zugangscode;

			$pin = $result->pin;
			$this->outputJsonSuccess(array('svg' => $qrcode->render($hash), 'pin' => $pin));
		}
		else
		{
			do {
				$token = generateToken();
				$hash = hash('md5', $token);
				$check = $this->_ci->CardModel->loadWhere(array('zugangscode' => $hash));
			} while(hasData($check));

			$pin = rand(1000,9999);

			$insert = $this->_ci->CardModel->insert(array('uid' => $this->_uid,
				'zugangscode' => $hash,
				'pin' => $pin,
				'insertamum' => date('Y-m-d H:i:s')));

			if (isError($insert))
				$this->terminateWithJsonError('Fehler beim Speichern');

			$this->outputJsonSuccess(array('svg' => $qrcode->render($hash), 'pin' => $pin));
		}
	}

	public function downloadQRCode()
	{
		$result = $this->_ci->CardModel->loadWhere(array('uid' => $this->_uid));

		if (hasData($result))
		{
			$result = getData($result)[0];

			$hash = $result->zugangscode;

			$tempdir = sys_get_temp_dir().'/QR_Codes';

			if (!file_exists($tempdir))
				mkdir($tempdir, 0777, true);

			$filename = $tempdir . '/' . 'QR_' . uniqid();
			$filenamePng = $filename . '.png';
			$filenamePdf = $filename . '.pdf';

			$options = new QROptions([
				'outputType' => QRCode::OUTPUT_IMAGE_PNG,
				'cachefile' => $filenamePng,
				'addQuietzone' => false,
				'eccLevel' => 0b10
			]);

			$qrcode = new QRCode($options);
			$qrcode->render($hash);

			$this->_ci->documentlib->convert($filenamePng,  $filename, 'pdf');

			$files[] = $filenamePng;
			$files[] = $filenamePdf;

			$fsize = filesize($filenamePdf);
			header('Content-type: application/pdf');
			header('Content-Disposition: attachment; filename="QRCode.pdf"');
			header('Content-Length: '.$fsize);

			echo file_get_contents($filenamePdf);

			foreach ($files as $file)
				unlink($file);
		}
	}

	public function searchPerson()
	{
		$filter = mb_strtolower($this->_ci->input->get('term'));

		$result = $this->_ci->StudentModel->searchStudent($filter);

		if (isSuccess($result))
			$this->outputJson($result->retval);
		else
			$this->outputJson(null);
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