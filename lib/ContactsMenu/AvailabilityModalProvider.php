<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Calendar\ContactsMenu;

use OCA\Calendar\AppInfo\Application;
use OCP\Contacts\ContactsMenu\IActionFactory;
use OCP\Contacts\ContactsMenu\IEntry;
use OCP\Contacts\ContactsMenu\IProvider;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\L10N\IFactory;

class AvailabilityModalProvider implements IProvider {
	private IL10N $l10n;

	public function __construct(
		private IUserManager $userManager,
		private IURLGenerator $urlGenerator,
		private IActionFactory $actionFactory,
		IFactory $l10nFactory,
	) {
		$this->l10n = $l10nFactory->get(Application::APP_ID);
	}

	public function process(IEntry $entry) {
		$targetUserId = $entry->getProperty('UID');
		$targetUser = $this->userManager->get($targetUserId);
		if (!empty($targetUser)) {
			$iconUrl = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath('core', 'actions/recent.svg'));
			$profileActionText = $this->l10n->t('Show availability');

			$action = $this->actionFactory->newJavascriptAction($iconUrl, $profileActionText, 'calendar-availability');
			$action->setPriority(-1000);
			$entry->addAction($action);
		}
	}
}
