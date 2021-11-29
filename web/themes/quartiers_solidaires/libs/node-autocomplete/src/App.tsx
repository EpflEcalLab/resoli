import React, { useEffect, useState } from 'react';
import CreatableSelect from 'react-select/creatable';

export type Props = {
  list: {
    id: string;
    name: string;
  }[]
};

type SelectItem = {
  value: string;
  label: string;
};

const App = ({ list }: Props): JSX.Element => {
  const options: SelectItem[] = list.map(({id, name}) => ({
    label: name,
    value: id
  }));
  const [selected, setSelected] = useState<SelectItem | null>(null);

  useEffect(() => {
    if (selected !== null) {
      const { label, value } = selected;
      const isFresh = label === value;

      const inputId = document.getElementById('node-autocomplete-target-id') as HTMLInputElement;
      if (inputId !== null) inputId.value = isFresh ? '' : value;

      const inputName = document.getElementById('node-autocomplete-target-name') as HTMLInputElement;
      if (inputName !== null) inputName.value = isFresh ? value : '';
    }
  }, [selected])

  return (
    <CreatableSelect
      defaultValue={selected}
      onChange={setSelected}
      options={options}
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
          neutral20: '#e9ecef',
          neutral30: '#dee2e6',
          neutral40: '#ced4da',
          neutral50: '#adb5bd',
          neutral60: '#868e96',
          neutral70: '#495057',
          neutral80: '#343a40',
          neutral90: '#292b2c',
        },
      })}
    />
  );
}

export default App;
