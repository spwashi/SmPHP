/**
 * Created by Sam Washington on 7/29/17.
 */

import Sm from "../index";

let _convertConfigToMap     = (configured_entity_obj): Map => {
    /** @type {Array} structure the configuration to e a map */
    const map_prepared_obj_array = Object.keys(configured_entity_obj)
                                         .map(key => [key, configured_entity_obj[key]]);
    
    return new Map(map_prepared_obj_array);
};
/**
 * initialize an object (indexed by typical identifier) representing the SmEntity that we are initializing
 * @param configured_entity_obj
 * @param prototype
 * @return {Array}
 */
    export const initialize = (configured_entity_obj: Object, prototype: typeof Sm.std.Std): Array<Promise> => {
    const all = [];
    
    _convertConfigToMap(configured_entity_obj)
        .forEach((ce_config, ce_name: string) => {
            ce_config.name = ce_config.name || ce_name;
            
            // Use the prototype to create an instance of this desired type with its configuration.
            const itemPromise = prototype.init(ce_config)
                                         .catch(i => console.error(i));
            
            all.push(itemPromise);
        });
    return all;
};