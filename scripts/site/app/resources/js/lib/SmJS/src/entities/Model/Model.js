import Property from "../Property/Property";
import PropertyMetaContainer from "./PropertyMetaContainer";
import {SOURCE} from "../DataSource/DataSource";
import {DataSourceHaver} from "../DataSource/DataSourceHaver"
import {SymbolStore} from "../../std/symbols/SymbolStore";
import TimeoutError from "../../errors/TimeoutError";
import Configuration from "../Configuration";
import {PropertyHaverConfigurationExtender, PropertyHaverExtender} from "../Property/PropertyHaver";

const ATTRIBUTE = SymbolStore.$_$.item('_attribute_').Symbol;

/**
 * @class ModelConfiguration
 * @mixes PropertyHaverConfiguration
 * @extends Configuration
 *
 */
class ModelConfiguration extends PropertyHaverConfigurationExtender(DataSourceHaver.getConfiguration()) {

}

/**
 * @extends DataSourceHaver
 * @extends PropertyHaver
 */
export default class Model extends PropertyHaverExtender(DataSourceHaver) {
    static Configuration = ModelConfiguration;
    static smID          = 'Model';
    
    constructor(name, config) {
        super(name, config);
        console.log(arguments);
        this._PropertyMetaContainer = new PropertyMetaContainer;
    }
    
    /**
     * @return {PropertyMetaContainer}
     */
    get propertyMeta() {return this._PropertyMetaContainer;}
    
    get jsonFields() {
        return new Set([...super.jsonFields, 'propertyMeta', 'properties'])
    }
    
    toJSON__properties() {
        const properties = {};
        this.properties
            .forEach((property, name) => {
                properties[property.configuration.identifier] = property;
            });
        return properties;
    }
    
    /**
     *
     * @param {Property} property
     * @private
     */
    _attachDataSourceToProperty(property): Promise<Property> {
        return this.resolve(SOURCE)
                   .then(i => {
                       /** @type {DataSource}  */
                       const [, dataSource] = i;
                       const dsn            = dataSource._id || dataSource.smID || null;
                       return property.configure({source: dsn})
                                      .then(i => property);
                   })
                   .catch(i => {
                       const TIMEOUT = SymbolStore.$_$.item('TIMEOUT').Symbol;
                       // Ignore timeouts - if this doesn't have a data source, it doesn't have one
                       if (i instanceof TimeoutError && i.activeSymbol instanceof SymbolStore) {
                           if (i.activeSymbol === this.symbolStore.item(ATTRIBUTE).item(SOURCE)) {
                               return property;
                           }
                       }
                       throw i;
                   });
    }
    
    /**
     * Add and register a Property, assuring that it is initialized and attached to this class.
     * @param original_property_name
     * @param property_config
     * @private
     * @return {Promise<Property>}
     */
    addProperty(original_property_name, property_config): Promise<Property> {
        // The Property
        return super.addProperty(original_property_name, property_config)
                    .then(property => {
                        this._attachDataSourceToProperty(property);
                        return property;
                    });
    }
    
    /**
     * Add the Property to the PropertyMeta to keep track of it.
     *
     * @param {Property} property
     * @private
     */
    _incorporatePropertyIntoMeta(property) {
        const config = property.configuration.current;
        if (config.primary) this._PropertyMetaContainer.addPropertiesToPrimaryKey(property);
        
        let unique = config.unique;
        if (typeof unique !== 'string') unique = !!unique;
        if (unique === true) unique = 'unique_key';
        
        if (unique) this._PropertyMetaContainer.addPropertiesToUniqueKey(unique, property);
        return property;
    }
    
    _registerProperty(property: Property) {
        this._incorporatePropertyIntoMeta(property);
        return super._registerProperty(property);
    }
}