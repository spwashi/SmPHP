/**
 * @class Datatype
 * @extends ConfiguredEntity
 */
import ConfiguredEntity from "./ConfiguredEntity";
import {StdError} from "../errors/Error";

export default class Datatype extends ConfiguredEntity {
    static smID = 'Datatype';
    
    /**
     * This identifies the Datatype
     * @return {string}
     */
    get name() {
        return this.configuration.current._id || this.smID;
    }
    
    get jsonFields() {
        return new Set([...super.jsonFields, 'name']);
    }
    
    inherit(item) {
        if (this._hasInheritedOnce) return Promise.reject(new StdError('Can only inherit from one Datatype', this.symbolStore));
        this._hasInheritedOnce = true;
        return super.inherit(item);
    }
}