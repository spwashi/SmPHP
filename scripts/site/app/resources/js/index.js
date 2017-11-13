import Sm from "./lib/SmJS/src"
import {app_loader} from "./_application";
import * as config from "../../config";

app_loader.setBase(__dirname, Sm)
          .then((application: Application) => {
    
              application.createConfigRequireFile()
                         .then(response => {
                             const configModels = config.models
                                 ? config.models(Sm)
                                         .then(models => {
                                             const _sm__config = Sm._config;
                                             return _sm__config.initialize(models,
                                                                           Sm.entities.Model);
                                         })
                                 : null;
                             return Promise.resolve(configModels)
                                           .then(modelPromises => {
                                               console.log(modelPromises);
                                               return Promise.all(modelPromises)
                                           })
                                           .catch(error => console.log(error));
                         })
                         .then(result => {
                             application.storeEntityConfig(result);
                             application.saveConfig();
                         })
                         .catch(i => {
                             console.error(i);
                         });
    
          });
const entities = Sm.entities;