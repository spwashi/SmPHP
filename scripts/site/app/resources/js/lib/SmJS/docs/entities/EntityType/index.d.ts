/// <reference path="/docs/global.d.ts" />

namespace Sm.entities {
    import PropertyHaver = Sm.entities.Property.PropertyHaver;

    class EntityType extends ConfiguredEntity, PropertyHaver {
        static init(config: EntityType.entity_type_config);
    }

    namespace EntityType {

        interface entity_type_config extends Sm.entities.ConfiguredEntity._config {
            models: Sm.entities.ConfiguredEntity._config
        }

        interface entity_type_property_config extends Sm.entities.ConfiguredEntity._config {

        }

        class EntityTypeProperty extends Sm.entities.Property {
        }
    }
}