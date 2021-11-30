# Node Autocomplete

## Usage

First ensure the lib is built:

```
$ cd web/themes/quartiers_solidaires/libs/node-autocomplete/
$ yarn && yarn build
```

Then import the `web/themes/quartiers_solidaires/libs/node-autocomplete/build/static/js/main.XXXXXX.js` in your page.

Finally, use the following markup in your page:

```html
<!-- Interactive autocomplete -->
<div
  id="node-autocomplete"
  data-list='[{"id": "ac232e32","name": "Type X", "theme": "my-theme-id"},...]'
  data-target-name="#node-autocomplete-target-name"
  data-target-id="#node-autocomplete-target-id"
  data-value="ac232e32"
  data-placeholder="Type something"
  data-create="Create"
></div>

<!-- Input filled with existing selected ID -->
<input type="text" id="node-autocomplete-target-id" class="sr-only" />

<!-- Input filled with newly created item name -->
<input type="text" id="node-autocomplete-target-name" class="sr-only" />
```

The `data-list` attribute should be a JSON structured like:

```ts
type List = {
  id: string;
  name: string;
  theme?: string;
}[];
```

## Contribute

Just fire the dev server and code in `src/`:

```bash
$ cd web/themes/quartiers_solidaires/libs/node-autocomplete/
$ yarn && yarn start
```
