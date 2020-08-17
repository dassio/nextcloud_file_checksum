<?php

namespace OCA\FilesChecksum\Settings;

use \OCP\Settings\ISettings;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCA\Activity\CurrentUser;
use OCA\Activity\UserSettings;
use OCP\Activity\IManager;

class Personal implements ISettings {

    /** @var \OCP\IConfig */
	protected $config;

	/** @var IManager */
	protected $manager;

	/** @var \OCA\Activity\UserSettings */
	protected $userSettings;

	/** @var \OCP\IL10N */
	protected $l10n;

	/** @var string */
    protected $user;
    
    /**
    * constructor of the controller
    *
    * @param IConfig $config
    * @param IManager $manager
    * @param UserSettings $userSettings
    * @param IL10N $l10n
    * @param CurrentUser $currentUser
    */
   public function __construct(IConfig $config,
                               IManager $manager,
                               UserSettings $userSettings,
                               IL10N $l10n,
                               CurrentUser $currentUser) {
       $this->config = $config;
       $this->manager = $manager;
       $this->userSettings = $userSettings;
       $this->l10n = $l10n;
       $this->user = (string) $currentUser->getUID();
   }

    /**
	 * @return TemplateResponse
	 */
    public function getForm()
    {
        return new TemplateResponse('files_checksum', 'index',['setting' => 'personal']);
    }

    /**
	 * @return string the section ID, e.g. 'sharing'
	 */
    public function getSection()
    {
        return 'additional';
    }

    /**
	 * @return int whether the form should be rather on the top or bottom of
	 * the admin section. The forms are arranged in ascending order of the
	 * priority values. It is required to return a value between 0 and 100.
	 *
	 * E.g.: 70
	 */
    public function getPriority() {
        return 100;
    }
}
?>
