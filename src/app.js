import axios from 'axios';
import AirReaction from './modules/AirReaction';
import FingerprintJS from '@fingerprintjs/fingerprintjs';

(async () => {
  const settings = {
    requireLogin: window.airReactionsSettings.requireLogin === '1',
    visitorId: null,
    loginRequiredMessage: window.airReactionsSettings.loginRequiredMessage,
  }

  if ( ! settings.requireLogin ) {
    const fp = await FingerprintJS.load();
    const result = await fp.get();
    settings.visitorId = result.visitorId;
  }

	// Update settings
	window.airReactionsApi = {
		...window.airReactionsApi,
		...settings,
	};

  window.addEventListener('initAirReactions', initReactions);

  // Trigger init for the first time
  const event = new Event('initAirReactions');
  window.dispatchEvent(event);
})();

function initReactions() {
  const settings = window.airReactionsApi || false;
  if (! settings) {
    console.warn('Air reactions localized settings missing');
    return;
  }

  const apiUrl = settings.url || false;
  const nonce = settings.nonce || false;

  if (! apiUrl || ! nonce) {
    console.warn('Air reactions API url or nonce missing!');
    return
  }
  const reactionElements = document.querySelectorAll('[data-air-reaction-id]');

  const api = axios.create({
    baseURL: apiUrl,
    headers: {
      'X-WP-Nonce': nonce,
    }
  });

  reactionElements.forEach(reactionElement => {
    // If reaction is not defined, initialize
    if (!('airReaction' in reactionElement)) {
      reactionElement.airReaction = new AirReaction(reactionElement, api, settings);
    }
  });
}
