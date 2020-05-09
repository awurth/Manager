import { bind } from 'lodash-decorators';
import slugify from 'slugify';

export default class Slug {

  private source: HTMLInputElement;
  private target: HTMLInputElement;

  public constructor (source: string|HTMLInputElement, target: string|HTMLInputElement) {
    this.source = source instanceof Element ? source : document.querySelector(source);
    this.target = target instanceof Element ? target : document.querySelector(target);

    this.init();
  }

  private init (): void {
    this.source.addEventListener('keyup', this.onSourceKeyUp);
  }

  @bind
  private onSourceKeyUp (): void {
    this.target.value = slugify(this.source.value, {
      lower: true,
      strict: true
    });
  }

}
