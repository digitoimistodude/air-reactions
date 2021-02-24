
export default class AirReaction {
  constructor(reactionElement, api) {
    if (! ('airReactionId' in reactionElement.dataset)) {
      return false;
    }
    this.api     = api;
    this.elem    = reactionElement;
    this.id      = reactionElement.dataset.airReactionId;
    this.reacted = 'airReactionReacted' in reactionElement.dataset ? reactionElement.dataset.airReactionReacted : 'false';
    this.count   = 'airReactionCount' in reactionElement.dataset ? reactionElement.dataset.airReactionCount : 0;
    this.items   = this.buildItems();

    this.items.forEach((item) => {
      item.reactionButton.addEventListener('click', () => {
        const elem = document.querySelector(`[data-air-reaction-id="${this.id}"]`);
        if (elem && 'airReaction' in elem) {
          elem.airReaction.toggle(item.reactionType);
        }
      });
    });
  }
  buildItems() {
    const itemElements = [...this.elem.querySelectorAll('[data-air-reaction-item]')];

    const items = [];

    for (let index = 0; index < itemElements.length; index++) {
      const itemElement = itemElements[index];

      if ( ! ('airReactionItem' in itemElement.dataset)) {
        console.warn('"data-air-reaction-item" attribute is missing from reaction-item element');
        continue;
      }

      const button = itemElement.querySelector('button');

      if ( typeof button === 'undefined' ) {
        console.warn('button element is missing from reaction-item element');
        continue;
      }

      items.push({
        reactionType: itemElement.dataset.airReactionItem,
        reactionButton: button,
        elem: itemElement,
      });
    }
    return items;
  }
  toggle(type) {

    const currentItem = this.items.find(item => item.reactionType === type);
    // Disable the button while we handle request
    currentItem.reactionButton.disabled = true;

    const formData = {
      id: this.id,
      type: type,
    };

    this.api.post('/add-reaction', formData)
      .then(() => {
        currentItem.elem.classList.add('air-reactions__item--reacted');
        currentItem.reactionButton.disabled = false;
      })
      .catch((error) => {
        // eslint-disable-next-line no-console
        console.log(error);
        currentItem.reactionButton.disabled = false;
    });
  }
  setLikeAmount() {

  }
}