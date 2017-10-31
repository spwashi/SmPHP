/**
 * Created by Sam Washington on 6/8/17.
 */

import Property from "../Property/Property";
import Std from "../../std/Std";

/**
 * An object that is meant to contain information about properties as they exist in this context
 * @class PropertyMetaContainer
 * @extends Std
 */
class PropertyMetaContainer extends Std {
    constructor() {
        super();
        /** @type {Set} Represents the properties that make up the Primary Key */
        this._primaryKey = new Set;
        /** @type {Map} Represents Sets that represent the Unique Keys */
        this._uniqueKeys = new Map;
    }
    
    static get smID() {
        return 'PropertyMetaContainer';
    }
    
    get primary() {
        return this.getPrimaryKeySet();
    }
    
    get unique() {
        return this.getUniqueKeySet();
    }
    
    /**
     * These functions require that a Set of properties is used.
     * @param propertySet
     * @private
     */
    _enforceIsPropertySet(propertySet) {
        if (propertySet instanceof Property) propertySet = new Set([propertySet]);
        
        if (!(propertySet instanceof Set)) throw new Error("Invalid argument Type - must be Property or Set");
        return propertySet;
    }
    
    /**
     *
     * @param {Property}        property
     * @param {Set|Map}         keySet
     * @return {Set|Map|boolean}
     * @private
     */
    _findPropertyKeySet(property, keySet) {
        // default behavior- return the keyset
        if (property === null || typeof property === "undefined") return keySet;
        
        if (keySet instanceof Map) {
            let matchingKeysets = new Map;
            keySet.forEach((set, name, map) => {
                const _keySet = this._findPropertyKeySet(property, set);
                
                if (_keySet instanceof Set) {
                    matchingKeysets.set(name, _keySet);
                }
            });
            if (!matchingKeysets.size) return false;
            return matchingKeysets;
        }
        
        //If we pass in a property, interpret this as "get PrimaryKey where property = ___"
        return keySet.has(property) ? keySet : false;
    }
    
    /**
     * Get the Primary Key (must contain property if it is set)
     * @param property
     * @return {*}
     */
    getPrimaryKeySet(property = null) {
        return this._findPropertyKeySet(property, this._primaryKey);
    }
    
    /**
     * Get an array that corresponds to the Unique Key sets that the property belongs to
     * @param property
     * @return {Array<Set>|Map|bool} Returns the unique key Map if no args are passed, an array of Sets that contain the property, or false
     */
    getUniqueKeySet(property) {
        return this._findPropertyKeySet(property, this._uniqueKeys);
    }
    
    toJSON__primary() {
        return [...this.primary].map(property => property.smID);
    }
    
    toJSON__unique() {
        const unique         = {};
        const selfUniqueKeys = this.unique;
        
        if (!selfUniqueKeys) return null;
        
        selfUniqueKeys.forEach((item, index) => {
            unique[index] = [...item].map(property => property.smID);
        });
        return unique
    }
    
    toJSON() {
        let [primary, unique] = [this.toJSON__primary(), this.toJSON__unique()];
        return {primary, unique}
    }
    
    /**
     * Add what should be a set of properties to a pre-existing set (if that exists)
     * @param keySet
     * @param propertySet
     * @return {Set}
     * @private
     */
    _mergePropertySetWithKeySet(keySet, propertySet) {
        keySet = keySet || [];
        return new Set([...keySet, ...this._enforceIsPropertySet(propertySet)]);
    }
    
    /**
     * Set the properties that are going to act as the Primary Key.
     *
     * @param {Set|Property} propertySet A Property or Set of properties that are going to be used as the Primary Key.
     * @return {PropertyMetaContainer}
     */
    addPropertiesToPrimaryKey(propertySet) {
        this._primaryKey = this._mergePropertySetWithKeySet(this._primaryKey, propertySet);
        return this._primaryKey;
    }
    
    /**
     * Add a Property or a Set of properties to act as a Unique Key under a particular unique key name.
     * The unique key name is used for identification purposes. In instances when this PropertyMetaContainer is being
     * used to configure a TableDataSource, this would be useful in naming those keys.
     *
     * @param {string}  keyName     The name of the Key to add
     * @param {Set|Property}     propertySet The property or Set of properties thar are going to be used as the unique key.
     */
    addPropertiesToUniqueKey(keyName, propertySet) {
        // Add the Set to the others
        const keySet = this._mergePropertySetWithKeySet(this._uniqueKeys.get(keyName),
                                                        propertySet);
        this._uniqueKeys.set(keyName, keySet);
    }
}

export default PropertyMetaContainer;