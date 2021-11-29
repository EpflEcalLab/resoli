import React from 'react';
import ReactDOM from 'react-dom';
import App, { Props } from './App';

const root = document.getElementById('na');
const data = root?.dataset as unknown as Props;

ReactDOM.render(
  <React.StrictMode>
    <App list={JSON.parse(data.list as unknown as string)} />
  </React.StrictMode>,
  root,
);
