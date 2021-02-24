import axios from 'axios';
import AirReaction from './modules/AirReaction';

const reactionElements = document.querySelectorAll('[data-air-reaction-id]');
const api = axios.create({
  baseURL: `${window.airReactionsApi.url}`,
  headers: {
		'X-WP-Nonce': window.airReactionsApi.nonce
	}
});

reactionElements.forEach(reactionElement => reactionElement.airReaction = new AirReaction(reactionElement, api));

