<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cards extends API_Controller
{

	private $_ci; // Code igniter instance

	private $_uid;

	/**
	 * Person API constructor.
	 */
	public function __construct()
	{
		parent::__construct(array(
							'ValidationData' => 'extension/cards:rw',
							'PersonData' => 'extension/cards:rw',
							'CardData' => 'extension/cards:rw',
							'PersonPhoto' => 'extension/cards:rw'
		));

		$this->_ci =& get_instance();
		$this->_ci->load->model('ressource/Betriebsmittelperson_model', 'BetriebsmittelpersonModel');
		$this->_ci->load->model('ressource/Betriebsmittel_model', 'BetriebsmittelModel');
		$this->_ci->load->model('person/Benutzer_model', 'BenutzerModel');
		$this->_ci->load->model('person/Person_model', 'PersonModel');
		$this->_ci->load->model('crm/Konto_model', 'KontoModel');
		$this->_ci->load->model('crm/Akte_model', 'AkteModel');
		$this->_ci->load->model('extensions/FHC-Core-Cards/Card_model', 'CardModel');
		$this->_ci->load->model('person/Fotostatusperson_model', 'FotostatusPersonModel');

		$this->_ci->load->helper('hlp_authentication');
		$this->_ci->load->helper('extensions/FHC-Core-Cards/hlp_cards_common');

		$this->_ci->load->config('extensions/FHC-Core-Cards/cards');

		$this->_setAuthUID();
	}

	/**
	 * @return void
	 */
	public function getValidationData()
	{
		$cardIdentifier = $this->_ci->get('cardIdentifier');

		if (is_null($cardIdentifier))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Fehlerhafte Parameterübergabe'), REST_Controller::HTTP_OK);

		$card = $this->_ci->BetriebsmittelpersonModel->getBetriebsmittelZuordnung($cardIdentifier);

		if (!hasData($card))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Konnte Karte keiner Person zuweisen. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$cardUser = getData($card)[0]->uid;

		$user = $this->_ci->BenutzerModel->load(array('uid' => $cardUser));

		if (!hasData($user))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Die Person kann nicht geladen werden. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$uid = getData($user)[0]->uid;

		$studiengang = $this->_ci->KontoModel->getLastStudienbeitrag($uid, implode("','" , $this->_ci->config->item('BUCHUNGSTYPEN')));

		if (isError($studiengang))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Fehler beim Auslesen des Studienganges. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		if (!hasData($studiengang))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Verlängerung der Karte ist derzeit nicht möglich da der Studienbeitrag noch nicht bezahlt wurde.'), REST_Controller::HTTP_OK);

		$studiensemester_kurzbz = getData($studiengang)[0]->studiensemester_kurzbz;

		$this->_ci->response(array('validdate' => 'gültig bis ' . $studiensemester_kurzbz, 'error' => null), REST_Controller::HTTP_OK);
	}

	/**
	 * @return void
	 */
	public function getPersonData()
	{
		$hash = $this->_ci->get('hash');
		$pin = $this->_ci->get('pin');
		$type = $this->_ci->get('type');

		if (is_null($hash) || is_null($pin) || is_null($type))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Fehlerhafte Parameterübergabe'), REST_Controller::HTTP_OK);

		$user = $this->_ci->CardModel->loadWhere(array('zugangscode' => $hash, 'pin' => $pin));

		if (!hasData($user))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Die Person kann nicht geladen werden. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$uid = getData($user)[0]->uid;

		$person = $this->_ci->PersonModel->getByUid($uid);

		if (!hasData($person))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Die Person kann nicht geladen werden. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$person = getData($person)[0];

		$this->_ci->response(array('uid' => $uid, 'lastname' => $person->nachname, 'firstname' => $person->vorname, 'error' => null), REST_Controller::HTTP_OK);
	}

	public function getPersonPhoto()
	{
		$uid = $this->_ci->get('uid');

		if (is_null($uid))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Fehlerhafte Parameterübergabe'), REST_Controller::HTTP_OK);

		$person = $this->_ci->PersonModel->getByUid($uid);

		if (!hasData($person))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Die Person kann nicht geladen werden. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$person = getData($person)[0];

		$personFoto = $this->_ci->FotostatusPersonModel->getLastFotoStatus($person->person_id);

		if (!hasData($personFoto))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Die Person hat kein Foto. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$personFoto = getData($personFoto)[0];

		if ($personFoto->fotostatus_kurzbz === 'abgewiesen')
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Das Foto wurde abgewiesen. Laden Sie bitte ein gültiges Foto hoch.'), REST_Controller::HTTP_OK);

		if ($personFoto->fotostatus_kurzbz === 'hochgeladen')
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Foto wurde noch nicht akzeptiert.'), REST_Controller::HTTP_OK);

		if ($personFoto->fotostatus_kurzbz === 'akzeptiert')
			$this->_ci->response(array('photo' => $person->foto, 'error' => null), REST_Controller::HTTP_OK);
		else
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Laden Sie bitte ein gültiges Foto hoch.'), REST_Controller::HTTP_OK);

	}
	/**
	 * @return void
	 */
	public function postPersonPhoto()
	{
		$uid = $this->_ci->post('uid');
		$photo = $this->_ci->post('photo');

		if (is_null($uid) || is_null($photo))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Fehlerhafte Parameterübergabe'), REST_Controller::HTTP_OK);

		$person = $this->_ci->PersonModel->getByUid($uid);

		if (!hasData($person))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Die Person kann nicht geladen werden. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$person = getData($person)[0];

		$this->_addPhoto($photo, $person);

		$this->_ci->response(array('result' => true, 'error'=> null), REST_Controller::HTTP_OK);
	}

	/**
	 * @return void
	 */
	public function postCardData()
	{
		$uid = $this->_ci->post('uid');
		$cardData = $this->_ci->post('cardIndetifier');

		if (is_null($uid) || is_null($cardData))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Fehlerhafte Parameterübergabe'), REST_Controller::HTTP_OK);

		$person = $this->_ci->PersonModel->getByUid($uid);

		if (!hasData($person))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Die Person kann nicht geladen werden. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$person = getData($person)[0];

		$insert = $this->_ci->BetriebsmittelModel->insert(
			array(
				'betriebsmitteltyp' => 'Zutrittskarte',
				'nummer' => transform_kartennummer($cardData),
				'nummer2' => $cardData,
				'insertamum' => date('Y-m-d H:i:s'),
				'insertvon' => $this->_uid,
				'updateamum' => date('Y-m-d H:i:s'),
				'updatevon' => $this->_uid,
				'reservieren' => false
			)
		);

		if (isError($insert))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Fehler beim Speichern des Betriebsmittels. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$this->_ci->BetriebsmittelpersonModel->insert(
			array(
				'betriebsmittel_id' => $insert->retval,
				'person_id' => $person->person_id,
				'insertamum' => date('Y-m-d H:i:s'),
				'insertvon' => $this->_uid,
				'ausgegebenam' => date('Y-m-d'),
				'uid' => $this->_uid
			)
		);

		if (isError($insert))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Fehler beim Speichern des Betriebsmittels. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$qrCode = $this->_ci->CardModel->loadWhere(array('uid' => $uid));

		if (hasData($qrCode))
			$this->_ci->CardModel->delete(array('uid' => $uid));

		$this->_ci->response(array('result' => true, 'error' => null), REST_Controller::HTTP_OK);
	}


	/**
	 * Retrieve the UID of the logged user and checks if it is valid
	 */
	private function _setAuthUID()
	{
		$this->_uid = getAuthUID();

		if (!$this->_uid)
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'User authentification failed.'), REST_Controller::HTTP_OK);
	}

	private function _addPhoto($photo, $person)
	{
		$lichtbild = resize($photo, 827, 1063);

		$exists = $this->_ci->AkteModel->getAkten($person->person_id, 'Lichtbil');

		if (isError($exists))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Die Akte kann nicht geladen werden. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$data = array(
			'dokument_kurzbz' => 'Lichtbil',
			'person_id' => $person->person_id,
			'inhalt' => $lichtbild,
			'mimetype' => 'image/jpg',
			'erstelltam' => date('Y-m-d H:i:s'),
			'gedruckt' => false,
			'titel' => 'Lichtbild_' . $person->person_id .'.jpg',
			'bezeichnung' => 'Lichtbild gross',
			'updateamum' => date('Y-m-d H:i:s'),
			'updatevon' => $this->_uid,
			'insertamum' => date('Y-m-d H:i:s'),
			'insertvon' => $this->_uid,
			'uid' => null
		);

		if (hasData($exists))
		{
			$akte = getData($exists);

			$result = $this->_ci->AkteModel->update(
				$akte[0]->akte_id,
				$data
			);

		}
		else
		{
			$result = $this->_ci->AkteModel->insert(
				$data
			);
		}

		if (isError($result))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Fehler beim Speichern der Akte. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$photo = resize($photo, 101, 130);

		$result = $this->_ci->PersonModel->update($person->person_id,
			array(
				'foto' => $photo
			)
		);

		if (isError($result))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Fehler beim Speichern des Fotos. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);

		$insert = $this->_ci->FotostatusPersonModel->insert(
			array(
				'person_id' => $person->person_id,
				'fotostatus_kurzbz' => 'hochgeladen',
				'datum' => date('Y-m-d'),
				'insertamum' => date('Y-m-d H:i:s'),
				'insertvon' => $this->_uid,
				'updateamum' => date('Y-m-d H:i:s'),
				'updatevon' => $this->_uid
			)
		);

		if (isError($insert))
			$this->_ci->response(array('validdate' => 'CUSTOMERROR', 'error' => 'Fehler beim Speichern des Fotostatus. Bitte wenden Sie sich an den Service Desk.'), REST_Controller::HTTP_OK);
	}
}