// @flow

/**
 * @class DataSource
 * @extends ConfiguredEntity
 */

import SymbolStore from "../../std/symbols/SymbolStore";
import ConfiguredEntity from "../ConfiguredEntity";

/** @type {symbol} Represents the source of some data. (that could be used to retrieve/update/etc maybe) */
const SOURCE = SymbolStore.$_$.item('_source_').Symbol;

/**
 * @alias Sm.entities.DataSource
 */
class DataSource extends ConfiguredEntity {
    static _types;
    static smID = 'DataSource';
    static type = null;
    
    constructor(name, config) {
        super(name, config);
        /**
         * The type of DataSource this is going to be
         * @type {null}
         * @private
         */
        this._type = null;
    }
    
    get type() { return this.constructor.type; }
    
    get jsonFields() {
        return new Set([...super.jsonFields, 'type'])
    }
    
    /**
     * Register a DataSource as a type of DataSource
     * @param {typeof DataSource}   _DataSource
     * @param {string|symbol|null}  identifier If set, this is what will be used to retrieve the DataSource type that  we register
     */
    static registerType(_DataSource, identifier = null) {
        if (!(_DataSource.prototype instanceof DataSource)) throw new TypeError("Configured 'DataSource' is not actually a DataSource");
        
        const id = identifier || _DataSource.type;
        
        if (!id) throw new StdError("Cannot register datasource without a type or identifier");
        
        this._types[id] = _DataSource;
    }
    
    /**
     * Create a DataSource based on some sort of configuration
     *
     * @param {{}|string} _config
     * @return Promise<DataSource>
     */
    static factory(_config: DataSource._config = {}) {
        let configType;
        if (typeof  _config === "string") {
            configType = _config;
            _config    = ({};
        :
            Sm.entities.DataSource._config;
        )
        } else if (typeof _config !== "object") {
            throw new TypeError("Cannot build object with anything other than a string");
        }
        
        configType = configType || _config.type;
        
        if (this._types[configType]) {
            /** @type {typeof DataSource|typeof Std}  */
            const ctor = this._types[configType];
            // name is optional if it is provided in the config (assumed)
            return ctor.init(Object.assign({}, _config))
        }
        
        throw new TypeError(`Cannot build object with this configuration - ${JSON.stringify(_config)}`);
    }
    
    toJSON__type() {
        return this.type;
    }
}

/**
 * symbol to identify the DataSource on an object
 *
 * @type {symbol}
 */
DataSource.SOURCE = SOURCE;
DataSource._types = {};

export default DataSource;
export {SOURCE, DataSource};