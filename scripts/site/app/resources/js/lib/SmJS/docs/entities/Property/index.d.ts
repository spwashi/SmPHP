namespace Sm.entities {
    class Property {

    }

    namespace Property {
        class PropertyHaver {
            constructor(name, config) ;

            /**
             * Get the properties of this Model.
             * @return {Map<string|Symbol, Property>}
             * @constructor
             */
            get properties() ;

            /**
             * Get the Property type that we are going to use
             * @param property_config
             * @return {typeof Property}
             */
            getPropertyType(property_config): typeof Property;

            /**
             * Name properties that we are going to register under this Model.
             * @param original_property_name
             * @return {string}
             * @private
             */
            _getNameForProperty(original_property_name) ;

            /**
             * Add and register a Property, assuring that it is initialized and attached to this class.
             * @param original_property_name
             * @param property_config
             * @private
             * @return {Promise<Property>}
             */
            addProperty(original_property_name, property_config): Promise<Property> ;

            /**
             * Actually register a Property under this Model. Emits the relevant registration events.
             * @protected
             */
            _registerProperty(property: Property): Property;
        }
    }
}