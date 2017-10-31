import Configuration from "../Configuration";
import Property from './Property';
import ConfiguredEntity from '../ConfiguredEntity';
import {Sm} from "../../../tests/Sm";

export const PropertyHaverConfigurationExtender = (parent: Configuration): typeof Sm.entities.ConfiguredEntity.Configuration => {
    return class PropertyHaverConfiguration extends parent {
        get inheritables() {
            return [...super.inheritables, 'properties'];
        }
        
        get current() {
            const current                        = super.current;
            const effectivePropertyConfiguration = this._getEffectivePropertyConfiguration();
            if (effectivePropertyConfiguration) current.properties = effectivePropertyConfiguration;
            return current;
        }
        
        /**
         * configure the properties for this Model
         * @param properties_config
         * @return {Promise.<*>}
         * @private
         */
        configure_properties(properties_config) {
            const haver    = this.owner;
            const promises = Object.entries(properties_config)
                                   .map((i) => {
                                       let [property_name, property_config] = i;
                                       // Set the "_id" of the property. This is the name that we use to configure the property initially.
                                       property_config._id                  = property_name;
                                       return haver.addProperty(property_name, property_config);
                                   });
            return Promise.all(promises);
        }
        
        /**
         * Get an object representation of what essentially is the original configuration that was used
         * to configure the properties of this object
         *
         * @return {Object<string, Property>}
         * @private
         */
        _getEffectivePropertyConfiguration() {
            const properties = {};
            this.owner
                .properties
                .forEach((property: Property, name) => {
                    const config                  = property.configuration;
                    properties[config.identifier] = config.current;
                });
            return properties;
        }
    }
};

export const PropertyHaverExtender = (parent: typeof ConfiguredEntity): typeof Sm.entities.Property.PropertyHaver => {
    return class extends parent {
        constructor(name, config) {
            super(name, config);
            this._properties = new Map;
        }
        
        /**
         * Get the properties of this Model.
         * @return {Map<string|Symbol, Property>}
         * @constructor
         */
        get properties() { return this._properties; }
        
        /**
         * Get the Property type that we are going to use
         * @param property_config
         * @return {typeof Property}
         */
        getPropertyType(property_config): typeof Property {
            return Property;
        }
        
        /**
         * Name properties that we are going to register under this Model.
         * @param original_property_name
         * @return {string}
         * @private
         */
        _getNameForProperty(original_property_name) { return `{${this.smID}}${original_property_name}`; }
        
        /**
         * Add and register a Property, assuring that it is initialized and attached to this class.
         * @param original_property_name
         * @param property_config
         * @private
         * @return {Promise<Property>}
         */
        addProperty(original_property_name, property_config): Promise<Property> {
            const property_name = this._getNameForProperty(original_property_name);
            property_config._id = property_config._id || original_property_name;
            
            // The Property
            return this.getPropertyType()
                       .init(property_name, property_config)
                       .then(property => {
                           /** @type {Property} property */
                           if (!(property instanceof Property)) throw new Error('Improperly created property');
                           return property;
                       })
                       .then(property => this._registerProperty(property))
        }
        
        /**
         * Actually register a Property under this Model. Emits the relevant registration events.
         * @protected
         */
        _registerProperty(property: Property): Property {
            this._properties.set(property.smID, property);
            this.registerAttribute(property.configuration.identifier, property);
            return property;
        }
    };
    
};