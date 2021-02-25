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

  const reactionElements = document.querySelectorAll('[data-air-reaction-id]');
  const api = axios.create({
    baseURL: `${window.airReactionsApi.url}`,
    headers: {
      'X-WP-Nonce': window.airReactionsApi.nonce
    }
  });

  reactionElements.forEach(reactionElement => reactionElement.airReaction = new AirReaction(reactionElement, api, settings));
})();
