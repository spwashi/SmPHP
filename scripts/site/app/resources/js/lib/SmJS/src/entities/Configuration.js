// @flow
import ConfiguredEntity from "./ConfiguredEntity";

interface;
config;
{

}

/**
 * @class Configuration
 * @name ConfiguredEntity.Configuration
 */
class Configuration {
    /** Array of config objects that we should "pop" items off of to configure the newly set object. */
    configurationQueue: [];
    /** Set the contains the objects used to configure this one */
    configurationHistory: [];
    /** @private */
    _id: string;
    
    constructor(configuredEntity: ?ConfiguredEntity) {
        this.configurationHistory = [];
        this.configurationQueue   = [];
        if (configuredEntity) this.establishOwner(configuredEntity);
    }
    
    /**
     * Get the current configuration
     * @return {{}}
     */
    get current(): Object {
        return Object.assign({}, ...this.configurationHistory);
    }
    
    get first(): Object {
        return this.configurationHistory[0] || null;
    }
    
    get inheritables() {
        return [];
    }
    
    /**
     * Get the thing that we're going to use to identify this configurable entity in the configuration
     * @return {string}
     */
    get identifier() {
        return this._id;
    }
    
    /**
     * Returns an instance of this Configuration type but with the provided condifuration object Queued.
     */
    static create(config: Object): Configuration {
        const c_tor    = this;
        const instance = new c_tor(null);
        instance.configurationQueue.push(config);
        return instance;
    }
    
    /**
     * This sets the owner and gives them all of the configuration has been previously set on this object
     * @param configuredEntity
     * @return {Promise.<*[]>}
     */
    establishOwner(configuredEntity: ConfiguredEntity): Promise<Configuration> {
        this.owner = configuredEntity;
        
        const _configPromises = [];
        for (let i = this.configurationQueue.length; i--;) {
            let config = this.configurationQueue[i];
            _configPromises.push(this.configure(config).catch());
        }
        
        return Promise.all(_configPromises).then(i => this);
    }
    
    configure(config: Object): Promise<Configuration> {
        let config_properties = this.getConfig(config);
        let promises          = [];
        
        // establish the identity of whatever we're configuring
        this._id = this._id || config._id;
        
        // Add the config properties to the "history" to keep track of what was configured/when
        this.configurationHistory.push(config_properties);
        
        for (let cp_name in config_properties) {
            if (!config_properties.hasOwnProperty(cp_name)) continue;
            
            const loopPromise = this._promiseToConfig(cp_name, config_properties);
            promises.push(loopPromise);
        }
        return Promise.all(promises).then(i => this)
    }
    
    getConfig(config: ConfiguredEntity | Configuration | Object) {
        if (config instanceof ConfiguredEntity) config = config.configuration;
        if (config instanceof Configuration) return this._inheritConfig(config);
        
        return Object.assign({}, config);
    }
    
    _inheritConfig(config): {} {
        const inheritables      = this.inheritables;
        const config_properties = {};
        for (let i = 0; i < inheritables.length; i++) {
            const inheritable_item = inheritables[i];
            const currentConfig    = config.current;
            if (inheritable_item in currentConfig) {
                config_properties[inheritable_item] = currentConfig[inheritable_item];
            }
        }
        return config_properties;
    }
    
    /**
     * Look
     * @param config_property_name
     * @param config_properties
     * @return {Promise<*>}
     * @private
     */
    _promiseToConfig(config_property_name: string, config_properties: {}): Promise<*> {
        let config_fn: (config_value: any, configuredEntity: ConfiguredEntity) => {};
        
        const fn_name = 'configure_' + config_property_name;
        config_fn     = (this;
    :
        any;
    )
        [fn_name];
        
        // Push the function's resolution if there is a function;
        let promise = null;
        if (typeof config_fn === 'function') {
            promise = config_fn.apply(this, [config_properties[config_property_name]]);
        }
        
        return Promise.resolve(promise)
    }
}

export default Configuration;