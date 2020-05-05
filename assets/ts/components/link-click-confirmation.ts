import { bind } from 'lodash-decorators';

export default class LinkClickConfirmation {

  public constructor () {
    this.init();
  }

  private init (): void {
    document.querySelectorAll('[data-confirm]').forEach(link => {
      link.addEventListener('click', this.onLinkClick);
    });
  }

  @bind
  private onLinkClick (event): void {
    event.preventDefault();

    const link = event.currentTarget;

    if (confirm(link.dataset.confirm)) {
      window.location = link.href;
    }
  }

}
