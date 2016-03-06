<?php
/*--------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
    __      __       _     _____                 _                                  _     __  __      _   _               _
    \ \    / /      | |   |  __ \               | |                                | |   |  \/  |    | | | |             | |
     \ \  / /_ _ ___| |_  | |  | | _____   _____| | ___  _ __  _ __ ___   ___ _ __ | |_  | \  / | ___| |_| |__   ___   __| |
      \ \/ / _` / __| __| | |  | |/ _ \ \ / / _ \ |/ _ \| '_ \| '_ ` _ \ / _ \ '_ \| __| | |\/| |/ _ \ __| '_ \ / _ \ / _` |
       \  / (_| \__ \ |_  | |__| |  __/\ V /  __/ | (_) | |_) | | | | | |  __/ | | | |_  | |  | |  __/ |_| | | | (_) | (_| |
        \/ \__,_|___/\__| |_____/ \___| \_/ \___|_|\___/| .__/|_| |_| |_|\___|_| |_|\__| |_|  |_|\___|\__|_| |_|\___/ \__,_|
                                                        | |                                                                 
                                                        |_| 				
/-------------------------------------------------------------------------------------------------------------------------------/

	@version		1.0.3
	@build			6th March, 2016
	@created		24th February, 2016
	@package		Support Groups
	@subpackage		locations.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 
	
	Support Groups 
                                                             
/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Locations Controller
 */
class SupportgroupsControllerLocations extends JControllerAdmin
{
	protected $text_prefix = 'COM_SUPPORTGROUPS_LOCATIONS';
	/**
	 * Proxy for getModel.
	 * @since	2.5
	 */
	public function getModel($name = 'Location', $prefix = 'SupportgroupsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		
		return $model;
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('location.export', 'com_supportgroups') && $user->authorise('core.export', 'com_supportgroups'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			JArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Locations');
			// get the data to export
			$data = $model->getExportData($pks);
			if (SupportgroupsHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				SupportgroupsHelper::xls($data,'Locations_'.$date->format('jS_F_Y'),'Locations exported ('.$date->format('jS F, Y').')','locations');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_SUPPORTGROUPS_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_supportgroups&view=locations', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('location.import', 'com_supportgroups') && $user->authorise('core.import', 'com_supportgroups'))
		{
			// Get the import model
			$model = $this->getModel('Locations');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (SupportgroupsHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('location_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'locations');
				$session->set('dataType_VDM_IMPORTINTO', 'location');
				// Redirect to import view.
				$message = JText::_('COM_SUPPORTGROUPS_IMPORT_SELECT_FILE_FOR_LOCATIONS');
				$this->setRedirect(JRoute::_('index.php?option=com_supportgroups&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_SUPPORTGROUPS_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_supportgroups&view=locations', false), $message, 'error');
		return;
	} 
}
