/**
 * Air Reaction
 */
export default class AirReaction {
  constructor(reactionElement, api) {
    if (! ('airReactionId' in reactionElement.dataset)) {
      return false;
    }
    this.reactedClass = 'air-reactions__item--reacted';
    this.api          = api;
    this.elem         = reactionElement;
    this.id           = reactionElement.dataset.airReactionId;
    this.storageKey   = `air-reaction-${this.id}`;
    this.userReaction = 'airReactionUserReaction' in reactionElement.dataset && reactionElement.dataset.airReactionUserReaction !== 'false' ? reactionElement.dataset.airReactionUserReaction : false;
    this.user         = 'airReactionUser' in reactionElement.dataset ? reactionElement.dataset.airReactionUser : 0;
    this.items        = this.buildItems();

    this.items.forEach((item) => {
      item.reactionButton.addEventListener('click', () => {
        const elem = document.querySelector(`[data-air-reaction-id="${this.id}"]`);
        if (elem && 'airReaction' in elem) {

          if ( elem.userReaction === item.reactionType ) {
              // If already reacted, toggle false value
            elem.airReaction.toggle(false);
          } else {
            elem.airReaction.toggle(item.reactionType);
          }
        }
      });
    });
  }
  /**
   * Build items, check that they have all the necessary properties to carry on
   */
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

      const countElement = itemElement.querySelector('[data-air-reaction-count]');

      if ( typeof countElement === 'undefined' ) {
        console.warn('count element is missing or it doesnt have "data-air-reaction-count" attribute');
        continue;
      }

      items.push({
        reactionType: itemElement.dataset.airReactionItem,
        reactionButton: button,
        count: parseInt(countElement.dataset.airReactionCount, 10 ),
        elem: itemElement,
        countElem: countElement,
      });
    }
    return items;
  }
  /**
   * The callback function for reaction button, send the reaction to the API
   *
   * @param {string} type Reaction type
   */
  toggle(type) {

    const currentItem = this.findItem(type);
    // Disable the button while we handle request
    currentItem.reactionButton.disabled = true;
    const formData = {
      id: this.id,
      type: type,
    };

    this.api.post('/add-reaction', formData)
      .then((response) => {
        this.toggleReaction(type);
        if ( 'items' in response.data ) {
          this.updateCount(response.data.items);
        }
        currentItem.reactionButton.disabled = false;
      })
      .catch((error) => {
        // eslint-disable-next-line no-console
        console.log(error);
        currentItem.reactionButton.disabled = false;
    });

  }
  /**
   * Get the cookie from the storage
   */
  getCookie() {
    if (! ('localStorage' in window)) {
      return;
    }
    return localStorage.getItem(this.storageKey);
  }

  /**
   * Save value to cookie
   *
   * @param {mixed} value The value to save to cookie
   */
  setCookie(value) {
    if (! ('localStorage' in window)) {
      return;
    }
    localStorage.setItem(this.storageKey, value);
  }

    /**
   * Remove cookie
   */
  removeCookie(value) {
    if (! ('localStorage' in window)) {
      return;
    }
    localStorage.removeItem(this.storageKey);
  }
  /**
   * Toggle a reaction for the element
   *
   * @param {string} type Reaction type
   */
  toggleReaction(type) {
    let oldReactedItem = false;
    const cookie = this.getCookie();
    // Check if user has reacted on this
    if (this.userReaction) {
      oldReactedItem = this.findItem(this.userReaction);
    } else if (cookie) {
      oldReactedItem = this.findItem(cookie);
    }

    if ( oldReactedItem ) {
      this.removeReaction(oldReactedItem);
    }

    // If reaction type is not false, add a reaction,
    // if it's false, remove reactions from all
    if ( type && this.findItem(type) ) {
      const newReactedItem = this.findItem(type);
      this.addReaction(newReactedItem);
    }

  }

  findItem(type) {
    return this.items.find(item => item.reactionType === type);
  }

  addReaction(item) {
    item.elem.classList.add(this.reactedClass);
    this.userReaction = item.reactionType;
    this.elem.dataset.userReaction = item.reactionType;
    this.setCookie(item.reactionType);
  }

  removeReaction(item) {
    item.elem.classList.remove(this.reactedClass);
    this.userReaction = '';
    this.elem.dataset.userReaction = '';
    this.removeCookie();
  }

  updateCount(items) {
    const itemValues = Object.values(items)
    const itemKeys = Object.keys(items);
    for (let index = 0; index < itemKeys.length; index++) {
      const itemCount = parseInt(itemValues[index]);
      const item = this.findItem(itemKeys[index]);
      item.count = itemCount;
      item.countElem.innerHTML = itemCount;
    }
  }
}