import React from 'react';
import ReactDOM from 'react-dom';
import ReactSelect from 'react-select';
import { bind } from 'lodash-decorators';

export default class Select {

  private readonly element: HTMLSelectElement;

  public constructor (element: string | HTMLSelectElement) {
    this.element = element instanceof HTMLSelectElement ? element : document.querySelector(element);

    if (!this.element) {
      return;
    }

    this.render();
  }

  private getDefaultValue (): Array<any> | object {
    if (this.element.multiple) {
      const selected = [];
      this.element.querySelectorAll('option[selected]').forEach((option: HTMLOptionElement) => {
        selected.push({
          value: option.value,
          label: option.textContent
        });
      });

      return selected;
    }

    const selected = this.element.querySelector('option[selected]') as HTMLOptionElement;

    return selected ? {
      value: selected.value,
      label: selected.textContent
    } : null;
  }

  private getOptions (): Array<any> {
    const options = [];
    this.element.querySelectorAll('option').forEach((option: HTMLOptionElement) => {
      options.push({
        value: option.value,
        label: option.textContent
      });
    });

    return options;
  }

  @bind
  private onChange (value: any): void {
    this.element.querySelectorAll('option').forEach((option: HTMLOptionElement) => {
      option.selected = false;
    });

    if (Array.isArray(value)) {
      value.forEach(selected => {
        const option = this.element.querySelector(`option[value="${selected.value}"]`) as HTMLOptionElement;

        if (option) {
          option.selected = true;
        }
      });
    } else if (value) {
      const option = this.element.querySelector(`option[value="${value.value}"]`) as HTMLOptionElement;

      if (option) {
        option.selected = true;
      }
    }

    const event = document.createEvent('HTMLEvents');
    event.initEvent('change', true, false);

    this.element.dispatchEvent(event);
  }

  private render (): void {
    const options = this.getOptions();

    const LinkTypeChoice = () => (
      <ReactSelect
        defaultValue={this.getDefaultValue()}
        isClearable={!this.element.required}
        isDisabled={this.element.disabled}
        isMulti={this.element.multiple}
        onChange={this.onChange}
        options={options}
        styles={{
          control: provided => ({
            ...provided,
            borderWidth: 2,
            boxShadow: 'none'
          }),
          valueContainer: provided => ({
            ...provided,
            paddingBottom: 4,
            paddingTop: 4
          })
        }}
        theme={theme => ({
          ...theme,
          colors: {
            ...theme.colors,
            primary: '#667eea',
            primary25: '#ebf4ff',
            neutral20: '#cbd5e0',
            neutral30: '#a0aec0',
          }
        })}
      />
    );

    const container = document.createElement('div');

    this.element.style.display = 'none';
    this.element.insertAdjacentElement('afterend', container);

    ReactDOM.render(<LinkTypeChoice/>, container);
  }

}
