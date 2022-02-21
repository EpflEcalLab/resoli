import React, { useEffect, useState } from 'react';
import CreatableSelect from 'react-select/creatable';

export type Props = {
  targetId?: string;
  targetName?: string;
  placeholder?: string;
  create?: string;
  value?: string;
  noPadding?: string;
  noOption?: string;
  list: {
    id: string;
    name: string;
    theme?: string;
  }[]
};

type SelectItem = {
  value: string;
  label: string;
  theme?: string;
};

const App = ({ list, targetId, targetName, placeholder, value, create, noPadding }: Props): JSX.Element => {
  const options: SelectItem[] = list.map(({id, name, theme}) => ({
    label: name,
    value: id,
    theme
  }));
  const defaultValue: SelectItem | null = options.filter(i => i.value === value)[0] ?? null;
  const [selected, setSelected] = useState<SelectItem | null>(defaultValue);
  const [noOption, setNoOption] = useState<string | null>();

  useEffect(() => {
    if (selected !== null) {
      const { label, value, theme } = selected;
      const isFresh = label === value;

      const inputId = document.getElementById(targetId?.replace('#', '') ?? 'node-autocomplete-target-id') as HTMLInputElement;
      if (inputId !== null) inputId.value = isFresh ? '' : value;

      const inputName = document.getElementById(targetName?.replace('#', '') ??'node-autocomplete-target-name') as HTMLInputElement;
      if (inputName !== null) inputName.value = isFresh ? value : '';

      if (theme !== undefined) {
        const themeLabel = document.getElementById(`label-${theme?.replace('#', '')}`) as HTMLLabelElement;
        if (themeLabel !== null) themeLabel.click();
      }
    } else {
      /* Use the value of data-no-option */
      const inputId = document.getElementById(targetId?.replace('#', '') ?? 'node-autocomplete-target-id') as HTMLInputElement;
      setNoOption(inputId.dataset.noOption);
    }
  }, [selected, targetName, targetId, noOption])

  return (
    <div className="text-dark" style={{paddingBottom: noPadding !== 'true' ? 300 : 0}}>
      <CreatableSelect
        defaultValue={selected}
        onChange={setSelected}
        options={options}
        placeholder={placeholder ?? 'Search...'}
        noOptionsMessage={() => (noOption)}
        formatCreateLabel={input => `${create} “${input}”`}
        isClearable
        theme={(theme) => ({
          ...theme,
          borderRadius: 4,
          colors: {
            ...theme.colors,
            primary25: '#83b8fb',
            primary50: '#83b8fb',
            primary75: '#83b8fb',
            primary: '#325ac8',
            danger: '#c80050',
            dangerLight: '#d84c84',
            neutral10: '#f2f2f2',
            neutral20: '#868e96',
            neutral30: '#868e96',
            neutral40: '#868e96',
            neutral50: '#495057',
            neutral60: '#868e96',
            neutral70: '#495057',
            neutral80: '#343a40',
            neutral90: '#292b2c',
          },
        })}
      />
    </div>
  );
}

export default App;
