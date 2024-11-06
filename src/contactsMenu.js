/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import 'core-js/stable/index.js'

import '../css/calendar.scss'

import { getRequestToken } from '@nextcloud/auth'
import { linkTo } from '@nextcloud/router'
import { registerContactsMenuHook } from '@nextcloud/vue'

// CSP config for webpack dynamic chunk loading
// eslint-disable-next-line
__webpack_nonce__ = btoa(getRequestToken())

// Correct the root of the app for chunk loading
// OC.linkTo matches the apps folders
// OC.generateUrl ensure the index.php (or not)
// We do not want the index.php since we're loading files
// eslint-disable-next-line
__webpack_public_path__ = linkTo('calendar', 'js/')

registerContactsMenuHook('calendar-availability', async () => {
	const { default: Vue } = await import('vue')
	const { default: ContactsMenuAvailability } = await import('./views/ContactsMenuAvailability.vue')
	const { translate, translatePlural } = await import('@nextcloud/l10n')
	const { default: ClickOutside } = await import('vue-click-outside')
	const { default: VTooltip } = await import('v-tooltip')
	const { default: VueShortKey } = await import('vue-shortkey')
	const { createPinia, PiniaVuePlugin } = await import('pinia')

	Vue.use(PiniaVuePlugin)
	const pinia = createPinia()

	// Register global components
	Vue.directive('ClickOutside', ClickOutside)
	Vue.use(VTooltip)
	Vue.use(VueShortKey, { prevent: ['input', 'textarea'] })

	Vue.prototype.$t = translate
	Vue.prototype.$n = translatePlural

	// The nextcloud-vue package does currently rely on t and n
	Vue.prototype.t = translate
	Vue.prototype.n = translatePlural

	// Append container element to the body to mount the vm at
	const el = document.createElement('div')
	document.body.appendChild(el)

	const View = Vue.extend(ContactsMenuAvailability)
	const vm = new View({
		propsData: {},
		pinia,
	})
	vm.$mount(el)
})
