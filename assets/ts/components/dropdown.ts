import { bind } from 'lodash-decorators';

export default class Dropdown {

  private readonly selector: string;

  private dropdowns: NodeList;

  public constructor (selector: string) {
    this.selector = selector;

    this.init();
  }

  public closeAll (): void {
    this.dropdowns.forEach((dropdown: Element) => {
      const menu = dropdown.querySelector('.dropdown-menu');
      if (menu) {
        menu.classList.remove('open');
      }
    });
  }

  private init (): void {
    this.dropdowns = document.querySelectorAll(this.selector);

    this.dropdowns.forEach(dropdown => {
      dropdown.addEventListener('click', this.onDropdownClick);
    });

    document.addEventListener('click', this.onDocumentClick);
  }

  @bind
  private onDocumentClick (event: MouseEvent): void {
    const target = event.target as Element;
    if (!target.closest(this.selector)) {
      this.closeAll();
    }
  }

  @bind
  private onDropdownClick (event: MouseEvent): void {
    const dropdown = event.currentTarget as Element;
    const menu = dropdown.querySelector('.dropdown-menu');
    const isOpen = menu && menu.classList.contains('open');

    this.closeAll();

    if (!isOpen) {
      menu.classList.toggle('open');
    }
  }

}
