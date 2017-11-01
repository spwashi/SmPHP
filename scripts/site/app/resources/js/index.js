import Sm from "./lib/SmJS/src/index"
import {app_loader} from "./app/app";
import * as config from "../../config";

app_loader.setBase(__dirname, Sm)
          .then((application: Application) => {
    
              application.createConfigRequireFile()
                         .then(response => {
                             const configModels = config.models
                                 ? config.models(Sm)
                                         .then(models => {
                                             const _sm__config = Sm._config;
                                             console.log(_sm__config);
                                             return _sm__config.initialize(models,
                                                                           Sm.entities.Model);
                                         })
                                 : null;
                             return Promise.resolve(configModels)
                                           .then(modelPromises => Promise.all(modelPromises))
                                           .catch(error => console.log(error));
                         })
                         .then(result => {
                             console.log(JSON.stringify(result, ' ', 3));
                         })
                         .catch(i => {
                             console.error(i);
                         });
    
          });
const entities = Sm.entities;