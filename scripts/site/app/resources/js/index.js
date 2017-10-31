import Sm from "./lib/SmJS/src/index"
import {app_loader} from "./app/app";
import * as config from "../../config";

app_loader.setBase(__dirname, Sm)
          .then((application: Application) => {
    
              application.createConfigRequireFile()
                         .then(response => {
                             const configModels = config.models && Sm._config.initialize(config.models(Sm), Sm.entities.Model);
                             Promise.resolve(configModels);
                         })
                         .then(result => {
                             // console.log(result)
                         });
    
          });
const entities = Sm.entities;