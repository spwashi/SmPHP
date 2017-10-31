/// <reference path="/docs/global.d.ts" />
namespace Sm {
    namespace entities {
        class DataSource extends Sm.std.Std {
            static _types;

        }

        namespace DataSource {
            /**
             * This is a thing
             */
            interface _config {
                type?: string;
            }

            interface DataSourceHaver {
                Configuration: Sm.entities.ConfiguredEntity.Configuration;
            }
        }
    }
}