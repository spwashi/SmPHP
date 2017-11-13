import React from 'react'
import {render} from 'react-dom'
import {createStore,} from "redux";

import reducer from "./rootReducer";
import schematic_depiction from "./scenes/schematic-depiction"
import {ActiveSchemaContainer} from "./scenes/schematic-depiction/components/schemaContainer";
import {Provider} from "react-redux";

const app_elem = document.getElementById('app');
const store    = createStore(reducer);

const app =
          <div>
              <ActiveSchemaContainer onCreateSchemaContainer={schematic_depiction.actions.createSchema} />
          </div>;
render(
    <Provider store={store}>
        {app}
    </Provider>,
    app_elem);