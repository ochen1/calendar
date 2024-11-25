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
	private readonly IL10N $l10n;

	public function __construct(
		private readonly IUserManager $userManager,
		private readonly IURLGenerator $urlGenerator,
		private readonly IActionFactory $actionFactory,
		IFactory $l10nFactory,
	) {
		$this->l10n = $l10nFactory->get(Application::APP_ID);
	}

	public function process(IEntry $entry) {
		$targetUserId = $entry->getProperty('UID');
		$targetUser = $this->userManager->get($targetUserId);
		if ($targetUser === null) {
			// Showing the availability only makes sense for users
			return;
		}

		// TODO: Come up with a better icon!
		//       The recent icon is already used for the local time entry.
		$iconUrl = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath('core', 'actions/recent.svg'));
		$profileActionText = $this->l10n->t('Show availability');

		$action = $this->actionFactory->newJavascriptAction(
			$iconUrl,
			$profileActionText,
			'calendar-availability',
			Application::APP_ID,
		);
		$action->setPriority(-1000);
		$entry->addAction($action);
	}
}
