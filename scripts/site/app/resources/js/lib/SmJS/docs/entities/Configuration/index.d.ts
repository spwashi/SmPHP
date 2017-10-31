/// <reference path="/docs/global.d.ts" />
namespace Sm {
    namespace entities {
        class ConfiguredEntity extends Sm.std.Std {
            constructor(name, config: {});

            _parentSymbols: Set<any>;

            get parentSymbols(): Set<Symbol>;

            get jsonFields(): Array<string>;

            get configuration(): ConfiguredEntity.Configuration;

            initialize(config): Promise<ConfiguredEntity>;

            configure(config: ConfiguredEntity._config);
        }

        namespace ConfiguredEntity {
            interface _config {
                _id?: string
            }

            class Configuration {
                get current(): {};

                static create(config: object);
            }
        }
    }
}